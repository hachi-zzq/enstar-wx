<?php namespace Enstar\Library\Weixin;

use Enstar\Utils\HttpClient;
use Illuminate\Support\Facades\Log;

/**
 * Created by PhpStorm.
 * User: mynpc
 * Date: 2015/3/4
 * Time: 14:41
 */
class WeixinClient
{
    const API_CREATE_MENU = 'https://api.weixin.qq.com/cgi-bin/menu/create';
    const API_SET_INDUSTRY = 'https://api.weixin.qq.com/cgi-bin/template/api_set_industry';
    const API_GET_ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
    const API_GET_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
    const API_GET_USER_INFO = 'https://api.weixin.qq.com/cgi-bin/user/info';
    const API_GET_MEDIA = 'http://file.api.weixin.qq.com/cgi-bin/media/get';

    const API_SEND_TEMPLATE_MESSAGE = 'https://api.weixin.qq.com/cgi-bin/message/template/send';

    const ACCESS_TOKEN_EXPIRES_IN = 7200;

    private $httpClient;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
    }


    /**
     * 设置微信菜单
     * @param $data_string
     * @param $access_token
     * @param string $api
     * @return string
     */
    public function setMenu($data_string, $access_token, $api = self::API_CREATE_MENU)
    {
        return $this->doPost($data_string, $access_token, $api);
    }

    /**
     * 设置行业
     * @param $data_string
     * @param $access_token
     * @param string $api
     * @return string
     */
    public function setIndustry($data_string, $access_token, $api = self::API_SET_INDUSTRY)
    {
        return $this->doPost($data_string, $access_token, $api);
    }

    /**
     * 发送模板信息
     * @param $data_string
     * @param $access_token
     * @param string $api
     * @return string
     */
    public function sendTemplateMessage($data_string, $access_token, $api = self::API_SEND_TEMPLATE_MESSAGE)
    {
        return $this->doPost($data_string, $access_token, $api);
    }


    /**
     * 通过openId获取用户信息
     * @param $openId
     * @param $access_token
     * @param string $api
     * @param string $lang
     * @return mixed|null|string
     */
    public function getUserInfoByOpenId($openId, $access_token, $api = self::API_GET_USER_INFO, $lang = 'zh_CN')
    {
        $url = "$api?access_token=$access_token&openId=$openId&lang=$lang";
        $rtJson = $this->httpClient->get_contents($url);
        $rtJson = json_decode($rtJson);
        if (array_key_exists('errcode', $rtJson)) {
            Log::info("Get user info error.[openid:$openId,errormsg:$rtJson->errmsg]");
            return null;
        }
        return $rtJson;
    }

    /**
     * @param $data_string
     * @param $access_token
     * @param $api
     * @return string
     */
    private function doPost($data_string, $access_token, $api)
    {
        $url = $api . '?access_token=' . $access_token;
        return $this->httpClient->post($url, $data_string);
    }

    /**
     * 获得access_token
     * @return null
     */
    public function applyWeixinAccessToken($appid, $secret, $api = self::API_GET_ACCESS_TOKEN)
    {
        $url = "$api&appid=$appid&secret=$secret";
        $rtJson = $this->httpClient->get_contents($url);
        $rtJson = json_decode($rtJson);
        if (array_key_exists('access_token', $rtJson)) {
            return $rtJson->access_token;
        }

        return null;
    }

    /**
     * 获得access_token
     * @return null
     */
    public function applyJsapiTicket($access_token, $api = self::API_GET_TICKET)
    {
        $url = "$api?access_token=$access_token&type=jsapi";
        $rtJson = $this->httpClient->get_contents($url);
        $rtJson = json_decode($rtJson);
        if (array_key_exists('ticket', $rtJson)) {
            return $rtJson->ticket;
        }
        return null;
    }

    /**
     * 下载媒体文件
     * @param $dir
     * @param $fileName
     * @param $mediaId
     * @param $access_token
     * @param string $api
     */
    public function downloadMedia($dir, $fileName, $mediaId, $access_token, $api = self::API_GET_MEDIA)
    {
        $url = $api . '?access_token=' . $access_token . '&media_id=' . $mediaId;
        $this->httpClient->download($url, $dir, $fileName);
    }

    /**
     * @param $jsapiTicket
     * @param $url
     * @param $noncestr
     * @param $timestamp
     * @return string
     */
    public function getSignature($jsapiTicket, $url, $noncestr, $timestamp)
    {
        $str = "jsapi_ticket=$jsapiTicket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
        return sha1($str);
    }
}