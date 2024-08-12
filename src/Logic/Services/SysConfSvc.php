<?php

namespace G\Logic\Services;


use G\Logic\Models\SysConfModel;

class SysConfSvc
{
    /**
     * 获取配置
     * @param $key
     * @param $def
     * @return array|mixed|string
     */
    static function getConf($key ,$def = '',$isJson = false){
        $keys = explode('.',$key);
        $where = [
            'conf_key' => $keys[0],
        ];
        $level = 2;
        if(isset($keys[1])){
            $where['group_key'] = $keys[1];
            $level = 1;
        }
        if(isset($keys[2])){
            $where['item_key'] = $keys[2];
            $level = 0;
        }
        if(!$level){
            $row = AppConfModel::where($where)->fetch();
            if(isset($row['val'])){
                return $isJson ? json_decode($row['val'],true) : $row['val'];
            }
            return $def;
        }

        $list = AppConfModel::where($where)->fetchAll();
        if(!$list){
            return $def;
        }
        $ret = is_string($def) ? [] : $def;
        if($level  == 1){
            foreach ($list as $item){
                $ret[$item['item_key']] = $isJson ? json_decode($item['val'],true) : $item['val'];
            }
            return $ret;
        }
        if($level  == 2){
            foreach ($list as $item){
                if(!isset($ret[$item['group_key']])){
                    $ret[$item['group_key']] = [];
                }
                $ret[$item['group_key']][$item['item_key']] = $isJson ? json_decode($item['val'],true) : $item['val'];
            }
            return $ret;
        }
        return $def;
    }

    /**
     * 保存配置
     * @param $key
     * @param $val
     * @return true
     * @throws \Exception
     */
    static function setConf($key,$val = ''){
        $keys = explode('.',$key);
        $level = 3 - count($keys);
        if(!$level){
            return self::saveVal($keys[0],$keys[1],$keys[2],$val);
        }elseif($level == 1){
            if(!is_array($val)){
                throw new \Exception('参数错误',500);
            }
            foreach ($val as $k => $item) {
                self::saveVal($keys[0],$keys[1],$k,$item);
            }
            return true;
        }elseif($level == 2){
            if(!is_array($val)){
                throw new \Exception('参数错误',500);
            }
            foreach ($val as $k => $item) {
                if(!is_array($item)){
                    throw new \Exception('参数错误',500);
                }
                foreach ($item as $kk => $vv){
                    self::saveVal($keys[0],$k,$kk,$vv);
                }
            }
            return true;
        }
        throw new \Exception('参数错误',500);
    }

    /**
     * 保存信息
     * @param $k1
     * @param $k2
     * @param $k3
     * @param $val
     * @return true
     * @throws \Exception
     */
    private static function saveVal($k1,$k2,$k3,$val){
        $where = [
            'conf_key' => $k1,
            'group_key' => $k2,
            'item_key' => $k3
        ];
        $row = SysConfModel::where($where)->fetch();
        if($row){
            return SysConfModel::where($where)->update(['val' => $val]);
        }
        $where['val'] = $val;
        return SysConfModel::insert($where);

    }
}