<?php

namespace G;

use Apps\Models\Sys\AppMqModel;

abstract class Event
{
    static function send($data){
        AppMqModel::save([
            'msg' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'next_time' => date('Y-m-d H:i:s'),
            'consumer' => get_called_class(),
        ]);
    }
    abstract function execute($data);
}