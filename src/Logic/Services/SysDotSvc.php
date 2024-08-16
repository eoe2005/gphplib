<?php

namespace G\Logic\Services;

use G\Logic\Models\SysDotLogModel;

class SysDotSvc
{
    /**
     * 添加日志
     * @param $id
     * @return void
     */
    static function addLog($id,$val = 1){
        if(!SysDotLogModel::update(['val[+]' => $val],['conf_id' => $id,'day' => date('Y-m-d H:00:00')])){
            SysDotLogModel::insert(['val' => $val,'day' => date('Y-m-d H:00:00'),'conf_id' => $id]);
        }
    }
}