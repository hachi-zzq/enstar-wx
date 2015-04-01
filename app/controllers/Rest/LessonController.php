<?php
namespace Enstar\Controller\Rest;

use Config;
use Input;
use Response;
use Lesson;
use Unit;
use Sentence;
use Analysis;
use Advisory;
use Reading;

/**
 * RestAPI 课文类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class LessonController extends BaseController
{

    /**
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $id
     * @return json
     */
    public function byId($id)
    {
        $lessonArray = $this->getLessonDetail($id);
        if (!$lessonArray) {
            return $this->encodeResult('20801', 'empty set', $lessonArray);
        }
        return $this->encodeResult('10800', 'success', $lessonArray);
    }

    /**
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $key
     * @param $language
     * @return json
     */
    public function byKeyLanguage($key, $language)
    {
        $lesson = Lesson::where('lesson_key', $key)->where('language', $language)->first();
        if (!$lesson) {
            return $this->encodeResult('20802', 'empty set', null);
        }

        $lessonArray = $this->getLessonDetail($lesson->id);
        if (!$lessonArray) {
            return $this->encodeResult('20802', 'empty set', $lessonArray);
        }
        return $this->encodeResult('10801', 'success', $lessonArray);
    }

    /**
     * 获取课文详情通用方法
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $lessonID
     * @return array
     */
    private function getLessonDetail($lessonID)
    {
        $lesson = Lesson::find($lessonID);
        if (!$lesson) {
            return null;
        }

        $resultArray = array();
        $resultArray['id'] = $lesson->id;
        $resultArray['guid'] = $lesson->guid;
        $resultArray['title'] = $lesson->title;
        $resultArray['unit_id'] = $lesson->unit_id;
        $resultArray['unit_title'] = '';
        $resultArray['book_id'] = $lesson->book_id;
        $resultArray['audio'] = url($lesson->audio);

        if ($lesson->unit_id) {
            $unit = Unit::find($lesson->unit_id);
            if ($unit) {
                $resultArray['unit_title'] = $unit->name;
            }
        }

        // get audio file size
        try {
            $size = filesize(public_path() . $lesson->audio);
        } catch (\Exception $e) {
            $size = 0;
        }
        $resultArray['size'] = $size;

        $sentences = Sentence::where('lesson_id', $lessonID)->orderBy('sort')->get();
        if (!$sentences) {
            return Response::json(array('id' => 0));
        }

        $lessonReport = Analysis::where('lesson_id', $lessonID)->first();
        if (count($lessonReport)) {
            $lessonReportPath = empty($lessonReport->path) ? '' : url($lessonReport->path);
            $resultArray['referenceSpeedMin'] = Analysis::getSpeed($lessonReport->id);
        } else {
            $lessonReportPath = '';
            $resultArray['referenceSpeedMin'] = Config::get('evaluate.speed.low');
        }
        $resultArray['referenceSpeedMax'] = Config::get('evaluate.speed.high');
        $resultArray['analysis'] = $lessonReportPath;

        $sentences = url('/data/lesson/' . $lesson->guid . '.json');
        $resultArray['full_text'] = $sentences;
        $resultArray['lesson_unique'] = $lesson->lesson_unique;

        // TODO update user_lessons

        return $resultArray;
    }
}
