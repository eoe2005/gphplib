<?php

namespace G\Logic\Controllers;

use G\Logic\Models\SysAdminModel;
use G\Logic\Models\SysLinksModel;
use G\Logic\Services\SysConfSvc;
use G\Req;
use G\Verify;

trait AdminSysTrait
{

    /**
     * 获取系统配置
     * @return array|mixed|string
     */
    function getConfAction(){
        return SysConfSvc::getConf(Req::input('key'),Req::input('defval',[]));
    }

    /**
     * 保存系统配置
     * @return true
     * @throws \Exception
     */
    function saveConfAction(){
        return SysConfSvc::setConf(Req::input('key'),Req::input('data',[]));
    }
    /**
     * 友情链接
     * @return void
     */
    function linksAction()
    {
        $name = Req::input('name','');
        $targetType = Req::input('type',0);
        $where = [];
        if($name){
            $where['name[like]'] = $name;
        }
        if($targetType){
            $where['target_type'] = $targetType;
        }
        $ret = [
            'total' => SysLinksModel::where($where)->count('*'),
            'list' => SysLinksModel::where($where)->page(Req::input('page',1),Req::input('pageSize',10))->fetchAll(),
            'target_types' => [
                ['id' => 10,'name' => '网站'],
                ['id' => 20,'name' => 'ANDROID'],
                ['id' => 30,'name' => 'IOS'],
            ]
        ];
        return $ret;
    }

    /**
     * 保存友情链接
     * @return void
     */
    function saveLinkAction(){
        $data = Req::input('data',[]);
        Verify::check($data,[
            'target_type' => ['rules' => 'required','msg' => '类型错误'],
            'name' => ['rules' => 'required','msg' => '类型错误'],
            'img' => ['rules' => 'required','msg' => '类型错误'],
            'links' => ['rules' => 'required','msg' => '类型错误'],
        ]);
        if(SysLinksModel::save($data)){
            return 'ok';
        }
        throw new \Exception('保存失败');
    }

    /**
     * 获取管理员列表
     * @return void
     */
    function adminsAction()
    {
        $name = Req::input('name','');
        $isDeny = Req::input('is_deny','');
        $where = [];
        if($name){
            $where['nick_name[like]'] = $name;
        }
        if($isDeny != ''){
            $where['status'] = $isDeny;
        }
        $ret = [
            'total' => SysAdminModel::where($where)->count('*'),
            'list' => SysAdminModel::where($where)->page(Req::input('page',1),Req::input('pageSize',10))->fetchAll()
        ];
        return $ret;
    }

    /**
     * 保存管理员
     * @return void
     */
    function saveAdminAction()
    {
        $data = Req::input('data',[]);
        Verify::check($data,[
            'login_name' => ['rules' => 'required','msg' => '参数错误'],
            'nick_name' => ['rules' => 'required','msg' => '参数错误'],
        ]);
        if($data['passwd']){
            $data['sign'] = SysAdminModel::genrateSign();
            $data['passwd'] = SysAdminModel::buildPass($data['passwd'],$data['sign']);
        }
        if(SysLinksModel::save($data)){
            return 'ok';
        }
        throw new \Exception('保存失败');
    }

    /**
     * 修改密码
     * @return mixed
     * @throws \Exception
     */
    function changePassAction(){
        $oldPass = Req::input('old');
        $pass = Req::input('pass');
        $adminId = $_SESSION['admin_id'];
        if(!$oldPass || !$pass){
            throw new \Exception('参数错误',403);
        }
        $row = SysAdminModel::find($adminId);
        if($row['passwd'] != SysAdminModel::buildPass($oldPass,$row['sign'])){
            throw new \Exception('旧密码错误',403);
        }
        $sign = SysAdminModel::genrateSign();
        return SysAdminModel::update([
            'passwd' => SysAdminModel::buildPass($pass,$sign),
            'sign' => $sign
        ],['id' => $adminId]);
    }
}