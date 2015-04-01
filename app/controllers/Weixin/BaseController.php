<?php namespace Enstar\Controller\Weixin;

use \Input;
use \Session;
use \User;
use \stdClass;
use \Config;
use Enstar\Library\Weixin\WebAuth;
use Enstar\Library\Weixin\WxUser;
use Enstar\Library\Weixin\WeixinClient;
use Enstar\Library\Help\Tool;

/**
 * Enstar基类
 * User: zhengqian.zhu
 * Date: 15-3-4
 * Time: 下午1:50
 */
class BaseController extends \BaseController
{

    /**
     * @通过用户openid获取ID
     * @param null $openId
     * @return int
     */
    public function getUserIdFromOpenId($openId=null)
     {
        if(empty($openId)) $openId = Session::get('openid');
        $user = User::where('openid',$openId)->first();
         return empty($user) ? 0 :$user->id;
     }


    /**
     * 获得微信jsapi的配置信息
     * @return stdClass
     */
    public function getJsapiConfig()
    {
        $mq = new \ReadMQ();
        $jsapiTicket = $mq->getWeixinJsapiTicket();
        $url = \Request::fullUrl();
        $noncestr = Tool::getRandChar(16);
        $timestamp = time();
        $weixinClient = new WeixinClient();
        $signature = $weixinClient->getSignature($jsapiTicket, $url, $noncestr, $timestamp);
        $config = new stdClass();
        $config->jsapiTicket = $jsapiTicket;
        $config->url = $url;
        $config->noncestr = $noncestr;
        $config->timestamp = $timestamp;
        $config->signature = $signature;
        $config->appid  =Config::get('weixin.appID');
        return $config;
    }
}