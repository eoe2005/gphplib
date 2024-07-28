<?php

namespace G;

class Controller
{
     function getPage(){
        $ret = Req::input('page',1);
        if(!$ret){
            $ret = 1;
        }
        return $ret;
    }
     function getpageSize(){
        $ret = Req::input('pageSize',10);
        if(!$ret){
            $ret = 10;
        }
        return $ret;
    }

    /**
     * 检查是否是手机号
     * @param $mobile
     * @param $error
     * @return bool
     * @throws \Exception
     */
    function checkMobile($mobile,$error = '')
    {
        if(preg_match("/1\d{10}/",$mobile)){
            return true;
        }
        if($error){
            throw new \Exception($error,403);
        }
        return false;
    }


}