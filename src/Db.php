<?php

namespace G;
/**
 * @method static query($sql,$args = [])
 * @method static begin(\Countable $call)
 * @method static getLastID()
 */
class Db
{
    /**
     * @var \G\Db
     */
    private static $self = null;


    /**
     * @var \PDO
     */
    private $_pdo = null;

    static function ins(){
        if(is_null(self::$self)){
            self::$self = new static();
        }
        return self::$self;
    }

    private function __construct()
    {
        $conf = Conf::get('database',[]);
        $driver = $conf['driver'] ?? 'mysql';
        if($driver == 'mysql'){
            $this->_pdo = new \PDO(sprintf("mysql:host=%s;dbname=%s",$conf['host'] ?? '127.0.0.1',$conf['dbname'] ?? 'test'),$conf['user'] ?? 'root',$conf['passwd'] ?? '');
        }else{
            throw new \Exception('系统配置错误',500);
        }
    }
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::ins(),$name],$arguments);
    }
    public function __call($name, $arguments)
    {
        return call_user_func_array([self::ins(),$name],$arguments);
    }

    private function begin(\Countable $call){
        if(!$this->_pdo->inTransaction()){
            $this->_pdo->beginTransaction();
        }
        try {
            $ret = call_user_func_array($call,[]);
            $this->_pdo->commit();
            return $ret;
        }catch (\Exception $e){
            $this->_pdo->rollBack();
            return false;
        }
    }
    private function getLastID(){
        return $this->_pdo->lastInsertId();
    }
    private function query($sql,$args = []){
//        var_dump($sql,$args);
        Log::Sql('%s %s',$sql,json_encode($args));
        $ret = $this->_pdo->prepare($sql);
        if($ret === false){
            return $ret;
        }
        $ret->execute($args);
        Log::Sql("[%s %s] %s %s",$ret->errorCode(),json_encode($ret->errorInfo(),JSON_UNESCAPED_UNICODE),$sql,json_encode($args,JSON_UNESCAPED_UNICODE));
        return $ret;
    }

    /**
     * @param $tname
     * @return Model
     */
    static function table($tname){
        return new Model($tname);
    }


}