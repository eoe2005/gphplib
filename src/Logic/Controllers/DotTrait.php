<?php

namespace G\Logic\Controllers;

use G\Logic\Models\SysDotConfModel;
use G\Logic\Services\SysDotSvc;
use G\Req;

trait DotTrait
{
    /**
     * 添加日志
     * @return void
     */
    function addDotAction()
    {
        $key = Req::input('key');
        $val = Req::input('val', 1);
        $row = SysDotConfModel::fetch(['conf_key' => $key]);
        if (!$row) {
            return '';
        }
        SysDotSvc::addLog($row['id'], $val);
        return '';
    }

    /**
     * 批量添加日志
     * @return void
     */
    function addDotsAction()
    {
        $list = Req::input(['data']);
        if(!$list){
            return '';
        }
        $datas = [];
        $keys = [];
        foreach ($list as $item) {
            $day = date('Y-m-d H:00:00', $item['time']);
            if (!isset($datas[$day])) {
                $datas[$day] = [];
            }
            if(!isset($datas[$day][$item['key']])){
                $datas[$day][$item['key']] = 0;
            }
            $datas[$day][$item['key']] += $item['val'];
            $keys[] = $item['key'];
        }
        $list = SysDotConfModel::fetchAll(['conf_key' => $keys]);
        $gmap = array_column($list,null,'conf_key');
        foreach ($datas as $d => $item){
            foreach ($item as $k => $v){
                $confId = $gmap[$k] ?? 0;
                if(!$confId){
                    continue;
                }
                SysDotSvc::addLog($confId,$v);
            }

        }
        return '';
    }
}