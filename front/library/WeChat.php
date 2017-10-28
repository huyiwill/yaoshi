<?php

namespace front\library;

/**
 * Class WeChat
 * @package front\library
 */
class WeChat
{
    private static $AppID = 'wx919b0ced8252ca0e';
    private static $AppSecret = '08e7828a624150adc4c466caa6422184';

    /* 根据code获取信息 */
    public static function getInfoByCode($code)
    {
        $url = sprintf(
        'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',
                self::$AppID,self::$AppSecret,$code
            );
        return json_decode(Tools::curlGET($url),true);
    }

    /* 刷新access_token */
    public static function refreshToken($refresh_token)
    {
        $url = sprintf(
            'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s',
            self::$AppID,$refresh_token
        );
        return json_decode(Tools::curlGET($url), true);
    }

    /* 微信用户信息 */
    public static function userInfo($accessToken, $openId)
    {
        $url = sprintf(
            'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s',
            $accessToken,$openId
        );
        return json_decode(Tools::curlGET($url), true);
    }
}