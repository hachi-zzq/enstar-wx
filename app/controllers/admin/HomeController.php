<?php
namespace Enstar\Controller\Admin;

use Illuminate\Support\Facades\Redis;
use \View;
use \Request;
use \Input;
use \Validator;
use \Lesson;
use \Enstar\Library\MQ;
use \User;
use \DB;
use \School;
use \Admin;
use \Reading;
use \Config;
use \Device;
use \Cache;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-19 下午3:27
 */
class HomeController extends BaseController
{

    /**
     * home index page
     * @author zhengqian.zhu@enstar.com
     * @return null
     */
    public function index()
    {
        //今日阅读
        $preOneDay = date("Y-m-d",time());
        $readings = Reading::where("created_at",">",$preOneDay)->orderBy('created_at','DESC')->get();
        foreach($readings as $l){
            $l->lesson = Lesson::find($l->lesson_id)->title;
            $objUser = User::find($l->user_id);
            $l->user = $objUser->mobile ? $objUser->moblie : $objUser->token;
            $l->lang = $l->language == "en-gb" ? 'en' : 'us';
            $l->audio_url = Config::get('app.api_url').$l->audio;
            $l->ip = $objUser->ip;
            $l->status = $this->handerLessonStatus($l->status);
        }
        unset($l);

        //昨日阅读
        $date = new \DateTime();
        $date->modify("-1 day");
        $preTwoDay = $date->format("Y-m-d");
        $readingsTwo = Reading::where("created_at",">",$preTwoDay)->where('created_at','<',$preOneDay)->get();

        foreach($readingsTwo as $l){
            $l->lesson = Lesson::find($l->lesson_id)->title;
            $objUser = User::find($l->user_id);
            $l->user = $objUser->mobile ? $objUser->moblie : $objUser->token;
            $l->lang = $l->language == "en-gb" ? 'en' : 'us';
            $l->audio_url = Config::get('app.api_url').$l->audio;
            $l->ip = $objUser->ip;
            $l->status = $this->handerLessonStatus($l->status);
        }
        //新用户
        $users = User::orderBy('created_at','DESC')->take(10)->get();

        //队列信息
        $redis = Redis::connection();
        $redisNceStatus = $redis->get('rocket:asr:status');
        $arrRedis = array();
        $arrRedis['redisNceStatus'] = (! $redisNceStatus or $redisNceStatus !=='on') ?  '不可用' : '正常';
        $arrRedis['redisNceAnalyzeInput'] = $redis->llen('rocket:lesson:analyze:input');
        $arrRedis['redisNceAnalyzeOutput'] = $redis->llen('rocket:lesson:analyze:output');
        $arrRedis['redisNceReadInput'] = $redis->llen('rocket:read:analyze:input');
        $arrRedis['redisNceReadOutput'] = $redis->llen('rocket:read:analyze:output');
        $arrRedis['redisNceRetry'] = $redis->llen('rocket:read:analyze:retry');

        //注册阅读数
        $regToday = User::where("created_at",">",$preOneDay)->get();


        return View::make('admin.home')->with('readings',$readings)
                                        ->with('readingsTwo',$readingsTwo)
                                        ->with('redis',$arrRedis)
                                        ->with('users',$users)
                                        ->with('regToday',$regToday);
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
                    $status = '未评分';
                    $statusClass = 'label-warning';
                    break;
                case -1;
                    $status = '评分失败';
                    $statusClass = 'label-danger';
                    break;
                case 10:
                    $status = '正在评分';
                    $statusClass = 'label-success';
                    break;
                case 100:
                    $status = '评分成功';
                    $statusClass = 'label-primary';
                    break;
                default :

            }
        }
        return array(
            'status'=>$status,
            'statusClass'=>$statusClass);
    }
    public function test()
    {


    }


}

