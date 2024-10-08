<?php

namespace G;

class Conf
{
    private static $_data = null;
    static function get($key,$def = ''){
        if(is_null(self::$_data)){
            self::$_data =parse_ini_file(APP_ROOT.'/.env',true);
        }
        $keys = explode('.',$key);
        if(count($keys) == 1){
            return self::$_data[$key] ?? $def;
        }
        $ret = self::$_data[$keys[0]] ?? [];
        return $ret[implode('.',array_slice($keys,1))] ?? $def;
    }



    /**
     * 获取配置
     * @param $key
     * @param $def
     * @return void
     */
    static function getAppConf($key,$def = []){
        return self::getConfGroup(strtolower(APP_NAME),$key,$def);
    }

    /**
     * 保存配置组
     * @param $app
     * @param $key
     * @param $val
     * @return void
     */
    static function setConfGroup($app,$key,$val = []){
        $model = new Model('t_sys_conf');
        if(!is_array($val)){
            throw new \Exception('参数错误',403);
        }
        //删除失效的配置
        $model->delete(['conf_key' => $app,'group_key' => $key,'item_key[notin]' => array_keys($val)]);
        foreach ($val as $k => $v){
            if(!$model->update(['val' => $v,'is_del' => 0],['conf_key' => $app,'group_key' => $key,'item_key' => $k])){
                $model->insert(['conf_key' => $app,'group_key' => $key,'item_key' => $k]);
            }
        }
        Cache::redis(false)->zAdd(self::getRedisKey($app,true),['CH'],$key,1);
        Cache::redis(false)->hMSet(self::getRedisKey($app,$key),$val);
    }

    /**
     * 获取分组配置信息
     * @param $app
     * @param $key
     * @param $defval
     * @return array
     * @throws \Exception
     */
    static function getConfGroup($app,$key,$defval = []){
        if(!is_array($defval)){
            throw new \Exception('参数错误',403);
        }
        $retKey = self::getRedisKey($app,$key);
        $redis = Cache::redis(true);
        if($redis->exists($retKey)){
            return $redis->hGetAll($retKey);
        }
        $model = new Model('t_sys_conf');
        $retList = $model->fetchAll(['conf_key' => $app,'group_key' => $key]);
        $retMap = [];
        if($retList){
            foreach ($retList as $k => $v){
                $retMap[$v['item_key']] = $v['val'];
            }
            $redis->hMSet($retKey,$retMap);
        }
        foreach ($defval as $k => $v){
            if(!isset($retMap[$k])){
                $retMap[$k] = $v;
            }
        }
        return $retMap;
    }

    /**
     * 获取key信息
     * @param $app
     * @param $isKeyOrName
     * @return string
     */
    private static function getRedisKey($app,$isKeyOrName = true){
        if($isKeyOrName === true){
            return sprintf("sys:conf:%s:keys",$app);
        }else{
            return sprintf("sys:conf:%s:%s",$app,$isKeyOrName);
        }
    }

}