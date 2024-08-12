<?php

namespace G\Logic\Models;

use G\Model;

class SysAdminModel extends Model
{
    /**
     * 生成密码
     * @param $pass
     * @param $sign
     * @return string
     */
    static function buildPass($pass, $sign)
    {
        return md5($sign . '_' . $pass);
    }

    /**
     * 生成加盐信息
     * @return string
     */
    static function genrateSign()
    {
        return sprintf('%d', rand(100000, 999999));
    }
}