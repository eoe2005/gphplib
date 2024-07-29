<?php

namespace G;
/**
 * @method static insert($data)
 * @method static delete($where = [])
 * @method static update($data,$where = [])
 * @method static fetch($where = [],$fields = '')
 * @method static fetchAll($where = [],$fields = '')
 * @method static min($name,$where = [])
 * @method static max($name,$where = [])
 * @method static count($name,$where = [])
 * @method static avg($name,$where = [])
 * @method static  sum($name,$where = [])
 * @method static select($keys)
 * @method static DbQuery where($where)
 * @method static DbQuery order($order)
 * @method static DbQuery group($val)
 * @method static DbQuery limit($start,$pagesize = 10)
 * @method static DbQuery page($page = 1,$pagesize = 10)
 * @method static find($id)
 * @method static findAll($ids)
 * @method static save($data)
 *
 * @method insert($data)
 * @method delete($where = [])
 * @method update($data,$where = [])
 * @method fetch($where = [],$fields = '')
 * @method fetchAll($where = [],$fields = '')
 * @method min($name,$where = [])
 * @method max($name,$where = [])
 * @method count($name,$where = [])
 * @method avg($name,$where = [])
 * @method sum($name,$where = [])
 * @method DbQuery select($keys)
 * @method DbQuery where($where)
 * @method DbQuery order($order)
 * @method DbQuery group($val)
 * @method DbQuery limit($start,$pagesize = 10)
 * @method DbQuery page($page = 1,$pagesize = 10)
 * @method  find($id)
 * @method  findAll($ids)
 * @method save($data)
 */
class Model
{

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([new static(),$name],$arguments);
    }
    public function __call($name, $arguments)
    {
        return call_user_func_array([new DbQuery($this->tableName),$name],$arguments);
    }

    private $tableName = '';

    public function __construct($tbname = '')
    {
        if($tbname){
            $this->tableName = Conf::get('database.tablepre','').$tbname;
        }else{
            $name = str_replace('Model','',basename(str_replace('\\','/',get_called_class())) );
            $name = preg_replace_callback("/[A-Z]/",function ($m){
                return '_'.strtolower($m[0]);
            },$name);
            $this->tableName = Conf::get('database.tablepre').substr($name,1);
        }
    }
}