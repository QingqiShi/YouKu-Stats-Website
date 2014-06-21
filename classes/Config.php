<?php

class Config {

    // Load the $GLOBALS array and get values using a path based method
    // i.e. Config::get('mysql/host')
    public static function get($path = null) {
        if ($path) {
            $config = $GLOBALS['config'];
            $path = explode('/', $path);

            foreach($path as $bit) {
                if (isset($config[$bit])) {
                    if (is_array($config[$bit])) {
                        $config = $config[$bit];
                    } else {
                        return $config[$bit];
                    }
                } else {
                    return null;
                }
            }
            return $config;
        }
        return null;
    }
}
