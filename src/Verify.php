<?php

namespace G;

class Verify
{
    /**
     * 检查参数是否正确
     * @param $data
     * @param $rule
     * @return void
     */
    static function check($data,$rule){
        foreach ($rule as $key => $val){
            $rules = $val['rules'];
            if (is_string($val['rules'])){
                $keys = explode('|',$val['rules']);
                foreach ($keys as $vv){
                    $rules[$vv] = $val['msg'];
                }
            }
            foreach ($rules as $k => $msg){
                $pval = $data[$key] ?? '';
                if($k == 'required'){
                    if(!$pval){
                        throw new \Exception($msg,403);
                    }
                }elseif($k == 'number'){
                    if(!is_numeric($pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif($k == 'date'){
                    if(!preg_match("/\d{4}-\d{2}-\d{2}/",$pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif($k == 'time'){
                    if(!preg_match("/\d{2}:\d{2}:\d{2}/",$pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif($k == 'datetime'){
                    if(!preg_match("/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/",$pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("gt:",$k)){
                    $sub = substr($pval,3);
                    if($sub <= $pval){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("gte:",$k)){
                    $sub = substr($pval,4);
                    if($sub < $pval){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("lt:",$k)){
                    $sub = substr($pval,3);
                    if($sub >= $pval){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("lte:",$k)){
                    $sub = substr($pval,4);
                    if($sub > $pval){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("maxlen:",$k)){
                    $sub = substr($pval,7);
                    if($sub < strlen($pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("minlen:",$k)){
                    $sub = substr($pval,7);
                    if($sub > strlen($pval)){
                        throw new \Exception($msg,403);
                    }
                }elseif(str_starts_with("in:",$k)){
                    $sub = substr($pval,3);
                    if(!in_array($pval,explode(',',$sub))){
                        throw new \Exception($msg,403);
                    }
                }
            }
        }
    }
}