<?php

namespace G;

class Conf
{
    private static $_data = null;
    static function get($key,$def = ''){
        if(is_null(self::$_data)){
            self::$_data =parse_ini_file(dirname(realpath(__DIR__)).'/.env',true);
//            var_dump(self::$_data);
        }
        return self::$_data[$key] ?? $def;
    }

    /**
     * 获取全部省份
     * @return string[]
     */
    static function getAllProvince(){
        return ["北京市","天津市","河北省","山西省","内蒙古","辽宁省","吉林省","黑龙江省","上海市","江苏省","浙江省","安徽省","福建省","江西省","山东省","河南省","湖北省","湖南省","广东省","广西","海南省","重庆市","四川省","贵州省","云南省","西藏","陕西省","甘肃省","青海省","宁夏","新疆","台湾省","澳门","香港"];
    }
}