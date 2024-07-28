<?php

namespace G;

class Cache
{
    /**
     * @var Cache
     */
    private static $_self = null;

    /**
     * @var \Redis
     */
    private $_redis = null;

    /**
     * @return Cache|null
     */
    static function ins(){
        if(is_null(self::$_self)){
            self::$_self = new static();
        }
        return self::$_self;
    }

    /**
     * @return \Redis|null
     */
    static function redis(){
        return self::ins()->_redis;
    }
    private function __construct()
    {
        $this->_redis = new \Redis();
        if(!$this->_redis->connect(Conf::get('redis.host','127.0.0.1'))){
            throw new \Exception('系统异常');
        }
        $passwd = Conf::get('redis.passwd');
        if($passwd){
            if(!$this->_redis->auth($passwd)){
                throw new \Exception('系统异常');
            }
        }
    }
    private function __clone()
    {
        throw new \Exception('系统异常');
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([self::ins(),$name],$arguments);
    }
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this,$name],$arguments);
    }
}