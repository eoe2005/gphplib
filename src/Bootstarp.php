<?php

namespace G;
define('DS',DIRECTORY_SEPARATOR);
define('APP_ROOT','');
class Bootstarp
{
    static function Run(){
        date_default_timezone_set('Asia/Shanghai');
        register_shutdown_function(function (){
            Log::flush();
        });
        if(php_sapi_name() == 'cli'){
            self::cli();
        }else{
            self::web();
        }
    }

    private static function web(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN');
        header('Access-Control-Expose-Headers: Authorization, authenticated');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, OPTIONS');
        header('Access-Control-Allow-Credentials: true');

        $app = Req::getApp();
        $path = $_SERVER['REQUEST_URI'];
        $path = substr($path,1);
        $paths = explode('/',$path);

        $clsPre = '\\Apps\\Domain\\'.$app.'\\';
        $controllerCls = $clsPre .'Controllers\\'. ucfirst($paths[0] ?? '').'Controller';
        $actName = ($paths[1] ?? '').'Action';
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

        $jobMap = Conf::get('jobs',[]);
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