<?php 

session_start();

$GLOBALS['config'] = array(
    // 'site_url' => 'http://www.cssanott.co.uk',
    'site_url' => 'http://youkustats',
    'mysql' => array(
                     'host' => '127.0.0.1',
                     'username' => 'root',
                     'passowrd' => '',
                     'db' => 'rocky_youkustats'
                     ),
    'remember' => array(
                        'cookie_name' => 'hash',
                        'cookie_expiry' => 604800
                        ),
    'session' => array(
                       'session_name' => 'user'
                       )
);

spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});

