<?php
namespace Enstar\Controller\Admin;

use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use \Config;
use J20\Uuid\Uuid;
use \Unit;
use Illuminate\Support\Facades\Redis;
use \Lesson;
use \Reading;
use \User;

class ReadingController extends BaseController
{
    /**
     * @reading index
     * @param null
     * @return mixed
     * @author zhengqian.zhu
     */
    public function index()
    {
        $inputData = Input::all();
        $timeToday = date("Y-m-d");
        $timeNow = date("Y-m-d H:i:s");

        $timeYes = date("Y-m-d",time()-24*3600);

        $timeNowWeek = date("Y-m-d",time()-24*3600*(date("N")-1));

        $obj = Reading::orderBy('created_at',"DESC");
        $inputData['end_time'] = !empty($inputData['end_time']) ? $inputData['end_time'] : date("Y-m-d");
        if(isset($inputData['start_time'])){
            $obj = $obj->where('created_at','>',$inputData['start_time']);
        }

        if(isset($inputData['end_time'])){
            $obj = $obj->where('created_at','<',$inputData['end_time']);
        }

        if(isset($inputData['user_id'])){
            $obj = $obj->where('user_id',$inputData['user_id']);
        }

        $list = $obj->paginate(20);

        foreach($list as $l){
            $l->lesson = Lesson::find($l->lesson_id)->title;
            $objUser = User::find($l->user_id);
            $l->user = $objUser->mobile ? $objUser->moblie : $objUser->token;
            $l->lang = $l->language == "en-gb" ? 'en' : 'us';
            $l->status = $this->handerLessonStatus($l->status);
            $l->audio_url = Config::get('app.api_url').$l->audio;
        }
        return View::make('admin.reading.index')->with('readings',$list)->with('input',$inputData)->with('arrTime',array(
            'timeTody'=>$timeToday,
            'timeYes'=>$timeYes,
            'timeNow'=>$timeNow,
            'timeNowWeek'=>$timeNowWeek
        ));
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




}

