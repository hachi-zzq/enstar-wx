<?php
namespace Enstar\Controller\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use \Request;
use \AdminLog;
use \URL;
use \Config;
use \Auht;
use Enstar\Library\MQ;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-19 下午3:47
 */

class BaseController extends \BaseController
{

    public function __construct()
    {
        if(Config::get('app.adminLog')){
            $objLog = new AdminLog();
            $objLog->user_id = Auth::check() ? Auth::user()->id : 0;
            $objLog->user_agent = $_SERVER["HTTP_USER_AGENT"];
            $objLog->route = Request::fullUrl();
            $objLog->request_type = Request::path();
            $objLog->client_ip = Request::ip();
            $objLog->request_data = !empty($_REQUEST) ? json_encode($_REQUEST) : '';
            $objLog->save();
        }

        //redis mq
        $this->BaseHanderMQ();

    }

    /**
     * #lesson状态管理
     * @param null $statusCode
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function handerLessonStatus($statusCode=null){
        $status = '';
        $statusClass = '';
        if($statusCode !== null){
            switch($statusCode){
                case 0:
                    $status = '未匹配';
                    $statusClass = 'label-warning';
                    break;
                case -1;
                    $status = '匹配失败';
                    $statusClass = 'label-danger';
                    break;
                case 1:
                    $status = '正在匹配';
                    $statusClass = 'label-success';
                    break;
                case 2:
                    $status = '匹配成功';
                    $statusClass = 'label-primary';
                    break;
                case -2:
                    $status = '匹配完成，不可用';
                    $statusClass = 'label-danger';
                    break;
                default :

            }
        }
        return array(
            'status'=>$status,
            'statusClass'=>$statusClass);
    }

    public function BaseHanderMQ()
    {
        //遍历redis队列分析好的报告，入库
        $redis = MQ::getInstance();
        $redis->popLessonAnalyzeRet();
    }


    /**
     * @清理过期的xls
     * @autho zhengqian.zhu@enstar.com
     */
    public static function clearXls()
    {
        $dir = public_path('data/card_xls/');
        $dir = preg_replace("/\\\/","/",$dir);
        $dh = opendir($dir);
        while(($file=readdir($dh)) !== false){
            if($file == '.' or $file == '..' or $file=='.gitkeep')
                continue;
            if(time()-filectime($dir.$file) > 3600){
                unlink($dir.$file);
            }
        }
    }

}

