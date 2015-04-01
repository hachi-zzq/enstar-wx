<?php
namespace Enstar\Controller\Admin;

use Illuminate\Support\Facades\DB;
use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use \Unit;
use \Lesson;
use J20\Uuid\Uuid;
use \Sentence;
use \Enstar\Library\MQ;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-21 下午4:42
 */
class LessonController extends BaseController
{

    /**
     * #课文列表
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function index($bookId=null)
    {
        $bookId = Input::get('book_id') ? Input::get('book_id') : $bookId;
        $units = null;
        $lessons = null;
        //检查是否是单元
        if(count($units = Unit::where('book_id',$bookId)->orderBy('sort')->paginate(20))){
            //有单元
            foreach($units as $u){
                $lessons = $u->lessons = Unit::getLessons($u->id);
                foreach($lessons as $l){
                    $l->lastVersion = Lesson::getLastVersion($l->lesson_unique);
                    $l->statusClass = $this->handerLessonStatus($l->status)['statusClass'];
                    $l->status = $this->handerLessonStatus($l->status)['status'];
                }
                $lessons = null;
            }
        }else{
            //无单元
            $lessons = Lesson::where('book_id',$bookId)->orderBy('sort','ASC')->paginate(20);
            if($lessons){
                foreach($lessons as &$l){
                    $l->lastVersion = Lesson::getLastVersion($l->lesson_unique);
                    $l->statusClass = $this->handerLessonStatus($l->status)['statusClass'];
                    $l->status = $this->handerLessonStatus($l->status)['status'];
                }
            }
        }


        return View::make('admin.lesson.index')->with('book_id',$bookId)
                                                ->with('units',$units)
                                                ->with('lessons',$lessons);
    }





    /**
     * #添加课文
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function create($bookId=null)
    {
        return View::make('admin.lesson.create')->with('book_id',$bookId);
    }


    /**
     * #添加课文
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function postCreate()
    {
        $inputData = Input::only('book_unit','lesson_title','audio','raw_content','asr_content','book_id');
        $validator = Validator::make($inputData,array(
            'book_id'=>'required',
            'book_unit'=>'required',
            'lesson_title'=>'required',
            'audio'=>'required',
            'raw_content'=>'required',
            'asr_content'=>'required'
        ));
        //TODO 重名验证
        if($validator->fails()){
            return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('error_tips',$validator->messages()->first());
        }

        if(preg_match('/^book_([\d]+)unit_([\d]+)/',$inputData['book_unit'],$match)){
            $book_id = $match[1];
            $unit_id = $match[2];
        }elseif(preg_match('/^book_([\d]+)/',$inputData['book_unit'],$match)){
            $book_id = $match[1];
            $unit_id = null;
        }else{
            return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('error_tips','参数错误');
        }

        //教材有单元的时候，规定课文必须在它的单元下
        if($unit_id == null and count(Book::getUnits($book_id))!==0){
            return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('error_tips',sprintf(" %s 下面包含单元，所以课文必须在其单元下",Book::find($book_id)->name));
        }

        //save sentence
        $retSentenceHandler = \ESHelp::sentenceHandler($inputData['raw_content']);
        $asrSentence = explode("\n",$inputData['asr_content']);
        //去掉空行
        foreach($asrSentence as $k=>$v){
            if(trim($v) == ''){
                unset($asrSentence[$k]);
            }
        }
        if(count($retSentenceHandler) !== count($asrSentence))
            return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('error_tips','参数错误,请确保文本框两边行数对应');


        if(Input::hasFile('audio') and Input::file('audio')->isValid()){
            $file = Input::file('audio');
            if( ! in_array($file->getClientOriginalExtension(),array('mp3','wav','m4a'))){
                return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('error_tips','文件格式不正确');
            }
            $destinationFile = sprintf('%s.%s',Uuid::v4(false),$file->getClientOriginalExtension());
            $destinationPath = public_path().'/data/audio/'.date('Ymd');
            !is_dir($destinationPath) and mkdir($destinationPath,0777,true);
            $file->move(public_path().'/data/audio/'.date('Ymd'),$destinationFile);
            $audioRealPath = sprintf("%s/%s/%s",public_path('data/audio'),date('Ymd'),$destinationFile);
        }

        $objLesson = new Lesson();
        $objLesson->guid = Uuid::v4(false);
        $objLesson->book_id = $book_id;
        $objLesson->unit_id = $unit_id;
        $objLesson->raw_content = $inputData['raw_content'];
        $objLesson->asr_content = $inputData['asr_content'];
        $objLesson->title = $inputData['lesson_title'];
        $objLesson->audio = sprintf('%s/%s','/data/audio/'.date('Ymd'),$destinationFile);
        $objLesson->version = '1.0';
        $objLesson->lesson_unique = Uuid::v4(false);
        $objLesson->status = 0;
        $objLesson->tag = "新创建的课文";
        $objLesson->save();


//        print_r($asrSentence);
//        print_r($retSentenceHandler);
//        exit();
        foreach($retSentenceHandler as $k=>$v){
            $objSentence = new Sentence();
            $objSentence->lesson_id = $objLesson->id;
            $objSentence->raw_sentence = trim($v['sentence']);
            $objSentence->asr_sentence = trim($asrSentence[$k]);
            $objSentence->sort = $k+1;
            $objSentence->prefix = $v['prefix'];
            $objSentence->format = $v['format'];
            $objSentence->type = $v['prefix'] ? 'dialogue' : 'normal';
            $objSentence->save();
            $arrTmp[] = array(
                'id'=>$objSentence->sort,
                'text'=>$objSentence->raw_sentence,
                'asrText'=> $objSentence->asr_sentence,
                'format'=>$objSentence->format,
                'prefix'=>$objSentence->prefix,
                'type'=>$objSentence->type,
            );
        }

        //提交到redis队列处理
        $arrPushRedis = array();
        $arrPushRedis['lessonId'] = $objLesson->id;
        $arrPushRedis['sentences'] = $arrTmp;
        $arrPushRedis['audioPath'] = $objLesson->audio;
        $arrPushRedis['language'] = 'en';
        $redis = MQ::getInstance();
        $redis->pushLessonToAnalyze(json_encode($arrPushRedis));
        $redis->pushAudioConvert($audioRealPath);

        //保存课文格式到data/lesson文件夹中
        file_put_contents(sprintf("%s/%s.json",public_path('data/lesson'),$objLesson->guid),json_encode($arrPushRedis));

        return Redirect::route('adminLessonCreate',array($inputData['book_id']))->with('success_tips',sprintf('成功添加课文 %s ,版本号：%s',$objLesson->title,$objLesson->version));

    }


    /**
     * #lesson详情
     * @param $lesson_id
     * @author zhengqian
     */
    public function detail($lesson_id)
    {
        $objLesson = Lesson::find($lesson_id);
        return View::make('admin.lesson.detail')->with('lesson',$objLesson);
    }


    /**
     * #课文修改
     * @param $lesson_id
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function modify($lesson_id)
    {
        $obj = Lesson::find($lesson_id);
        !$obj and \App::abort(404);
        $bookId = $obj->book_id;
        $unitId = $obj->unit_id ? $obj->unit_id : null;
        if(Book::find($bookId)->status==1){
            return Redirect::route('adminLessonIndex',array($bookId))->with('error_tips','所在教材已经发布，不可以修改，如果您有改动，可以创建新版本在进行修改，或者可以进行勘误');
        }

        return View::make('admin.lesson.modify')->with('book_id',$bookId)->with('unit_id',$unitId)->with('lesson',$obj);
    }


    public function postModify()
    {
        $inputData = Input::only('book_unit','lesson_title','audio','raw_content','asr_content','lesson_id');
        $validator = Validator::make($inputData,array(
            'book_unit'=>'required',
            'lesson_title'=>'required',
            'audio'=>'',
            'raw_content'=>'required',
            'asr_content'=>'required',
            'lesson_id'=>'required'
        ));
        //TODO 重名验证
        if($validator->fails()){
            return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips',$validator->messages()->first());
        }

        if(preg_match('/^book_([\d]+)unit_([\d]+)/',$inputData['book_unit'],$match)){
            $book_id = $match[1];
            $unit_id = $match[2];
        }elseif(preg_match('/^book_([\d]+)/',$inputData['book_unit'],$match)){
            $book_id = $match[1];
            $unit_id = null;
        }else{
            return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips','参数错误');
        }

        //教材有单元的时候，规定课文必须在它的单元下
        if($unit_id == null and count(Book::getUnits($book_id))!==0){
            return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips',sprintf("%s 下面包含单元，所以课文必须在其单元下",Book::find($book_id)->name));
        }

        if(Book::find($book_id)->status == 1){
            return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips',sprintf(" %s已经发布，不能选择该教材",Book::find($book_id)->name));
        }

        //生成新句子
        $retSentenceHandler = \ESHelp::sentenceHandler($inputData['raw_content']);
        $asrSentence = explode("\n",$inputData['asr_content']);
        //去掉空行
        foreach($asrSentence as $k=>$v){
            if(trim($v) == ''){
                unset($asrSentence[$k]);
            }
        }
        if(count($retSentenceHandler) !== count($asrSentence))
            return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips','参数错误,请确保文本框两边行数对应');


        if(Input::hasFile('audio') and Input::file('audio')->isValid()){
            $file = Input::file('audio');
            if( ! in_array($file->getClientOriginalExtension(),array('mp3','wav','m4a'))){
                return Redirect::route('adminLessonModify',array($inputData['lesson_id']))->with('error_tips','文件格式不正确');
            }
            $destinationFile = sprintf('%s.%s',Uuid::v4(false),$file->getClientOriginalExtension());
            $destinationPath = public_path().'/data/audio/'.date('Ymd');
            !is_dir($destinationPath) and mkdir($destinationPath,0777,true);
            $file->move(public_path().'/data/audio/'.date('Ymd'),$destinationFile);
            $audioRealPath = sprintf("%s/%s/%s",public_path('data/audio'),date('Ymd'),$destinationFile);
        }

        $objLesson = Lesson::find($inputData['lesson_id']);
        $objLesson->book_id = $book_id;
        $objLesson->unit_id = $unit_id;
        $objLesson->raw_content = $inputData['raw_content'];
        $objLesson->asr_content = $inputData['asr_content'];
        $objLesson->title = $inputData['lesson_title'];
        $objLesson->status = 0;

        if(Input::hasFile('audio')){
            $objLesson->audio = sprintf('%s/%s','/data/audio/'.date('Ymd'),$destinationFile);
        }
        $objLesson->save();

        //删除旧的句子
        Sentence::where('lesson_id',$inputData['lesson_id'])->delete();


        foreach($retSentenceHandler as $k=>$v){
            $objSentence = new Sentence();
            $objSentence->lesson_id = $objLesson->id;
            $objSentence->raw_sentence = trim($v['sentence']);
            $objSentence->asr_sentence = trim($asrSentence[$k]);
            $objSentence->sort = $k+1;
            $objSentence->prefix = $v['prefix'];
            $objSentence->format = $v['format'];
            $objSentence->type = $v['prefix'] ? 'dialogue' : 'normal';
            $objSentence->save();

            $arrTmp[] = array(
                'id'=>$objSentence->sort,
                'text'=>$objSentence->raw_sentence,
                'asrText'=> $objSentence->asr_sentence,
                'format'=>$objSentence->format,
                'prefix'=>$objSentence->prefix,
                'type'=>$objSentence->type,
            );
        }


        //提交到redis队列处理
        $arrPushRedis = array();
        $arrPushRedis['lessonId'] = $objLesson->id;
        $arrPushRedis['sentences'] = $arrTmp;
        $arrPushRedis['audioPath'] = $objLesson->audio;
        $arrPushRedis['language'] = 'en';
        $redis = MQ::getInstance();
        $redis->pushLessonToAnalyze(json_encode($arrPushRedis));
        if(Input::hasFile('audio') and Input::file('audio')->isValid()){
            $redis->pushAudioConvert($audioRealPath);
        }

        //保存课文格式到data/lesson文件夹中
        file_put_contents(sprintf("%s/%s.json",public_path('data/lesson'),$objLesson->guid),json_encode($arrPushRedis));

        return Redirect::route('adminLessonIndex',array($book_id))->with('success_tips','修改成功');
    }

    /**
     * #教材勘误
     * @param $lesson_id
     * @author zhengqian.zhu@enstar.com
     */
    public function correct($lesson_id){
        $obj = Lesson::find($lesson_id);
        !$obj and \App::abort(404);
        //未发布的不允许勘误，勘误只针对已经发布的
        if(Book::find($obj->book_id)->status == 0){
            return Redirect::route('adminLessonIndex',array($obj->book_id))->with('error_tips',sprintf("%s 还未发布，不允许勘误，你可以直接修改",Book::find($obj->book_id)->name));
        }
        return View::make('admin.lesson.correct')->with('lesson',$obj);
    }

    /**
     * #教材勘误post
     * @param null
     * @author zhengqian.zhu@enstar.com
     */
    public function postCorrect()
    {
        $inputData = Input::only('lesson_id','raw_content','asr_content','tag');
        $validator = Validator::make($inputData,array(
            'lesson_id'=>'required',
            'raw_content'=>'required',
            'asr_content'=>'required',
            'tag'=>''
        ));
        //TODO 重名验证
        if($validator->fails()){
            return Redirect::route('adminLessonCorrect',array($inputData['lesson_id']))->with('error_tips',$validator->messages()->first());
        }
        //save sentence
        $retSentenceHandler = \ESHelp::sentenceHandler($inputData['raw_content']);
        $asrSentence = explode("\n",$inputData['asr_content']);
        //去掉空行
        foreach($asrSentence as $k=>$v){
            if(trim($v) == ''){
                unset($asrSentence[$k]);
            }
        }
        if(count($retSentenceHandler) !== count($asrSentence)){
            return Redirect::route('adminLessonCorrect',array($inputData['lesson_id']))->with('error_tips','参数错误,请确保文本框两边行数对应');
        }

        $objLesson = Lesson::find($inputData['lesson_id']);
        $newLesson = new Lesson();
        $newLesson->guid = Uuid::v4(false);
        $newLesson->book_id = $objLesson->book_id;
        $newLesson->unit_id = $objLesson->unit_id;;
        $newLesson->raw_content = $inputData['raw_content'];
        $newLesson->asr_content = $inputData['asr_content'];
        $newLesson->title = $objLesson->title;
        $newLesson->audio = $objLesson->audio;
        $newLesson->version = sprintf('%.1f',floatval(Lesson::getLastVersion($objLesson->lesson_unique)+1),1);
        $newLesson->lesson_unique = $objLesson->lesson_unique;
        $newLesson->status = 0;
        $newLesson->tag = $inputData['tag'];
        $newLesson->save();

        foreach($retSentenceHandler as $k=>$v){
            $objSentence = new Sentence();
            $objSentence->lesson_id = $newLesson->id;
            $objSentence->raw_sentence = trim($v['sentence']);
            $objSentence->asr_sentence = trim($asrSentence[$k]);
            $objSentence->sort = $k+1;
            $objSentence->prefix = $v['prefix'];
            $objSentence->format = $v['format'];
            $objSentence->type = $v['prefix'] ? 'dialogue' : 'normal';
            $objSentence->save();

            $arrTmp[] = array(
                'id'=>$objSentence->sort,
                'text'=>$objSentence->raw_sentence,
                'asrText'=> $objSentence->asr_sentence,
                'format'=>$objSentence->format,
                'prefix'=>$objSentence->prefix,
                'type'=>$objSentence->type,
            );
        }

        //提交到redis队列处理
        $arrPushRedis = array();
        $arrPushRedis['lessonId'] = $newLesson->id;
        $arrPushRedis['sentences'] = $arrTmp;
        $arrPushRedis['audioPath'] = $newLesson->audio;
        $arrPushRedis['language'] = 'en';
        $redis = MQ::getInstance();
        $redis->pushLessonToAnalyze(json_encode($arrPushRedis));

        //保存课文格式到data/lesson文件夹中
        file_put_contents(sprintf("%s/%s.json",public_path('data/lesson'),$objLesson->guid),json_encode($arrPushRedis));

        return Redirect::route('adminLessonIndex',array($objLesson->book_id))->with('success_tips','勘误完成');

    }

    /**
     * #删除lesson，如果book已经发布，那么随便删除，book已经发布的不允许删除
     * @param $lesson_id
     */
    public function destroy($lesson_id)
    {
        $obj = Lesson::find($lesson_id);
        $bookId = $obj->book_id;
        if(Book::find($bookId)->status==1){
            return Redirect::route('adminLessonIndex',array($bookId))->with('error_tips','所在教材已经发布，不可以删除，如果您有改动，可以创建新版本在进行修改');
        }
        Lesson::find($lesson_id)->delete();
        return Redirect::route('adminLessonIndex',array($bookId))->with('success_tips','删除成功');
    }


    /**
     * @param $lesson_id
     */
    public function rehash($lesson_id,$page=1)
    {
        $objLesson = Lesson::find($lesson_id);
        if(Book::find($objLesson->book_id)->status == 1){
            return Redirect::route('adminLessonIndex',array($objLesson->book_id))->with('error_tips','课文已经发布，不可以重新分析');
        }
        $objLesson->status = 0;
        $objLesson->save();
        $sentences = Sentence::where('lesson_id',$lesson_id)->get();
        if($sentences->count()){
            foreach($sentences as $s){
                $arrTmp[] = array(
                    'id'=>$s->sort,
                    'text'=>$s->raw_sentence,
                    'asrText'=> $s->asr_sentence,
                    'format'=>$s->format,
                    'prefix'=>$s->prefix,
                    'type'=>$s->type,
                );
            }
            //提交到redis队列处理
            $arrPushRedis = array();
            $arrPushRedis['lessonId'] = $objLesson->id;
            $arrPushRedis['sentences'] = $arrTmp;
            $arrPushRedis['audioPath'] = $objLesson->audio;
            $arrPushRedis['language'] = 'en';
            $redis = MQ::getInstance();
            $redis->pushLessonToAnalyze(json_encode($arrPushRedis));

        }

        //保存课文格式到data/lesson文件夹中
        file_put_contents(sprintf("%s/%s.json",public_path('data/lesson'),$objLesson->guid),json_encode($arrPushRedis));

//        return Redirect::route('adminLessonIndex',array($objLesson->book_id))->withInput(Input::get())->with('success_tips','正在重新分析，请稍等');
        return Redirect::to('/admin/lesson/'.$objLesson->book_id.'/index?page='.$page)->with('success_tips','正在重新分析，请稍等');

    }

    /**
     * @课文排序
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function multiSort()
    {
        $inputData = Input::only('sort','book_id');
        //TODO Validator
        foreach($inputData['sort'] as $lesson_id=>$sort){
            $obj = Lesson::find($lesson_id);
            $obj->sort = $sort;
            $obj->save();
        }
        return Redirect::route('adminLessonIndex',array($inputData['book_id']))->with('success_tips','排序成功！');
    }



    /**
     * @param $lesson_id
     */
    public function history($lesson_id)
    {

    }
}

