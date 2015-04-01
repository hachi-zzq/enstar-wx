<?php

use Enstar\Library\Weixin\WeixinClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

/**
 * 队列处理类
 * Class ReadMQ
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class ReadMQ
{
    private $redis = '';
    public $len = 0;
    const REDIS_KEY = "enstar:xy:read:analyze:input";
    const REDIS_RETRY_KEY = "enstar:xy:read:analyze:retry";

    const REDIS_KEY_WX_ACCESS_TOKEN = "enstar:xy:access_token";
    const REDIS_KEY_WX_JSAPI_TICKET = "enstar:xy:jsapi_ticket";

    public $retry_len = 0;

    public function __construct()
    {
        $this->redis = Redis::connection();
    }

    /**
     * 消息提交入队
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $key
     * @param $data
     * @return boolean
     */
    public function inQueue($key, $data = null)
    {
        if (is_array($data)) {
            return false;
        }

        $redisLog = new RedisLog();
        $redisLog->key = $key;
        $redisLog->content = $data;
        $redisLog->save();

        try {
            $this->redis->lpush($key, $data);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }


    /**
     * @获取到队列中所有数据
     * @param null
     * @author zhengqian.zhu
     */
    public function getQueueList()
    {
        $arr = array();
        $len = $this->redis->llen(self::REDIS_KEY);
        $this->len = $len;
        while ($len > 0) {
            array_push($arr, $this->redis->lindex(self::REDIS_KEY, $len - 1));
            $len--;
        };

        return $arr;

    }

    /**
     * @获取重试队列
     * @param null
     * @return array
     * @author zhengqian.zhu
     */
    public function getRetryList()
    {
        $arr = array();
        $len = $this->redis->llen(self::REDIS_RETRY_KEY);
        $this->retry_len = $len;
        while ($len > 0) {
            array_push($arr, $this->redis->lindex(self::REDIS_RETRY_KEY, $len - 1));
            $len--;
        };

        return $arr;
    }


    /**
     * 获得redis中微信的access_token
     * @return null
     */
    public function getWeixinAccessToken()
    {
        $accessToken = $this->redis->get(self::REDIS_KEY_WX_ACCESS_TOKEN);
        if (!$accessToken) {
            $wxClient = new WeixinClient();
            $accessToken = $wxClient->applyWeixinAccessToken(Config::get('weixin.appID'), Config::get('weixin.appsecret'));
            $this->redis->setex(self::REDIS_KEY_WX_ACCESS_TOKEN, WeixinClient::ACCESS_TOKEN_EXPIRES_IN - 200, $accessToken);
        }
        return $accessToken;
    }

    /**
     * 获得redis中微信的jsapi_ticket
     * @return null
     */
    public function getWeixinJsapiTicket()
    {
        $jsapiTicket = $this->redis->get(self::REDIS_KEY_WX_JSAPI_TICKET);
        if (!$jsapiTicket) {
            $wxClient = new WeixinClient();
            $accessToken = $this->getWeixinAccessToken();
            $jsapiTicket = $wxClient->applyJsapiTicket($accessToken,Config::get('weixin.api.get_jsapi_ticket'));
            $this->redis->setex(self::REDIS_KEY_WX_JSAPI_TICKET, WeixinClient::ACCESS_TOKEN_EXPIRES_IN - 200, $jsapiTicket);
        }
        return $jsapiTicket;
    }
}
