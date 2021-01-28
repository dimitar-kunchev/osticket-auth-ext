<?php 
require_once(INCLUDE_DIR.'class.auth.php');
require_once(INCLUDE_DIR.'class.staff.php');

class ExternalRESTAuthentication extends StaffAuthenticationBackend {
    static $name = "External Custom Authentication with a REST method";
    static $id = "ext";
    
    protected $config;
    
    function __construct($config) {
        $this->config = $config;
    }
    
    function supportsInteractiveAuthentication() {
        return true;
    }
    
    function authenticate($username, $password) {
        $ch = curl_init( $this->config->get('uri')); 
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( ['email' => $username, 'pass' => $password] ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        
        $user = null;
                
        if (!curl_errno($ch)) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // error_log('Curl http response code: '.$http_code);
            if ($http_code == 200) {
                if (($user = StaffSession::lookup($username)) && $user->getId()) {
                    // error_log('Login OK, user id ' . $user->getId());
                    if (!$user instanceof StaffSession) {
                        // osTicket <= v1.9.7 or so
                        $user = new StaffSession($user->getId());
                    }
                } elseif ($this->config->get('auto-create')) {
                    global $cfg;
                    
                    $user = Staff::create();
                    $response_data = json_decode($result, true);
                    $names = explode(' ', $response_data['name'], 2);
                    $data = [
                        'username' => substr($response_data['email'], 0, strpos($response_data['email'], '@')),
                        'firstname' => $names[0],
                        'lastname' => $names[1],
                        'email' => $response_data['email'], 
                        'role_id' => $this->config->get('auto-role-id'),
                        'dept_id' => $this->config->get('auto-department-id'),
                    ];
                    
                    // error_log(json_encode($data));
                    $errors = [];
                    if ($user->update($data, $errors)) {
                        $type = array('type' => 'created');
                        Signal::send('object.created', $staff, $type);
                        
                        $user = StaffSession::lookup($username);
                    } else {
                        //error_log(json_encode($errors));
                    }
                }
            }
        }
        
        curl_close($ch);
        return $user;
    }
}


require_once(INCLUDE_DIR.'class.plugin.php');
require_once('config.php');
class ExternalRESTAuthPlugin extends Plugin {
    var $config_class = 'ExternalRESTAuthConfig';
    
    function bootstrap() {
        $config = $this->getConfig();
        
        if ($config->get('auth-staff')) {
            StaffAuthenticationBackend::register(new ExternalRESTAuthentication($config));
        }
    }
}