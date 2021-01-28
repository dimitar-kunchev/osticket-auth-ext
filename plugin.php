<?php

return array(
    'id' =>             'auth:ext', # notrans
    'version' =>        '0.1',
    'name' =>           /* trans */ 'External REST Authentication',
    'author' =>         'Dimitar Kunchev',
    'description' =>    /* trans */ 'Allows authentication with a REST API',
    'url' =>            'http://about:blank',
    'plugin' =>         'authenticate.php:ExternalRESTAuthPlugin'
);

?>
