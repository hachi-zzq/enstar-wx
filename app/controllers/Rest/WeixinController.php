<?php namespace Enstar\Controller\Rest;

use Enstar\Library\Weixin\WeixinClient;
use Enstar\Utils\HttpClient;
use Illuminate\Support\Facades\Session;
use J20\Uuid\Uuid;
use \Lesson;
use \Input;
use \Config;
use \Cache;
use \ReadMQ;
use \User;
use \UserFavorite;
use \View;
use \Log;

class WeixinController extends BaseController
{

    const WX_MSG_TYPE_TEXT = 'text';//用户发送消息推送
    const WX_MSG_TYPE_EVENT = 'event';//事件推送
    const WX_MSG_EVENT_SUBSCRIBE = 'subscribe';//订阅事件
    const WX_MSG_EVENT_CLICK = 'CLICK';//订阅事件

    const WX_MENU_KEY_SEARCH_LESSON = 'SEARCH_LESSON';//搜索菜单的KEY
    const WX_MENU_KEY_ABOUT_XY = 'ABOUT_XY';//搜索菜单的KEY

    const WX_CLICK_EVENT_NO_RESPOND_TIMEOUT = 10;//针对微信菜单点击搜索事件无反应的超时时间

    const WX_QUIT_KEY = "q";//菜单事件退出开关

    private $mq;
    private $weixinClient;


    public function __construct()
    {
        $this->mq = new ReadMQ();
        $this->weixinClient = new WeixinClient();
    }


    /**
     * @用于服务器接入的测试
     * check token
     */
    public function checkSignature()
    {
        echo Input::get("echostr");
    }

    /**
     * 处理微信调用的请求
     */
    public function index()
    {
        $message = file_get_contents("php://input");
        Log::info($message);
        $message = simplexml_load_string($message, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($message) {
            $msgType = $message->MsgType;
            $fromUser = $message->FromUserName;
            $content = $message->Content;
            if ($msgType == self::WX_MSG_TYPE_TEXT) {
                if (Cache::get("menu:flag:" . $fromUser, '') == '' or strtolower($content) == self::WX_QUIT_KEY) {
                    //如果没有点击过菜单命令就发送信息，或者回复的是退出关键字，则退出菜单命令，清除菜单点击标记
                    Cache::forget("menu:flag:" . $fromUser);
                    return $this->respondTextMsg($fromUser, '请选择相应的功能菜单。');
                }
                //搜索课文
                return $this->searchLesson($message);
            }
            if ($msgType = self::WX_MSG_TYPE_EVENT) {
                if ($message->Event == self::WX_MSG_EVENT_SUBSCRIBE) {
                    //处理用户订阅事件
                    return $this->handleSubscribeEvent($message);
                }
                if ($message->Event == self::WX_MSG_EVENT_CLICK) {
                    if ($message->EventKey == self::WX_MENU_KEY_SEARCH_LESSON) {
                        //处理用户点击搜索菜单事件
                        return $this->handleClickSearchMenu($message);
                    }
                    if ($message->EventKey == self::WX_MENU_KEY_ABOUT_XY) {
                        //处理用户点击搜索菜单事件
                        return $this->handleClickAboutMenu($message);
                    }
                }
            }
        }
        return "";
    }


    /**
     * 搜索课文，返回搜索结果到微信
     * @return string
     */
    private function searchLesson($message)
    {
        $fromUser = $message->FromUserName;
        $content = $message->Content;
        $lessons = Lesson::search($content, 10);
        if (count($lessons) == 0) {
            return $this->respondTextMsg($fromUser, '没找到搜索结果，请重新回复关键字。');
        }
        $rsMsg = new \stdClass();
        $rsMsg->FromUserName = Config::get('weixin.id');
        $rsMsg->ToUserName = $fromUser;
        $rsMsg->items = array();
        foreach ($lessons as $l) {
            $item = new \stdClass();
            $item->title = $l->title;
            $item->description = substr($this->lessonFormatRender($l->asr_content), 0, 300);
            $item->picUrl = $this->getNceCover($l->book, empty($rsMsg->items));
            $item->url = route('lessonDetail', array('lesson_guid' => $l->guid));
            array_push($rsMsg->items, $item);
        }
        Cache::forget("menu:flag:" . $fromUser);
        return View::make('wx.respond_news')->with('message', $rsMsg);
    }

    /**
     * 搜索课文，返回搜索结果到微信
     * @return string
     */
    private function handleClickSearchMenu($message)
    {
        $fromUser = $message->FromUserName;
        Cache::put("menu:flag:" . $fromUser, self::WX_MENU_KEY_SEARCH_LESSON, self::WX_CLICK_EVENT_NO_RESPOND_TIMEOUT);
        return $this->respondTextMsg($fromUser, '请回复搜索关键字，退出搜索功能请回复q。');
    }


    /**
     * 关于菜单
     * @param $message
     * @return mixed
     * Author: Haiming.Wang<haiming.wang@enstar.com>
     */
    private function handleClickAboutMenu($message)
    {
        $fromUser = $message->FromUserName;
        return $this->respondTextMsg($fromUser, Config::get('weixin.about'));
    }


    /**
     * 处理关注事件,写入用户信息，回复问候语
     * @return string
     */
    private function handleSubscribeEvent($message)
    {
        $fromUserName = $message->FromUserName;
        $wxUserInfo = $this->weixinClient->getUserInfoByOpenId($fromUserName, $this->mq->getWeixinAccessToken());
        if (!$wxUserInfo) {
            return null;
        }
        $user = User::getByOpenId($fromUserName);
        if (!$user) {
            $user = new User();
            $user->openid = $fromUserName;
        }
        $user->subscribe = $wxUserInfo->subscribe;
        $user->openid = $fromUserName;
        $user->nickname = $wxUserInfo->nickname;
        $user->sex = $wxUserInfo->sex;
        $user->language = $wxUserInfo->language;
        $user->city = $wxUserInfo->city;
        $user->province = $wxUserInfo->province;
        $user->country = $wxUserInfo->country;
        $user->headimgurl = $wxUserInfo->headimgurl;
        $user->unionid = null;
        $user->save();
        return $this->respondTextMsg($fromUserName, Config::get('weixin.greetings'));
    }

    /**
     * 收藏
     * @param $lessonGuid
     * @return array
     */
    public function addFavoriteLesson($lessonGuid)
    {
        $openid = Session::get('openid');
        $user = User::getByOpenId($openid);
        if (!$openid or !$user) {
            return $this->encodeResult('20102', 'No permission');
        }
        $lesson = Lesson::getByGuid($lessonGuid);
        if (!$lesson) {
            return $this->encodeResult('20002', 'Not found');
        }

        if (!UserFavorite::isExist($lesson->id, $user->id)) {
            $uf = new UserFavorite();
            $uf->user_id = $user->id;
            $uf->lesson_id = $lesson->id;
            $uf->save();
        }
        return $this->encodeResult("10000", 'success');
    }


    /**
     * @取消收藏
     * @param $lessonGuid
     * @author: zhengqian.zhu@enstar.com
     */
    public function unFavoriteLesson($lessonGuid)
    {
        $openid = Session::get('openid');
        $user = User::getByOpenId($openid);
        if (!$openid or !$user) {
            return $this->encodeResult('20102', 'No permission');
        }
        $lesson = Lesson::getByGuid($lessonGuid);
        if (!$lesson) {
            return $this->encodeResult('20002', 'Not found');
        }

        if (!$fav=UserFavorite::isExist($lesson->id, $user->id)) {
            return $this->encodeResult('20003', 'Not Favorite');
        }

        if(UserFavorite::where('lesson_id',$lesson->id)->where('user_id',$user->id)->delete()){
            return $this->encodeResult('10000', 'success');
        }

    }


    /**
     * 分享课文
     * @param $lessonGuid 课文guid
     */
    public function shareLesson($lessonGuid)
    {

    }

    /**
     * 获取收藏的课文
     */
    public function getFavoriteLesson()
    {

    }


    /**
     * 获取阅读过的课文列表
     */
    public function getReadingLessonHistory()
    {

    }

    /**
     * 获取课文得分历史
     * * @param $lessonGuid 课文guid
     */
    public function getReadingScoreHistory($lessonGuid)
    {

    }


    /**
     * 回复普通信息
     * @param $toUserName
     * @param $content
     * @param null $fromUserName
     * @return mixed
     */
    private function respondTextMsg($toUserName, $content, $fromUserName = null)
    {
        $rsMsg = new \stdClass();
        $rsMsg->FromUserName = $fromUserName ? $fromUserName : Config::get('weixin.id');
        $rsMsg->ToUserName = $toUserName;
        $rsMsg->Content = $content;
        return View::make('wx.respond_text')->with('message', $rsMsg);
    }


    private function getNceCover($book, $big = false)
    {
        if($big){
            return asset('/static/img/nce_big_' . substr($book->book_key, 1) . '.png');
        }
        return asset('/static/img/nce_square_' . substr($book->book_key, 1) . '.png');
    }

    /**
     * @param $str
     * @return mixed
     * @author: haiming.wang@enstar.com
     */
    private function lessonFormatRender($str)
    {
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);

        return $str;
    }
}