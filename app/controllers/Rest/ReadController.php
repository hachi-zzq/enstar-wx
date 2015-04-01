<?php
namespace Enstar\Controller\Rest;

use Input;
use Response;
use Reading;
use Sentence;
use Analysis;
use Lesson;
use Advisory;
use Session;
use Validator;
use J20\Uuid\Uuid;
use Config;
use ReadMQ;

/**
 * RestAPI 阅读类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class ReadController extends BaseController
{

    /**
     * TODO 提交录音
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function postRead()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'lesson_id' => 'required',
            'readFile' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('20901', $validator->messages()->first(), null);
        }

        $lessonID = $input['lesson_id'];
        $lesson = Lesson::find($lessonID);
        if (!$lesson) {
            return $this->encodeResult('20902', 'no lesson', null);
        }

        $file = Input::file('readFile');
        if (!$file->isValid()) {
            return $this->encodeResult('20903', 'file invalid', null);
        }

        // get lesson info
        $sentences = Sentence::where('lesson_id', $lessonID)->orderBy('sort')->get();
        if (!$sentences) {
            return $this->encodeResult('20902', 'no lesson', null);
        }

        $lessonReport = Analysis::where('lesson_id', $lessonID)->first();
        if (!count($lessonReport)) {
            return $this->encodeResult('20902', 'no lesson', null);
        }

        $ext = strtolower($file->getClientOriginalExtension());
        $guid = Uuid::v4(false);
        $destFile = $guid . '.' . $ext;
        Input::file('readFile')->move(Config::get('app.read.absolutePath'), $destFile);

        // push sentences to array
        $sentencesArray = array();
        foreach ($sentences as $key => $sentence) {
            $item = array();
            $item['id'] = $sentence->sort;
            $item['text'] = $sentence->raw_sentence;
            $item['asrText'] = $sentence->asr_sentence;
            array_push($sentencesArray, $item);
        }

        // save read
        $audioPath = Config::get('app.read.relativePath') . $destFile;
        $readingModel = new Reading();
        $readingModel->lesson_id = $lessonID;
        $readingModel->lesson_key = $lesson->lesson_key;
        $readingModel->user_id = Session::get('uid');
        $readingModel->audio = $audioPath;
        $readingModel->grade = 0;
        $readingModel->report = '';
        $readingModel->status = 0;
        $readingModel->save();
        $readID = $readingModel->id;

        // push to redis
        $toPush = array(
            'readId' => $readID,
            'userId' => Session::get('uid'),
            'lessonId' => $lessonID,
            'homeworkId' => 0,
            'lessonReportGuid' => $lessonReport->guid,
            'submissionTime' => date('Y-m-d H:i:s', time()),
            'audioPath' => url($audioPath),
            'lessonReportPath' => $lessonReport->path,
            'language' => 'en',
            'sentences' => $sentencesArray
        );
        $toPushJson = json_encode($toPush);

        $readMq = new ReadMQ();
        $esq = $readMq->inQueue(Config::get('app.rocket_read_in_key'), $toPushJson);
        if (!$esq) {
            return $this->encodeResult('20904', 'redis server error', null);
        }

        // success
        $response = array(
            "read_id" => $readID,
            "guid" => $guid
        );
        return $this->encodeResult('10900', 'success', $response);
    }

    /**
     * 获取朗读历史记录
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function getRead($key)
    {
        $input = $this->getOptionalInput();
        $read = Reading::where('lesson_key', $key)
                       ->where('user_id', Session::get('uid'))
                       ->take($input['count'])
                       ->skip($input['start'])
                       ->get();

        $readArray = array();
        $readArray['read'] = null;

        if (count($read)) {
            $readArray['read'] = array();
            foreach ($read as $k => $r) {
                $item = array();
                $item['id'] = $r->id;
                $item['lesson_id'] = $r->lesson_id;
                $item['lesson_key'] = $r->lesson_key;
                $lesson = Lesson::find($r->lesson_id);
                if (count($lesson)) {
                    $item['title'] = $lesson->title;
                } else {
                    $item['title'] = '';
                }
                $item['audio'] = url($r->audio);
                $item['grade'] = $r->grade;
                $item['status'] = Reading::$status[$r->status];

                $advisory = Advisory::where('reading_id', $r->id)->first();
                if (count($advisory)) {
                    $item['advisory_id'] = $advisory->id;
                    $item['advisory_guid'] = $advisory->guid;
                } else {
                    $item['advisory_id'] = 0;
                    $item['advisory_guid'] = '';
                }
                $item['advisory'] = empty($r->report) ? '' : url($r->report);

                array_push($readArray['read'], $item);
            }
        }

        $readArray['count'] = count($read);
        $readArray['start'] = (int)($input['start']);

        return $this->encodeResult('11000', 'success', $readArray);
    }
}
