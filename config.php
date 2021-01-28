<?php
require_once(INCLUDE_DIR.'/class.forms.php');

class ExternalRESTAuthConfig extends PluginConfig {

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate() {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function($x) { return $x; },
                function($x, $y, $n) { return $n != 1 ? $y : $x; },
            );
        }
        return Plugin::translate('auth-ext');
    }

    function getOptions() {
        list($__, $_N) = self::translate();
        return [
            'uri' => new TextboxField(array(
                'label' => $__('SSO Address'),
                'configuration' => array('size'=>60, 'length'=>100),
            )),
            
            'auth-staff' => new BooleanField(array(
                'label' => $__('Staff Authentication'),
                'default' => true,
                'configuration' => array(
                    'desc' => $__('Enable authentication of staff members')
                )
            )),
            
            'auto-create' => new BooleanField(array(
                'label' => $__('Auto-Create users'),
                'default' => true,
                'configuration' => array(
                    'desc' => $__('If a user authentication succeeds via this backend but it is not registered in OSTicket - register them')
                )
            )),
            
            'auto-department-id' => new ChoiceField(array(
                'default' => 0,
                'required' => true,
                'label' => __('Department for created users'),
                'choices' => Dept::getDepartments(),
                'configuration' => array(
                    'classes' => 'span12',
                ),
            )),
            
            'auto-role-id' => new ChoiceField(array(
                'default' => 0,
                'required' => true,
                'label' => __('Role for created users'),
                'choices' => Role::getRoles(),
                'configuration' => array(
                    'classes' => 'span12',
                ),
            )),
        ];
    }

    function pre_save(&$config, &$errors) {
        global $msg;

        list($__, $_N) = self::translate();
        if (!$errors)
            $msg = $__('Configuration updated successfully');

        return true;
    }
}
