<?php

namespace G;

class Template
{
    /**
     * 解析模版内容
     * @param $tpl
     * @param $args
     * @return string
     */
    static function fetch($tpl,$args = []){

        $file = $tpl.'.php';
        if(!file_exists($file)){
            throw new \Exception('模版错误',500);
        }
        ob_start();
        extract($args);
        include $file;
        return ob_get_clean();
    }

    /**
     * 解析模版内容
     * @param $layout
     * @param $tpl
     * @param $args
     * @return string
     */
    static function fetchLayout($layout,$tpl,$args = []){
        $args['__LAYOUT_CONTENT__'] = self::fetch($tpl);
        return self::fetch($layout,$args);
    }

    /**
     * 显示模版
     * @param $tpl
     * @param $args
     * @return void
     */
    static function display($tpl,$args = []){
        echo self::fetch($tpl,$args);
    }

    /**
     * 展示模版
     * @param $layout
     * @param $tpl
     * @param $args
     * @return void
     */
    static function displayLayout($layout,$tpl,$args = []){
        echo self::fetchLayout($layout,$tpl,$args);
        die();
    }
}