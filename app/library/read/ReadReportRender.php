<?php namespace Enstar\Library\Read;

use \Exception;
use \Sentence;
use \Cache;

/**
 * @阅读报告选择
 * @author zhengqian,zhu
 */
class ReadReportRender
{

    /**
     * @课文id
     * @var
     */
    private $lessonId;

    /**
     * @报告地址
     * @var
     */
    private $reportJsonPath;

    /**
     * @课文json缓存key
     * @var string
     */
    private $cacheJsonKey;

    private $cacheRetRenderKey;

    public function __construct($lessonId,$reportJsonPath)
    {
        $this->lessonId = $lessonId;
        $this->reportJsonPath = $reportJsonPath;
        $this->cacheJsonKey = "report-json:".$reportJsonPath;
        $this->cacheRetRenderKey = "return-json:".$reportJsonPath;
    }

    /**
     * @开始渲染课文
     * @对内接口
     * @param null
     * @author zhengqian.zhu
     */
    private function renderReport()
    {
        $json = Cache::get($this->cacheJsonKey);
        if( ! $json){
            $json = file_get_contents($this->reportJsonPath);
            Cache::forever($this->cacheJsonKey,$json);
        }

        $objJson = json_decode($json);
        $sentences = $objJson->sentences;
        $arrRender = array();
        foreach($sentences as $k=>$s){
            $sentence_id = $s->id;
            $sent = Sentence::where('lesson_id',$this->lessonId)->where('sort',$sentence_id)->first();
            $arrSent['id'] = $sent->sort;
            $arrSent['text'] = $sent->raw_sentence;
            $arrSent['format'] = $sent->format;
            $arrSent['begin'] = $s->begin;
            $arrSent['end'] = $s->end;
            $arrRender[] = $arrSent;
            $words = $s->words;
            $arrWord = array();
            foreach($words as $w){
                $word['text'] = $w->text;
                $word['begin'] = $w->begin;
                $word['end'] = $w->end;
                $word['stress'] = $w->stress ? 1 : 0;
                $word['pronunciation'] = $w->pronunciation ? 1 : 0;
                $word['intonation'] = $w->intonation ? 1 : 0;
                $word['fluency'] = $w->fluency ? 1 : 0;
                array_push($arrWord,$word);
            }
            $arrRender[$k]['word'] = $arrWord;
        }
        return $arrRender;
    }

    /**
     * @对外接口，获取渲染的结果数组
     * @author zhengqian.zhu
     * @return mixed
     */
    public function getCacheRenderJson()
    {
        $json = Cache::get($this->cacheRetRenderKey);
        if( ! $json){
            Cache::forever($this->cacheRetRenderKey,$this->renderReport());
        }
        return Cache::get($this->cacheRetRenderKey);
    }
}