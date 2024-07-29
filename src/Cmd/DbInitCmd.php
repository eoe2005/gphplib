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
//            Db::exec($this->getTableString());
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
            if(!isset($newTables[$tn])){
                $delTable[] = $tn;
            }
        }
        foreach ($newTables as $tn => $v){
            if(!isset($oldTables[$tn])){
                $addTable[] = $v['sql'];
            }
        }
        var_dump($addTable);
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
        foreach ($lines as $line){
            $line = trim($line);
            $fc = strtolower($line[0]);
            if($fc == '`'){
                list($f,$info) = $this->parseField($line);
                $fields[$f] = $info;
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
                'ext' => $tc
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
        return ['primary',substr($line[1],0,strpos($line[1],'`'))];
    }
    private function parseKey($line){
        $line = explode('` (`',$line);
        $v = str_replace(" ",'',$line[1]);
        return[
            substr($line[0],strpos($line[0],'`')),
            explode("`,`",substr($v,0,strpos($v,'`)')))
        ];
    }
    private function parseUniqKey($line){
        $line = explode('` (`',$line);
        $v = str_replace(" ",'',$line[1]);
        return[
            substr($line[0],strpos($line[0],'`')),
            explode("`,`",substr($v,0,strpos($v,'`)')))
        ];
    }
    private function getTableString(){
        return file_get_contents(APP_ROOT.'/App/db.sql');
    }


}