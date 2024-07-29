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
     function getPageSize(){
        $ret = Req::input('pageSize',10);
        if(!$ret){
            $ret = 10;
        }
        return $ret;
    }



    /**
     * 显示模版
     * @param $args
     * @param $tpl
     * @return void
     */
    function display($args = [],$tpl = '')
    {
        if($tpl){
            Template::display(APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/'.$tpl,$args);
        }else{
            Template::display(APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/'.APP_CONTROLLER.'/'.APP_ACTION,$args);
        }
    }

    /**
     * 显示模版
     * @param $args
     * @param $tpl
     * @param $layout
     * @return void
     */
    function layout($args = [],$tpl = '',$layout = '')
    {
        if($layout){
            $layout = APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/'.$layout;
        }else{
            $layout = APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/Layout/index';
        }
        if($tpl){
            Template::displayLayout($layout,APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/'.$tpl,$args);
        }else{
            Template::displayLayout($layout,APP_ROOT.'/App/Apps/'.APP_NAME.'/Views/'.APP_CONTROLLER.'/'.APP_ACTION,$args);
        }
    }


}