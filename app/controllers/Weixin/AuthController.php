<?php namespace Enstar\Controller\Weixin;

use \Controller;
use \Input;
use \Session;
use Enstar\Library\Weixin\WebAuth;
use Enstar\Library\Weixin\WxUser;
/**
 * @wx授权
 * User: zhengqian.zhu
 * Email: zhengqian.zhu@enstar.com
 * Date: 15-3-26
 * Time: 下午4:36
 */
class AuthController extends Controller
{
    private $openid;

    /**
     * @用户授权
     * @author: zhengqian.zhu@enstar.com
     */
    public function authBase()
    {
        $authInput = Input::only('code','state');
        if (isset($authInput['code']) && $authInput['state'] == 'enstar123456') {
            return $this->getOpenidOnLine($authInput['code']);
        }else{
            echo "Permission denied";
            exit;
        }
    }


    /**
     * @请求微信api，获取openid
     * @param $code
     * @author: zhengqian.zhu@enstar.com
     */
    public function getOpenidOnLine($code)
    {
        $this->openid = WebAuth::getOpenId($code);
        Session::put('openid', $this->openid);
        return $this->saveUserInfo();

    }

    /**
     * @获取用户信息
     * @author zhengqian.zhu
     */
    public function saveUserInfo()
    {
        $mq = new \ReadMQ();
        $userAuth = new WxUser($mq->getWeixinAccessToken(), $this->openid);
        $user = new \User();
        $user->saveWxUser($userAuth->getUserInfo());
        return $this->redirectRequestUrl();
    }

    /**
     * @跳转原始的requestUrl
     * @return mixed
     * @author: zhengqian.zhu@enstar.com
     */
    public function redirectRequestUrl()
    {
        return \Redirect::to(Session::get("request_url"));
    }
}