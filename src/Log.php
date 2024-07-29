<?php

namespace G;

/**
 * @method static Error($format)
 * @method static Sql($format)
 * @method static Debug($format)
 * @method static Waring($format)
 * @method static Info($format)
 */
class Log
{

    private static $_levelMap = [
        'ERROR' => 0,
        'WARING' => 1,
        'SQL' => 2,
        'INFO' => 3,
        'DEBUG' => 4,
    ];


    private static $_data = [];

    public static function __callStatic($name, $arguments)
    {
        $name = strtoupper($name);
        if(!self::canWrite($name)){
            return;
        }
        self::save($name,$arguments);
    }

    /**
     * 保存日志信息
     * @param $level
     * @param $args
     * @return void
     */
    private static function save($level,$args){
        if(php_sapi_name() == 'cli'){
            echo sprintf("[%s] %s %s\n",date('Y-m-d H:i:s'),$level,call_user_func_array('sprintf',$args));
            return;
        }
        if(!self::$_data){
            self::$_data[] = sprintf("[%s] %s %s %s",date('Y-m-d H:i:s'),md5(time()),$_SERVER['REQUEST_URI'],json_encode($_REQUEST,JSON_UNESCAPED_UNICODE));
        }
        self::$_data[] = sprintf("  [%s] %s %s",date('Y-m-d H:i:s'),$level,call_user_func_array('sprintf',$args));
    }

    /**
     * 刷新日志信息
     * @return void
     */
    static function flush(){
        if(self::$_data){
            $logconf = Conf::get('log');
            $driver = $logconf['driver'] ?? 'file';
            if($driver == 'file'){
                $dir = $logconf['file.dir'] ?? '/tmp';
                file_put_contents(sprintf('%s/%s%s_%s.log',$dir,Req::getApp(),(php_sapi_name() == 'cli' ? '_cli' : ''),date('Ymd')),implode("\r\n",self::$_data),FILE_APPEND);
            }
//            file_put_contents(sprintf('%s/%s%s_%s.log',Conf::get('log.path','/tmp'),Req::getApp(),(php_sapi_name() == 'cli' ? '_cli' : ''),date('Ymd')),implode("\r\n",self::$_data),FILE_APPEND);
            self::$_data = [];
        }

    }

    /**
     * 是否需要写入日志
     * @param $name
     * @return bool
     */
    private static function canWrite($name){
        static $_level = false;
        if($_level === false){
            $level = strtoupper(Conf::get('log.level','DEBUG'));
            $aa = self::$_levelMap[$level] ?? false;
            if($aa !== false){
                $_level = $aa;
            }elseif($level == 'ALL'){
                $_level = 9999;
            }else{
                $_level = 4;
            }
        }
        $vv = self::$_levelMap[strtoupper($name)] ?? 0;
        return $vv <= $_level;
    }
}