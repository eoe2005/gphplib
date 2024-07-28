<?php

namespace G;

class Req
{
    private static $_appName = '';
    private static $ip = '';

    static function getIP(){
        if(self::$ip){
            return self::$ip;
        }
        $ip =  '';
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = "Unknown";
        }
        self::$ip = $ip;
        return $ip;
    }

    static function getApp(){
        if(!self::$_appName){
            $name = explode('.',$_SERVER['HTTP_HOST']);
            self::$_appName = ucfirst($name[0] ?? '');
        }
        return self::$_appName;
    }
    static function input($key,$def = ''){
        return $_REQUEST[$key] ?? $def;
    }


}