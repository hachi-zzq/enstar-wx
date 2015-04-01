<?php
namespace Enstar\Library\LeanCloudService;

use Enstar\Library\LeanCloudService\Base;

/**
 * 短信操作类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class Sms extends Base
{
    public static function verify($mobile, $code)
    {
        $url = "https://leancloud.cn/1.1/verifySmsCode/$code?mobilePhoneNumber=$mobile";
        $sms = new self();
        return $sms->xCurl($url);
    }

    public static function send($mobile)
    {
        $data = json_encode(array('mobilePhoneNumber' => $mobile));
        $url = 'https://leancloud.cn/1.1/requestSmsCode';
        $sms = new self();
        return $sms->xCurl($url, $data);
    }
}
