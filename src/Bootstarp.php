<?php

namespace G;
use G\Cmd\CmdConf;

define('DS',DIRECTORY_SEPARATOR);
//APP_ROOT 应用根目录
//APP_NAME 应用名称
//APP_CONTROLLER
//APP_ACTION
class Bootstarp
{
    static function Run(){
        date_default_timezone_set('Asia/Shanghai');
        register_shutdown_function(function (){
            Log::flush();
        });
        if(php_sapi_name() == 'cli'){
            define('APP_ROOT',realpath($_SERVER['DOCUMENT_ROOT']));
            self::cli();
        }else{
            define('APP_ROOT',dirname(realpath($_SERVER['DOCUMENT_ROOT'])));
            self::web();
        }
    }

    private static function web(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        header('Access-Control-Expose-Headers: Authorization, authenticated');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, OPTIONS');
        header('Access-Control-Allow-Credentials: true');

        $path = $_SERVER['REQUEST_URI'];
        $path = ltrim($path,'/');
        $i = strpos($path,'.');
        if($i !== false){
            $path = substr($path,0,$i);
        }
        $paths = explode('/',$path);
        if(Conf::get('app.is_domain') == '1'){
            $domain = $_SERVER['HTTP_HOST'];
            $ds = Conf::get('domain',[]);
            foreach ($ds as $k => $v) {
                if(strpos($domain,$k) === true){
                    define('APP_NAME',ucfirst($v));
                    break;
                }
            }
            if(!defined('APP_NAME')){
                define('APP_NAME','Www');
            }
        }else{
            $ad = ucfirst($paths[0]);
            if(!$ad){
                define('APP_NAME','Www');
            }else{
                if(is_dir(APP_ROOT.DS.'App/Apps/'.$ad)){
                    define('APP_NAME',$ad);
                }
                $paths = array_slice($paths,1);
            }
        }

        $clsPre = '\\Apps\\Domain\\'.APP_NAME.'\\';
        $contname = $paths[0] ?? 'index';
        if(!$contname){
            $contname = 'index';
        }
        $acname = $paths[1] ?? 'index';
        if(!$acname){
            $acname = 'index';
        }
        $controllerCls = $clsPre .'Controllers\\'. ucfirst($contname).'Controller';
        $actName = $acname.'Action';

        define('APP_CONTROLLER',ucfirst($contname));
        define('APP_ACTION',$acname);

        $pargs = isset($paths[2]) ? array_slice($paths,2) : [];

        $middleCls = $clsPre.'Middleware';

        if(class_exists($middleCls)){
            $mc = new  $middleCls;
            try{
                $mc->before();
                if(class_exists($controllerCls)){
                    $cc = new $controllerCls();
                    if(method_exists($cc,$actName)){
                        $ret = call_user_func_array([$cc,$actName],$pargs);
                        $mc->after($ret);
                    }else{
                        throw new \Exception('接口错误1',500);
                    }
                }else{
                    throw new \Exception('接口错误2'.$controllerCls,500);
                }
            }catch (\Exception $e){
                $mc->after($e);
            }
        }
        try {
            if(class_exists($controllerCls)){
                $cc = new $controllerCls();
                if(method_exists($cc,$actName)){
                    $ret = call_user_func_array([$cc,$actName],$pargs);
                    return self::json(200,'success',$ret);
                }else{
                    return self::json(404,'接口错误11',[]);
                }
            }else{
                return self::json(404,'接口错误22',[]);
            }
        }catch (\Exception $e){
            return self::json($e->getCode() ?: 500,'系统异常',[]);
        }

    }
    private static function json($code,$msg,$data){
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ]);
        die();
    }

    private static function cli(){
        $jobName =  strtolower($_SERVER['argv'][1] ?? '');
        $args = [];
        if($jobName){
            $args = array_slice($_SERVER['argv'],2);
        }

        $jobMap = CmdConf::getConf(Conf::get('jobs',[]));
        $jonCls = $jobMap[$jobName] ?? '';
        if(!$jonCls){
            echo "命令：\n";
            foreach ($jobMap as $cmd => $cls){
                echo sprintf("  %s\t%s\n",$cmd,(new $cls)->desc());
            }
            return;
        }else{
            $lockFile = sprintf('/tmp/%s.lock',$jobName);
            if(file_exists($lockFile)){
                Log::Err('%s 正在执行中...',$jobName);
                return;
            }

            $shutdownFunc = function () use($lockFile,$jobName){
                Log::Debug("%s执行完成\n\n",$jobName);
                unlink($lockFile);
            };
            file_put_contents($lockFile,'1');
            register_shutdown_function($shutdownFunc);
            pcntl_signal(SIGINT,$shutdownFunc);
            Log::Debug('%s 开始执行',$jobName);
            $job = new $jonCls;
            $job->execute($args);

        }

    }
}