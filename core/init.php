<?php 

session_start();

$GLOBALS['config'] = array(
    // 'site_url' => 'http://www.cssanott.co.uk',
    'site_url' => 'http://youkustats',
    'mysql' => array(
                     'host' => 'localhost',
                     'username' => 'ukstone',
                     'password' => 'woaini344-1107',
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

