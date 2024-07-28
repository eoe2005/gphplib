<?php

namespace G;

class Log
{
    private static $_data = [];
    static function Err($format){
        self::save('Error',func_get_args());
    }
    static function Debug($format){
        self::save('Debug',func_get_args());
    }
    static function Waring($format){
        self::save('Waring',func_get_args());
    }
    static function Sql($format){
        self::save('Sql',func_get_args());
    }

    private static function save($level,$args){
        if(php_sapi_name() == 'cli'){
            echo sprintf("[%s] %s %s\n",date('Y-m-d H:i:s'),$level,call_user_func_array('sprintf',$args));
            return;
        }
        if(!self::$_data){
            self::$_data[] = sprintf("[%s] %s %s %s",date('Y-m-d H:i:s'),md5(time()),$_SERVER['REQUEST_URI'],json_encode($_REQUEST,JSON_UNESCAPED_UNICODE));
        }
        self::$_data[] = sprintf("  [%s] %s %s",date('Y-m-d H:i:s'),$level,call_user_func_array('sprintf',$args));
    }

    static function flush(){
        if(self::$_data){
            file_put_contents(sprintf('%s/%s%s_%s.log',Conf::get('log.path','/tmp'),Req::getApp(),(php_sapi_name() == 'cli' ? '_cli' : ''),date('Ymd')),implode("\r\n",self::$_data),FILE_APPEND);
            self::$_data = [];
        }

    }
}