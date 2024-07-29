<?php

namespace G;

class Conf
{
    private static $_data = null;
    static function get($key,$def = ''){
        if(is_null(self::$_data)){
            if(php_sapi_name() == 'cli'){
                self::$_data =parse_ini_file(dirname(realpath($_SERVER['DOCUMENT_ROOT'])).'/.env',true);
            }else{
                self::$_data =parse_ini_file(realpath($_SERVER['DOCUMENT_ROOT']).'/.env',true);
            }
        }
        $keys = explode('.',$key);
        if(count($keys) == 1){
            return self::$_data[$key] ?? $def;
        }
        $ret = self::$_data[$keys[0]] ?? [];
        return $ret[implode('.',array_slice($keys,1))] ?? $def;
    }

}