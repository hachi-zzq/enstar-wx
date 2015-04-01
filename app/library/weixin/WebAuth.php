<?php
namespace Enstar\Library\Weixin;

use Enstar\Library\Help\Tool;

class WebAuth
{

    public static  function getOpenId($code){
        $appid = \Config::get('weixin.appID');
        $appsecret = \Config::get('weixin.appsecret');
        $api = sprintf(\Config::get('weixin.api.web_auth'),$appid,$appsecret,$code);
        try{
            $ret = Tool::getCurl($api,30);
            if($ret->httpCode != 200 or $ret->error or $ret->errno){
                throw new \Exception($ret->error,$ret->errno);
            }
        }catch (\Exception $e){
            echo $e->getMessage();
            die();
        }

        $obj = json_decode($ret->content);
        if(isset($obj->errcode)){
            die(sprintf("wx getAccess error,errcode:%d ,errmsg:%s",$obj->errcode,$obj->errmsg));
        }

        return $obj->openid;
    }

}
