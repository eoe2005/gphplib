<?php

namespace G;


abstract class Event
{
    static function send($data){
        Db::table('sys_mq',false)->insert([
            'msg' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'next_time' => date('Y-m-d H:i:s'),
            'consumer' => get_called_class(),
        ]);
    }
    //延时执行
    static function after($second,$data){
        Db::table('sys_mq',false)->insert([
            'msg' => json_encode($data,JSON_UNESCAPED_UNICODE),
            'next_time' => date('Y-m-d H:i:s',time() + $second),
            'consumer' => get_called_class(),
        ]);
    }
    abstract function execute($data);
}