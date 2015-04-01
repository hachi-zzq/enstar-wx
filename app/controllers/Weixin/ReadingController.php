<?php namespace Enstar\Controller\Weixin;

use \Input;
use \Reading;
use \View;
use \Lesson;
use \Validator;
use \Enstar\Library\Weixin\WeixinClient;
use \Enstar\Library\Read\ReadReportRender;
use J20\Uuid\Uuid;
use \Analysis;
use ReadMQ;
use \Sentence;
use \Config;

class ReadingController extends BaseController
{

    /**
     * @阅读记录列表，支持用户、课文筛选
     * @return mixed
     * @author zhengqian.zhu
     */
    public function index($userId=null)
    {
        if(empty($userId)){
            $userId = $this->getUserIdFromOpenId();
        }
        $reading = Reading::where('user_id',$userId)->whereRaw("score is not null and deleted_at is null")->orderBy('created_at','desc')->take(10)->get();
        $arrRet = array();
        foreach($reading as $r){
            if( ! array_key_exists($r->lesson_id,$arrRet)){
                $arr = Reading::where('lesson_id',$r->lesson_id)->where('user_id',$userId)->take(10)->get()->toArray();;
                $arrRet[$r->lesson_id]['reading'] = $arr;
                $max = 0;
                foreach($arr as $a){
                    if($a['score']>$max) $max = $a['score'];
                }
                $arrRet[$r->lesson_id]['max'] = $max;
                $arrRet[$r->lesson_id]['lesson'] = $r->lesson;
            }
        }

        return View::make('wx.recent_reading')
            ->with('jsapiConfig', $this->getJsapiConfig())
            ->with('reading',$arrRet);

    }


    /**
     * @最近评分
     * @return mixed
     * @author zhengqian.zhu
     */
    public function recentGrade($userId=null)
    {
        if(empty($userId)){
            $userId = $this->getUserIdFromOpenId();
        }
        $objGrade= Reading::where('user_id',$userId)->whereRaw("score is not null and deleted_at is null")->with('lesson')->orderBy('created_at','DESC')->take(5)->get();
        return View::make('wx.recent_grade')
            ->with('recent_grade',$objGrade)
            ->with('jsapiConfig', $this->getJsapiConfig())
            ;
    }


    /**
     * @阅读详情
     * @param $readingId
     * @author zhengqian.zhu
     */
    public function detail($readingUuid)
    {
        $objReading = Reading::where('uuid',$readingUuid)->first();
        ! $objReading and die('reading not exist');
        $reportPath = public_path(ltrim($objReading->report,'/'));
        if( ! is_file($reportPath)){
            echo sprintf("report path %s not exist",$reportPath);
            exit;
        }
        $read = new ReadReportRender($objReading->lesson_id,$reportPath);
        $arrReport =  $read->getCacheRenderJson();

        foreach($arrReport as &$s){
            $len = count($s['word']);
            $s['word'][0]['text'] = '<span>'.$s['word'][0]['text'];
            $s['word'][$len-1]['text'] = $s['word'][$len-1]['text'].'</span>';
            foreach($s['word'] as &$w){
                $text = $w['text'];
                if( ! $w['pronunciation']){
                    $w['text_pronunciation'] = '<span class="fail">'.$text.'</span>';
                }else{
                    $w['text_pronunciation'] = '<span>'.$text.'</span>';
                }

                if( ! $w['stress']){
                    $w['text_stress'] =  '<span class="fail">'.$text.'</span>';
                }else{
                    $w['text_stress'] = '<span>'.$text.'</span>';
                }

                if( ! $w['intonation']){
                    $w['text_intonation'] = '<span class="fail">'.$text.'</span>';
                }else{
                    $w['text_intonation'] = '<span>'.$text.'</span>';
                }

                if( ! $w['fluency']){
                    $w['text_fluency'] = '<span class="fail">'.$text.'</span>';
                }else{
                    $w['text_fluency'] = '<span>'.$text.'</span>';
                }
            }
            if($s['format'] == 'P'){
                $s['word'][0]['text_pronunciation'] = '</p><p>'.$s['word'][0]['text_pronunciation'];
                $s['word'][0]['text_stress'] = '</p><p>'.$s['word'][0]['text_stress'];
                $s['word'][0]['text_intonation'] = '</p><p>'.$s['word'][0]['text_intonation'];
                $s['word'][0]['text_fluency'] = '</p><p>'.$s['word'][0]['text_fluency'];
            }elseif($s['format'] == 'L'){
                $s['word'][0]['text_pronunciation'] = '<br/>'.$s['word'][0]['text_pronunciation'];
                $s['word'][0]['text_stress'] = '<br/>'.$s['word'][0]['text_stress'];
                $s['word'][0]['text_intonation'] = '<br/>'.$s['word'][0]['text_intonation'];
                $s['word'][0]['text_fluency'] = '<br/>'.$s['word'][0]['text_fluency'];
            }
            unset($s);
            unset($w);
        }

        return View::make('wx.reading_detail')
            ->with('reading',$objReading)
            ->with('error_detail',$arrReport)
            ->with('jsapiConfig', $this->getJsapiConfig())
            ;

    }


    /**
     * @保存用户上传mediaID
     * @author zhengqian,zhu
     */
    public function saveMedia()
    {
        $userId = $this->getUserIdFromOpenId();
        $inputData = Input::only('lesson_guid','media_id');
        \Log::info(json_encode($inputData));
        $validator = Validator::make($inputData,array(
            'lesson_guid'=>'required',
            'media_id'=>'required'
        ));
        if($validator->fails()){
            return json_encode(array(
                'msgcode'=>-1,
                'message'=>$validator->messages()->first()
            ));
        }

        $objLesson = Lesson::getByGuid($inputData['lesson_guid']);
        if( ! $objLesson){
            return json_encode(array(
                'msgcode'=>-2,
                'message'=>'lesson not found'
            ));
        }

        //下载音频
        $wxClient = new WeixinClient();
        $dir = public_path("data/audio/".date("Y-m-d"));
        if( ! is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $fileName = Uuid::v4(false).".amr";
        $mq = new \ReadMQ();
        $wxClient->downloadMedia($dir,$fileName,$inputData['media_id'],$mq->getWeixinAccessToken());

        // push sentences to array
        $sentences = Sentence::where('lesson_id', $objLesson->id)->orderBy('sort')->get();
        $sentencesArray = array();
        foreach ($sentences as $key => $sentence) {
            $item = array();
            $item['id'] = $sentence->sort;
            $item['text'] = $sentence->raw_sentence;
            $item['asrText'] = $sentence->asr_sentence;
            array_push($sentencesArray, $item);
        }

        /** save in reading table */
        $readingModel = new Reading();
        $readingModel->lesson_id = $objLesson->id;
        $readingModel->lesson_key = $objLesson->lesson_key;
        $readingModel->user_id = $userId;
        $readingModel->uuid = Uuid::v4(false);
        $readingModel->audio = "/data/audio/".date("Y-m-d").'/'.$fileName;
        $readingModel->grade = 0;
        $readingModel->report = '';
        $readingModel->status = 0;
        $readingModel->save();

        $readID = $readingModel->id;

        $lessonReport = Analysis::where('lesson_id', $objLesson->id)->first();
        // push to redis
        $toPush = array(
            'readId' => $readID,
            'userId' => $userId,
            'lessonId' => $objLesson->id,
            'homeworkId' => 0,
            'lessonReportGuid' => $lessonReport->guid,
            'submissionTime' => date('Y-m-d H:i:s', time()),
            'audioPath' => "/data/audio/".date("Y-m-d").'/'.$fileName,
            'lessonReportPath' => $lessonReport->path,
            'language' => 'en',
            'sentences' => $sentencesArray
        );
        $toPushJson = json_encode($toPush);

        $readMq = new ReadMQ();
        $esq = $readMq->inQueue(Config::get('app.enstar_read_in_key'), $toPushJson);
        if (!$esq) {
            return json_encode(array(
                'msgcode'=>-3,
                'message'=>'push redis error'
            ));
        }

        return json_encode(array(
            'msgcode'=>0,
            'message'=>'success'
        ));

    }






}