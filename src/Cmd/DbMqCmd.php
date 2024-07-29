<?php

namespace G\Cmd;

use G\Db;
use G\Job;
use G\Log;

class DbMqCmd implements Job
{

    function desc()
    {
        return "生产数据库版本的mq\n      init 初始化数据库";
    }

    function help()
    {
        // TODO: Implement help() method.
    }

    function execute($args = [])
    {
        $isinit = $args[0] ?? '';
        if($isinit == 'init'){
            $this->initData();
        }else{
            $this->run();
        }
    }
    private function run(){
        while (true){
            $time = date('Y-m-d H:i:s');
            $r  = Db::query("SELECT * FROM `sys_mq` WHERE `is_done`=0 AND `next_time`>=? ORDER BY next_time ASC LIMIT 1000",[$time]);
            $list = $r->fetchAll(\PDO::FETCH_ASSOC);
            if(!$list){
                return;
            }
            foreach ($list as $item){
                try{
                    if((new $item['consumer'])->execute(json_decode($item['msg'],true))){
                        Db::query('UPDATE sys_mq SET is_done=1 WHERE id=?',[$item['id']]);
                    }else{
                        Db::query('UPDATE sys_mq SET run_times=run_times+1,next_time=? WHERE id=?',[$item['id'],date('Y-m-d H:i:s',time() + pow(10,$item['run_times']))]);
                    }
                }catch (\Exception $e){
                    Db::query('UPDATE sys_mq SET run_times=run_times+1,next_time=? WHERE id=?',[$item['id'],date('Y-m-d H:i:s',time() + pow(10,$item['run_times']))]);
                }

            }
        }
    }
    //初始化数据
    private function initData(){
        $r = Db::query("show tables");
        $tlist = $r->fetchAll(\PDO::FETCH_COLUMN);
        if(in_array('sys_mq',$tlist)){
            Log::Debug('数据表已经创建，可以立即使用');
        }else{
            $ret = Db::exec("CREATE TABLE `sys_mq`
            (
                `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `run_times` INT UNSIGNED    NOT NULL DEFAULT 0 COMMENT '执行次数',
                `consumer`  VARCHAR(255)    NOT NULL DEFAULT '' COMMENT '执行脚本',
                `next_time` DATETIME        NOT NULL COMMENT '下次执行时间',
                `is_done`   TINYINT         NOT NULL DEFAULT 0 COMMENT '是否已经完成',
                `msg`       TEXT            NOT NULL COMMENT '消息内容',
                `is_del`    TINYINT         NOT NULL DEFAULT 0 COMMENT '是否删除',
                `create_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `create_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '创建IP',
                `update_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                `update_ip` VARCHAR(20)     NOT NULL DEFAULT '' COMMENT '更新IP',
                PRIMARY KEY (`id`),
                KEY `idx_create` (`create_at`),
                KEY `idx_update` (`update_at`),
                KEY `idx_done_time` (`is_done`, `next_time`)
            ) ENGINE = innodb DEFAULT CHARSET = utf8mb4 COMMENT ='消息队列';");
            Log::Debug('数据表已经创建成功，可以立即使用');
        }

    }
}