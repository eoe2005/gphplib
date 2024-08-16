<?php

namespace G\Logic\Controllers;

use G\Req;

trait AdminDotTrait
{
    /**
     * 打点配置列表
     * @return array
     */
    function dotConfsAction()
    {
        $where = [];
        $name = Req::input('name');
        if ($name) {
            $where['name[like]'] = $name;
        }
        $ret = [
            'total' => SysAdminModel::where($where)->count('*'),
            'list' => SysAdminModel::where($where)->page(Req::input('page', 1), Req::input('pageSize', 10))->fetchAll()
        ];
        return $ret;
    }

}