<?php

namespace G;

class DbQuery
{

    private $_where = [];
    private $_limit = [];
    private $_select = [];
    private $_order = [];
    private $_group = [];
    private $_tabname;
    public function __construct($tabname)
    {
        $this->_tabname = $tabname;
    }

    /**
     * @param $data
     * @return string
     */
    function insert($data){
        $data['update_ip'] = Req::getIP();
        $data['create_ip'] = Req::getIP();
        $sql = sprintf("INSERT `%s`(`%s`) VALUES(%s)",$this->_tabname,implode('`,`',array_keys($data)),substr(str_repeat(',?',count($data)),1));
        $ret = Db::query($sql,array_values($data));
        return Db::getLastID();
    }

    /**
     * 保存数据
     * @param $data
     * @return int|string
     * @throws \Exception
     */
    function save($data){
        if(isset($data['id']) && $data['id']){
            $id = $data['id'];
            unset($data['id']);
            return $this->update($data,['id' => $id]);
        }else{
            return $this->insert($data);
        }
    }

    /**
     * @param $where
     * @return int
     * @throws \Exception
     */
    function delete($where = []){
        $this->where($where);
        return $this->update(['is_del' => 1]);
    }

    /**
     * @param $data
     * @param $where
     * @return int
     * @throws \Exception
     */
    function update($data,$where = [])
    {
        $this->where($where);
        $data['update_ip'] = Req::getIP();
        $args = [];
        $sets = [];
        if(!$data || !is_array($data)){
            throw new \Exception('更新参数错误');
        }
        foreach ($data as $k => $v){
            if(strpos($k,'[') !== false){
                $kk = explode('[',$k);
                $vv = explode(']',$kk[1]);
                $sets[] = sprintf('`%s`=`%s` %s ?',$kk[0],$kk[0],$vv[0]);
            }else{
                $sets[] = sprintf('`%s`=?',$k);
            }
            $args[] = $v;
        }
        list($where,$wargs) = $this->buildWhere();
        if(!$wargs){
            throw new \Exception('更新数据缺少条件');
        }
        $sql = sprintf('UPDATE `%s` SET %s WHERE %s',$this->_tabname,implode(',',$sets),$where);
//        var_dump($args,$wargs);
        $re = Db::query($sql,array_merge($args , $wargs));
        return $re->rowCount();
    }
    function fetch($where = [],$fields = ''){
        $this->select($fields);
        $this->where($where);
        list($where,$wargs) = $this->buildWhere();
        $sql = sprintf('SELECT %s FROM `%s` WHERE %s',$this->buildSelect(),$this->_tabname,$where);
//        echo $sql;die();
        $ret = Db::query($sql,$wargs);
        return $ret->fetch(\PDO::FETCH_ASSOC);
    }
    function fetchAll($where = [],$fields = ''){
        $this->select($fields);
        $this->where($where);
        list($where,$wargs) = $this->buildWhere();
        $sql = sprintf('SELECT %s FROM `%s` WHERE %s',$this->buildSelect(),$this->_tabname,$where);
        $ret = Db::query($sql,$wargs);
        return $ret->fetchAll(\PDO::FETCH_ASSOC);
    }
    private function buildSelect(){
        if(!$this->_select){
            return '*';
        }
        if(is_string($this->_select)){
            return $this->_select;
        }elseif(is_array($this->_select)){
            $ret = [];
            foreach ($this->_select as $val){
                if(strpos($val,'(') || strpos($val,' ')){
                    $ret[] = $val;
                }else{
                    $ret[] = '`'.$val.'`';
                }
            }
            return implode(',',$ret);
        }
        return '*';
    }

    /**
     * 解析sql
     * @return void
     */
    private function buildWhere()
    {
        $where = '`is_del`=0';
        $args = [];
        if(is_string($this->_where)){
            $where .= ' AND '.$this->_where;
        }elseif(is_array($this->_where)){
            foreach ($this->_where as $k => $val){
                if(strpos($k,'[') !== false){
                    $kk = explode('[',$k);
                    $op = explode(']',$kk[1]);
                    if($op[0] == 'notin'){
                        $where .= sprintf(' AND `%s` NOT IN(%s)',$kk[0],substr(str_repeat(',?',count($val)),1));
                        foreach ($val as $v1){
                            $args[] = $v1;
                        }
                    }elseif ($op[0] == 'like'){
                        $where .= sprintf(' AND `%s`%s ?',$kk[0],'%'.$op[0].'%');
                        $args[] = $val;
                    }else{
                        $where .= sprintf(' AND `%s`%s ?',$kk[0],$op[0]);
                        $args[] = $val;
                    }

                }else{
                    if(is_array($val)){
                        $where .= sprintf(' AND `%s` IN(%s)',$k,substr(str_repeat(',?',count($val)),1));
                        foreach ($val as $v1){
                            $args[] = $v1;
                        }
                    }else{
                        $where .= sprintf(' AND `%s`=?',$k);
                        $args[] = $val;
                    }

                }
            }
        }
        if($this->_order){
            if(is_array($this->_order)){
                $ords = [];
                foreach ($this->_order as $k=>$v){
                    if(is_numeric($k)){
                        $ords[] = $v;
                    }elseif (is_string($k)){
                        $ords[] = sprintf('`%s` %s',$k,strtoupper($v));
                    }
                }
                $where .= ' ORDER BY '.implode(',',$ords);
            }elseif(is_string($this->_order)){
                $where .= ' ORDER BY '.$this->_order;
            }
        }
        if($this->_group){
            if(is_string($this->_group)){
                if(strpos($this->_group,',') !== false){
                    $where .= sprintf(' GROUP BY(%s)',$this->_group);
                }else{
                    $where .= sprintf(' GROUP BY(`%s`)',$this->_group);
                }
            }elseif(is_array($this->_group)){
                $where .= sprintf(' GROUP BY(`%s`)',implode('`,`',$this->_group));
            }
        }
        if($this->_limit){
            $where .= sprintf(' LIMIT %d,%d',$this->_limit[0],$this->_limit[1]);
        }
        return [$where,$args];
    }

    function min($name,$where = [])
    {
        return $this->sysval('min',$name,$where);
    }
    function max($name,$where = [])
    {
        return $this->sysval('max',$name,$where);
    }
    function count($name,$where = [])
    {
        return $this->sysval('count',$name,$where);
    }
    function avg($name,$where = [])
    {
        return $this->sysval('avg',$name,$where);
    }
    function sum($name,$where = [])
    {
        return $this->sysval('sum',$name,$where);
    }
    private function sysval($cmd,$name,$where = []){
        $this->where($where);
        list($where,$args) = $this->buildWhere();
        if($name == '*'){
            $sql = sprintf("SELECT %s(%s) AS num FROM `%s` WHERE %s",$cmd,$name,$this->_tabname,$where);
        }else{
            $sql = sprintf("SELECT %s(`%s`) AS num FROM `%s` WHERE %s",$cmd,$name,$this->_tabname,$where);
        }

        $ret = Db::query($sql,$args);
        $row = $ret->fetch(\PDO::FETCH_ASSOC);
        return $row['num'] ?? 0;
    }
    function select($keys){
        if(!$keys){
            return $this;
        }
        $this->_select = $keys;
        return $this;
    }
    function where($where){
        if(!$where){
            return $this;
        }
        $this->_where = $where;
        return $this;
    }
    function order($order){
        $this->_order = $order;
        return $this;
    }

    function group($val)
    {
        $this->_group = $val;
        return $this;
    }

    function limit($start,$pagesize = 10)
    {
        $this->_limit = [$start,$pagesize];
        return $this;
    }

    function page($page = 1,$pagesize = 10)
    {
        if($page >= 1){
           return $this->limit(($page - 1) * $pagesize,$pagesize);
        }
        return $this->limit(0,$pagesize);

    }

    /**
     * 获取一条记录
     * @param $id
     * @return mixed
     */
    function find($id){
        $ret = Db::query(sprintf("SELECT * FROM `%s` WHERE `is_del`=0 AND `id`=?",$this->_tabname),[$id]);
        return $ret->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * 获取多条记录
     * @param $ids
     * @return array
     */
    function findAll($ids){
        $ret = Db::query(sprintf("SELECT * FROM `%s` WHERE `is_del`=0 AND `id` IN (%s)",$this->_tabname,substr(str_repeat(',?',count($ids)),1)),array_values($ids));
        $r = $ret->fetchAll(\PDO::FETCH_ASSOC);
        return array_column($r,null,'id');
    }
}