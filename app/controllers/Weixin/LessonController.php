<?php namespace Enstar\Controller\Weixin;

use \Cache;
use \Book;
use Enstar\Library\Weixin\WeixinClient;
use \Lesson;
use \View;

class LessonController extends BaseController
{

    const LESSON_LIST_CACHE_KEY = 'book-id';

    const LESSON_DETAIL_CACHE_KEY = 'lesson-guid';

    /**
     * @课文列表
     * @param $bookId
     * @author zhengqian.zhu
     */
    public function index($bookId)
    {
        $cacheKey = self::LESSON_LIST_CACHE_KEY.$bookId;
        $objBook = Book::find($bookId);
        if (!$objBook) {
            die("book not found");
        }
        if( ! Cache::has($cacheKey)){
            $lessonIndex = Lesson::where('book_id', $bookId)->orderBy('sort')->get();
            Cache::put($cacheKey,serialize($lessonIndex),24*12*60);
        }
        $lessonIndex = unserialize(Cache::get($cacheKey));

        return View::make('wx.lesson_index')
            ->with('book',$objBook )
            ->with('lessons', $lessonIndex)
            ->with('jsapiConfig', $this->getJsapiConfig())
            ;
    }

    /**
     * @param $lesson_guid
     * @return mixed
     */
    public function lessonDetail($lesson_guid)
    {
        $userId = $this->getUserIdFromOpenId();
        $cacheKey = self::LESSON_DETAIL_CACHE_KEY.$lesson_guid;
        $lesson = Lesson::where('guid', $lesson_guid)->first();
        if (!$lesson) {
            die("Lesson not found");
        }

        if( ! Cache::has($cacheKey)){
            foreach($lesson->sentences as $s){
                if($s->format == 'P'){
                    $s->raw_sentence = "</p><p><span>".$this->lessonFormatRender($s->raw_sentence).'</span>';
                }elseif($s->format == 'L'){
                    $s->raw_sentence = "<br/><span>".$this->lessonFormatRender($s->raw_sentence).'</span>';
                }else{
                    $s->raw_sentence = "<span>".$this->lessonFormatRender($s->raw_sentence).'</span>';
                }
            }
            Cache::put($cacheKey,serialize($lesson),24*12*60);
        }

        $lesson = unserialize(Cache::get($cacheKey));

       $jsapiConfig = $this->getJsapiConfig();
        return View::make('wx.lesson_detail')
            ->with('jsapiConfig', $jsapiConfig)
            ->with('lesson', $lesson)
            ->with('userId',$userId)
            ;
    }

    /**
     * @param $str
     * @return mixed
     * @author: zhengqian.zhu@enstar.com
     */
    private function lessonFormatRender($str)
    {
        //去除[[ ]]
        $str = preg_replace('/\[\[[^\]]*\]\]/', '', $str);
        $str = str_replace('<p></p>', '', $str);
        //去除{}
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);

        return $str;
    }

}
