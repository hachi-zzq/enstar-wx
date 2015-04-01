<?php
namespace Enstar\Library\LeanCloudService;

use Enstar\Library\LeanCloudService\Base;

/**
 * 推送通知操作类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class Push extends Base
{
    public static function send($userId, $content = "Hello Enstar")
    {
        $data = array(
            "where" => array(
                "objectId" => $userId
            ),
            "data" => array(
                "alert" => $content
            )
        );
        $url = 'https://leancloud.cn/1.1/push';
        $push = new self();
        return $push->xCurl($url, $data);
    }
}
