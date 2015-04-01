<?php
namespace Enstar\Controller\Admin;

use Illuminate\Support\Facades\Redis;
use \View;
use \Redirect;
use \Request;
use \Input;
use \Validator;
use \Enstar\Library\MQ;
use \ReadMQ;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-19 下午3:27
 */
class QueueController extends BaseController
{

    /**
     * @阅读评分队列
     * @param null
     * @return mixed
     * @author zhengqian.zhu
     */
    public function readingIndex()
    {
        $readMQ = new ReadMQ();
        $readRedisKey = "rocket:read:analyze:input";
        $lists = $readMQ->getQueueList();

        return View::make('admin.queue.reading_index')
                                  ->with('list',$lists)
                                  ->with('key',$readRedisKey)
                                  ->with('len',$readMQ->len);
    }

    /**
     * @清空某个key
     * @param $type
     * @return mixed
     * @author zhengqian.zhu
     */
    public function flushKey($type)
    {
        if($type == 'reading'){
            $key = "rocket:read:analyze:input";
            $route = "readingQueue";
        }elseif($type == 'lesson'){
            $key = "rocket:lesson:analyze:input";
            $route = "lessonQueue";
        }elseif($type == 'retry'){
            $key = "rocket:lesson:analyze:retry";
            $route = "retryQueue";
        }else{
            return;
        }

        $redis = Redis::connection();
        $redis->del($key);
        return Redirect::route($route);

    }


    /**
     * @课文分析队列
     * @param null
     * @return mixed
     * @author zhengqian.zhu
     */
    public function lessonIndex()
    {
        $readRedisKey = "rocket:lesson:analyze:input";
        $lists = MQ::getQueueList();

        return View::make('admin.queue.lesson_index')
            ->with('list',$lists)
            ->with('key',$readRedisKey)
            ->with('len',MQ::$len);
    }

    /**
     * @重试队列index
     * @param mixed
     * @return null;
     * @author zhenqian.zhu@enstar.com
     */
    public function retryIndex()
    {
        $readRedisKey = "rocket:read:analyze:retry";
        $readMQ = new ReadMQ();
        $lists = $readMQ->getRetryList();
        return View::make('admin.queue.retry_index')
            ->with('list',$lists)
            ->with('key',$readRedisKey)
            ->with('len',$readMQ->retry_len);
    }


}

