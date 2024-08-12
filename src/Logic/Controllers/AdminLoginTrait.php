<?php

namespace G\Logic\Controllers;

use G\Logic\Models\SysAdminModel;
use G\Req;

trait AdminLoginTrait
{
    /**
     * 登录
     * @return void
     */
    function loginAction(){
        $name = Req::input('login_name');
        $pass = Req::input('pass');
        if (!$name || !$pass) {
            throw new \Exception('账号或者密码错误', 403);
        }
        $row = SysAdminModel::where(['login_name' => $name])->fetch();
        if (!$row || $row['passwd'] != SysAdminModel::buildPass($pass, $row['sign'])) {
            throw new \Exception('账号或者密码错误', 403);
        }
        if ($row['status'] != 0) {
            throw new \Exception('账号禁止登录', 403);
        }
        $_SESSION['admin_id'] = $row['id'];
        return [
            'admin_id' => $row['id'],
            'nick_name' => $row['nick_name'],
            'avatar' => $row['avatar'],
        ];
    }

    /**
     * 退出登录
     * @return void
     */
    function logoutAction()
    {
        session_destroy();
        return '';
    }
}