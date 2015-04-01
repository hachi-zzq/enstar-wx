<?php namespace Enstar\Library\Weixin;

use Enstar\Library\Help\Tool;
/**
 * @更加openid获取用户信息
 * Class WxUser
 * @author zhengqian.zhu
 * @date 2015-03-03
 */
class WxUser
{
    private $openid;

    private $access_token;

    private $lang;

    public function __construct($access_token,$openid,$lang='zh_CN')
    {
        $this->access_token = $access_token;
        $this->openid = $openid;
        $this->lang = $lang;
    }

    /**
     * @对外接口，获取用户信息
     * @param null
     * @return mixed
     */
    public function getUserInfo()
    {
        $userinfo = \Cache::get($this->openid);
        if ( ! $userinfo){
            \Cache::put($this->openid,$this->getUserInfoOnLine(),60*24);
        }

        return \Cache::get($this->openid);
    }

    /**
     * @在线拉去用户接口
     * @return mixed
     * @throws Exception
     */
    private function getUserInfoOnLine()
    {
        $api = sprintf(\Config::get('weixin.api.get_user_info'),$this->access_token,$this->openid,$this->lang);
        try{
            $ret = Tool::getCurl($api,30);
            if($ret->httpCode !== 200 or $ret->error or $ret->errno){
                throw new \Exception("curl get user info error");
            }
        }catch (\Exception $e){
            echo $e->getMessage();
            die();
        }

        return $ret->content;
    }


}