<?php

namespace G\Sdk;


class WeChatSdk
{
    /**
     * 获取微信账号信息
     * @param $code
     * @param $conf
     * @return {
     * "openid": "OPENID",
     * "nickname": "NICKNAME",
     * "sex": 1,
     * "province": "PROVINCE",
     * "city": "CITY",
     * "country": "COUNTRY",
     * "headimgurl": "https://thirdwx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0",
     * "privilege": ["PRIVILEGE1", "PRIVILEGE2"],
     * "unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     * }
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @link https://developers.weixin.qq.com/doc/oplatform/Mobile_App/WeChat_Login/Authorized_API_call_UnionID.html
     */
    static function getUserInfo($code,$conf){
        $http = new \GuzzleHttp\Client();
        $ret = $http->get(sprintf('https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',$conf['appid'],$conf['secret'],$code));
        if($ret->getStatusCode() != 200){
            throw new \Exception('获取token失败',403);
        }
        $jsonData = json_decode($ret->getBody(),true);
        $errcode = $jsonData['errcode'] ?? 0;
        if($errcode){//错误
            throw new \Exception($jsonData['errmsg'],403);
        }
        $access_token = $jsonData['access_token'] ?? '';
        if(!$access_token){
            throw new \Exception('获取token失败',403);
        }
        $retUserInfo = $http->get(sprintf('https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s',$access_token,$jsonData['openid']));
        if($retUserInfo->getStatusCode() != 200){
            throw new \Exception('获取用户信息失败',403);
        }
        $retData = json_decode($retUserInfo->getBody(),true);
        $errcode = $retData['errcode'] ?? 0;
        if($errcode){//错误
            throw new \Exception($retData['errmsg'],403);
        }
        return $retData;

    }
}