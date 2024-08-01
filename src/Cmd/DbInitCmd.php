<?php

namespace G\Cmd;

use G\Db;
use G\Job;
use G\Log;

class DbInitCmd implements Job
{

    function desc()
    {
        return "数据库操作\n            update 更新数据库";
    }

    function help()
    {
        return '';
    }

    function execute($args = [])
    {
        $isinit = $args[0] ?? '';
        if($isinit == 'update'){
            $this->updateTable();
        }else{
            Db::exec($this->getTableString());
        }
    }
    private function updateTable(){
        $tables = [];
        $tableStr = explode(';',$this->getTableString());
        $newTables = [];
        $oldTables = [];
        foreach ($tableStr as $item){
            $item = trim($item);
            if(!$item){
                continue;
            }
            list($tname,$info) = $this->parseTable($item);
            $newTables[$tname] = $info;
        }

        $r = Db::query("show tables");
        $tlist = $r->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tlist as $tname) {
           $rr = Db::query('show create table '.$tname);
           $dd = $rr->fetch(\PDO::FETCH_ASSOC);
            list($tname1,$info) = $this->parseTable($dd['Create Table']);
            $oldTables[$tname] = $info;
        }

        $delTable = [];
        $addTable = [];
        foreach ($oldTables as $tn => $v){
            if(!isset($newTables[$tn]) && !in_array($tn,self::$_systables)){
                $delTable[] = $tn;
            }
        }

        foreach ($newTables as $tn => $ntable){
            if(!isset($oldTables[$tn])){
                $addTable[] = $ntable['sql'];
                continue;
            }
            $oldTable = $oldTables[$tn];
            $dropF = [];//待删除的字段
            $dropIndex = [];//待删除的索引
            $monifyF = [];//待修改的字段
            $addKey = [];//要增加的索引
            $addF = [];//要增加的字段
            $isChangeTc = $ntable['table_comment'] != $oldTable['table_comment'];

            foreach ($oldTable['fields'] as $k => $ofitem){
                if(!isset($ntable['fields'][$k])){
                    $dropF[] = $k;
                }else{
                    $keys = ['type','null','defalut','ext','comment'];
                   $nv = $ntable['fields'][$k];
                   foreach ($keys as $kk){
                       if($ofitem[$kk] != $nv[$kk]){
//                           Log::Debug("字段不一样 %s -> %s : %s",$k,$ofitem[$kk],$nv[$kk]);
                           $monifyF[] = $k;
                       }
                   }
                }
            }
            foreach ($oldTable['indexs'] as $k => $ov){
                if(!isset($ntable['indexs'][$k])){
                    $dropIndex[] = $k;
                    continue;
                }
                $ni = $ntable['indexs'][$k] ?? '';
                if(!$ni){
                    $dropIndex[] = $k;
                    continue;
                }
                if($ni['type'] != $ov['type']){
                    $dropIndex[] = $k;
                    continue;
                }
                if(!isset($ni['keys'])){
                    $dropIndex[] = $k;
                    continue;
                }
                foreach ($ni['keys'] as $item){
                    if(!in_array($item,$ov['keys'])){
                        $dropIndex[] = $k;
                        break;
                    }
                }
                foreach ($ov['keys'] as $item){
                    if(!in_array($item,$ni['keys'])){
                        $dropIndex[] = $k;
                        break;
                    }
                }
            }
            foreach ($ntable['fields'] as $k => $nv){
                if(!isset($oldTable['fields'][$k])){
                    $addF[] = $k;
                }
            }
            foreach ($ntable['indexs'] as $k => $nv){
                if(!isset($oldTable['indexs'][$k])){
                    $addKey[] = $k;
                }
            }
            if($isChangeTc){//修改表备注
                Db::exec(sprintf("ALTER TABLE `%s` COMMENT='%s'",$tn,$ntable['table_comment']));
            }
            echo sprintf("删除索引 : %s\n",implode(',',$dropIndex));
            foreach ($dropIndex as $item){//删除索引
                Db::exec(sprintf("ALTER TABLE `%s` DROP INDEX `%s`",$tn,$item));
            }
            echo sprintf("删除字段 : %s\n",implode(',',$dropF));
            foreach ($dropF as $item){
                Db::exec(sprintf("ALTER TABLE `%s` DROP COLUMN `%s`",$tn,$item));
            }
            echo sprintf("修改字段 : %s\n",implode(',',$monifyF));
            foreach ($monifyF as $item){//修改字段
                Db::exec(sprintf("ALTER TABLE `%s` CHANGE `%s` %s",$tn,$item,$this->buildField($ntable,$item)));
            }
            echo sprintf("添加字段 : %s\n",implode(',',$addF));
            foreach ($addF as $item){//添加字段
                Db::exec(sprintf("ALTER TABLE `%s` ADD %s",$tn,$this->buildField($ntable,$item)));
            }
            echo sprintf("添加索引 : %s\n",implode(',',$addKey));
            foreach ($addKey as $item){//添加索引
                $indexVal = $ntable['indexs'][$item];
                if($indexVal['type'] == 'key'){
                    Db::exec(sprintf("ALTER TABLE `%s` ADD INDEX `%s`(`%s`)",$tn,$item,implode('`,`',$indexVal['keys'])));
                }elseif($indexVal['type'] == 'uniq'){
                    Db::exec(sprintf("ALTER TABLE `%s` ADD UNIQUE INDEX `%s`(`%s`)",$tn,$item,implode('`,`',$indexVal['keys'])));
                }
            }
        }
        echo sprintf("删除表 : %s\n",implode(',',$delTable));
        foreach ($delTable as $tb){
            if(!in_array($tb,['sys_mq'])){
                Db::exec(sprintf("DROP TABLE `%s`",$tb));
            }
        }
        //添加表
        foreach ($addTable as $sql){
            Db::exec($sql);
        }


    }
    function buildField($table,$key){
        $finfo = $table['fields'][$key];
        $i = array_search($key,$table['field_sort']);
        $ext = '';
        if($i !== false && $i > 0){
            $ext = sprintf('AFTER `%s`',$table['field_sort'][$i-1]);
        }
        $ret = sprintf("`%s` %s %s %s %s COMMENT '%s' %s",$key,$finfo['type'],$finfo['null'] ? 'NOT NULL' : '',$finfo['defalut'] != 'NULL' ? sprintf('DEFAULT %s',$finfo['defalut']) : '',$finfo['ext'],$finfo['comment'],$ext);
        return $ret;
    }
    private function parseTable($str){
        $item = preg_replace("/\s+/"," ",$str);
        $ext = substr($item,strpos($item,'`') + 1);
        $tname = substr($ext,0,strpos($ext,'`'));
        $ext = trim(substr($ext,strpos($ext,'(') + 1));
        $ei = strrpos($ext,')');
        $subext = trim(substr($ext,$ei + 1));
        $ext = trim(substr($ext,0,$ei));

        $lines = explode(',',$ext);
        $fields = [];
        $indexs = [];
        $fsort = [];
        foreach ($lines as $line){
            $line = trim($line);
            $fc = strtolower($line[0]);
            if($fc == '`'){
                list($f,$info) = $this->parseField($line);
                $fields[$f] = $info;
                $fsort[] = $f;
            }elseif($fc == 'k'){//索引
                list($f,$info) = $this->parseKey($line);
                $indexs[$f] = [
                    'type' => 'key',
                    'keys' => $info,
                ];
            }elseif($fc == 'p'){//主键
                list($f,$info) = $this->parsePrimary($line);
                $indexs[$f] = [
                    'type' => 'primary',
                    'keys' => $info,
                ];
            }elseif($fc == 'u'){//唯一索引
                list($f,$info) = $this->parseUniqKey($line);
                $indexs[$f] = [
                    'type' => 'uniq',
                    'keys' => $info,
                ];
            }
        }
        $subext = str_replace(' ','',strtoupper($subext));
        $tc = '';
        $ti = strpos($subext,'COMMENT=');
        if($ti !== false){
            $tc = rtrim(substr($subext,$ti + 9),"'");

        }

        return [
            $tname,[
                'sql' => $item,
                'fields' => $fields,
                'indexs' => $indexs,
                'table_comment' => $tc,
                'field_sort' => $fsort,
            ]
        ];
    }
    private function parseField($line){
        $line = ltrim($line,'`');
        $fe = strpos($line,'`');
        $fname = substr($line,0,$fe);
        $line = trim(substr($line,$fe + 1));

        $uline = strtoupper($line);
        $ext = '';
        if(strpos($uline,'AUTO_INCREMENT') !== false){
            $ext = 'AUTO_INCREMENT';
        }if(strpos($uline,'ON UPDATE') !== false){
            $ess = trim(substr($uline,strpos($uline,'ON UPDATE') + 9));
            $ext = 'ON UPDATE '. substr($ess,0,strpos($ess,' '));
        }
        $def = 'NULL';
        if(strpos($uline,'DEFAULT')){
            $ess = trim(substr($uline,strpos($uline,'DEFAULT') + 8));
            $def = substr($ess,0,strpos($ess,' '));
        }
        $comment = '';
        if(strpos($uline,'COMMENT')){
            $ess = trim(substr($uline,strpos($uline,'COMMENT') + 9));
            $comment = substr($ess,0,strpos($ess,"'"));
        }
        $lis = explode(' ',$uline);

        $types = $lis[0];
        if($lis[1] == 'UNSIGNED'){
            $types .= ' UNSIGNED';
        }
        $ret = [
            'type' => $types,
            'null' => strpos($uline,'NOT NULL') !== false,
            'defalut' => $def,
            'ext' => $ext,
            'comment' => $comment
        ];


        return [$fname,$ret];
    }
    private function parsePrimary($line){
        $line = explode('(`',$line);
        return ['primary',[substr($line[1],0,strpos($line[1],'`'))]];
    }
    private function parseKey($line){
        $line = explode('` (`',$line);
        $v = str_replace(" ",'',$line[1]);
        return[
            substr($line[0],strpos($line[0],'`')+1),
            explode("`,`",substr($v,0,strpos($v,'`)')))
        ];
    }
    private function parseUniqKey($line){
        $line = explode('` (`',$line);
        $v = str_replace(" ",'',$line[1]);
        return[
            substr($line[0],strpos($line[0],'`') + 1),
            explode("`,`",substr($v,0,strpos($v,'`)')))
        ];
    }
    private function getTableString(){
        return file_get_contents(APP_ROOT.'/App/db.sql');
    }

    private static $_systables = ['sys_mq'];

}