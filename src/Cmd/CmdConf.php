<?php

namespace G\Cmd;

class CmdConf
{
    static function getConf($data){
        return array_merge($data,[
           'dbmq' => DbMqCmd::class,
        ]);
    }
}