<?php namespace Enstar\Library;

use Illuminate\Support\Facades\Redis;
use \Analysis;
use J20\Uuid\Uuid;
use \Lesson;
use Predis\Connection\ConnectionException;

/**
 * #mq redis消息队列
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-26 上午11:16
 */
class MQ
{
    /**
     * #redis实例
     * @var null
     */
    private static  $MqInstance = null;
    public static  $redisInstance = null;
    public static $len = 0;

    const ANALYZE_OUTPUT_KEY = 'rocket:lesson:analyze:output';
    const ANALYZE_INPUT_KEY = 'rocket:lesson:analyze:input';

    const AUDIO_CONVERT_KEY = 'enstar:media:converter';

    /**
     * #禁止外界new，保证单件模式
     */
    private function __construct()
    {
        self::$redisInstance = Redis::connection();
    }

    /**
     * #禁止克隆
     */
    private function __clone()
    {

    }

    /**
     * #获取redis实例对象
     * @return null
     * @author zhengqian.zhu@enstar.com
     */
    public static function getInstance()
    {
        if( ! self::$MqInstance){
            self::$MqInstance = new MQ();
        }

        return self::$MqInstance;
    }


    /**
     * #将课文放到redis队列中
     * @param $array
     * @return bool
     */
    public function pushLessonToAnalyze($json){
        return self::$redisInstance->lpush(self::ANALYZE_INPUT_KEY,$json);
    }

    /**
     * #将分析结果取出来
     * @return mixed
     */
    public function popLessonAnalyzeRet(){
        $len = self::$redisInstance->llen(self::ANALYZE_OUTPUT_KEY);
        while ($len > 0) {
            $len--;
            $msg = self::$redisInstance->rpop(self::ANALYZE_OUTPUT_KEY);
            if ($msg) {
                $this->messageHandler($msg);
            }
        };
    }

    /**
     * #取出的json进行处理
     * @param $json
     */
    public function messageHandler($json){
        $arrRet = json_decode($json,true);
        //更新lesson状态
        $objLesson = Lesson::find($arrRet['lessonId']);
        if( ! $objLesson){
            return ;
        }

        $objBook = \Book::find($objLesson->book_id);
        if( ! $objBook){
            return;
        }

        if($arrRet['status'] == 'SUCCESS' ){
            self::handlerLessonTime(rtrim(public_path().'/').$arrRet['reportPath'],$arrRet['lessonId']);
            $objLesson->status = 2;
        }elseif($arrRet['status'] == 'FAIL'){
            $objLesson->status = -1;
        }elseif($arrRet['status'] == 'PROCESSING'){
            $objLesson->status = 1;
        }elseif($arrRet['status'] == 'UNAVAILABLE'){
            $objLesson->status = -2;
        }else{
            return;
        }
        $objLesson->save();

        //删除旧的
        if($arrRet['status'] == 'SUCCESS' or $arrRet['status'] == 'UNAVAILABLE'){
            //保存分析时间
            $objLesson->asr_duration = $arrRet['asr_time'];
            $objLesson->save();

            Analysis::where('lesson_id',$arrRet['lessonId'])->delete();

            $analysis = new Analysis();
            $analysis->guid = Uuid::v4(false);
            $analysis->lesson_id = $arrRet['lessonId'];
            $analysis->source = 1;
            $analysis->path = $arrRet['reportPath'];
            $analysis->save();
        }

    }


    /**
     * @音频推进队列，进行转码
     * @param $path
     * @return mixed
     */
    public function pushAudioConvert($path)
    {
        return self::$redisInstance->lpush(self::AUDIO_CONVERT_KEY,$path);
    }

    /**
     * @生成新的课文格式，新增时间点，和单据翻译
     * @param string $jsonPath
     * @param $lessonId
     */
    public static function handlerLessonTime($jsonPath='',$lessonId){
        $objLesson = Lesson::find($lessonId);
        if( ! $objLesson){
            return;
        }
        if(file_exists($jsonPath) and $jsonPath){
            $json = file_get_contents($jsonPath);
            $obj = json_decode($json);
            $sentence = $obj->sentences;
            $arrTmp = array();
            if(is_array($sentence)){
                foreach($sentence as $s){
                    $arrTmp[$s->id] = array(
                        'begin'=>$s->begin,
                        'end'=>$s->end
                    );
                }
            }

            if(file_exists($path=sprintf("%s%s.json",public_path('data/lesson/'),$objLesson->guid))){
                $lessonFormatJson = file_get_contents($path);
                $arrLessonFormat = json_decode($lessonFormatJson,true);
                foreach($arrLessonFormat['sentences'] as &$fs){
                    $fs['begin'] = isset($arrTmp[$fs['id']]) ? $arrTmp[$fs['id']]['begin'] : 0;
                    $fs['end'] = isset( $arrTmp[$fs['id']]) ? $arrTmp[$fs['id']]['end'] : 0;
                    $fs['translation'] = "";
                }
                file_put_contents(sprintf("%s%s.json",public_path('data/lesson_time/'),$objLesson->guid),json_encode($arrLessonFormat));
            }

        }else{
            \Log::info(sprintf("ID:%s Path:%s report json file is not exist",$lessonId,$jsonPath));
            return;
        }
    }

    public static function getNceRedisStatus()
    {
        return self::$redisInstance->get('rocket:asr:status');
    }



    /**
     * @获取到队列中所有数据
     * @param null
     * @author zhengqian.zhu
     */
    public static  function getQueueList()
    {
        $arr = array();
        $len = self::$redisInstance->llen(self::ANALYZE_INPUT_KEY);
        self::$len = $len;
        while ($len > 0) {
            array_push($arr,self::$redisInstance->lindex(self::ANALYZE_INPUT_KEY,$len-1));
            $len--;
        };

        return $arr;

    }


}
