<?php 

session_start();

$GLOBALS['config'] = array(
    'main_site' => 'http://www.cssanott.co.uk',
    // 'main_site' => 'http://youkustats',
    'site_url' => 'http://www.cssanott.co.uk/YouKu-Stats-Website',
    // 'site_url' => 'http://youkustats',
    'mysql' => array(
                     'host' => 'localhost',
                     'username' => 'xwiy_sql',
                     'password' => 'BJc=Ev.HdQ(p',
                     'db' => 'xwiy_youkustats'
                     ),
    'remember' => array(
                        'cookie_name' => 'hash',
                        'cookie_expiry' => 604800
                        ),
    'session' => array(
                       'token_name' => 'user'
                       )
);

spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});

require_once('functions/html.php');
require_once('functions/sanitize.php');
require_once('functions/generateurl.php');