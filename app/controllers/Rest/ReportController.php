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
use Enstar\Library\SpeedScore;

/**
 * RestAPI 报告类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class ReportController extends BaseController
{
    /**
     * 某篇课文的所有报告
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function reports()
    {
        $input = Input::all();
        $lessonID = $input['lessonID'];
        $studentID = $input['studentID'];

        $reading = Reading::where('lesson_id', $lessonID)
                            ->where('student_id', $studentID)
                            ->where('status', 100)
                            ->take($input['count'])
                            ->skip($input['start'])
                            ->orderBy($input['orderby'], $input['sort'])
                            ->get();

        $resultArray = array();
        if (count($reading)) {
            $resultArray['advisory'] = array();
            foreach ($reading as $key => $r) {
                $item = array();

                $advisory = Advisory::where('reading_id', $r->id)->orderby('updated_at', 'desc')->first();
                if (count($advisory)) {
                    $item['id'] = $advisory->id;
                    $item['guid'] = $advisory->guid;
                    $item['read_id'] = $r->id;
                    $item['lesson_id'] = $r->lesson_id;

                    $lesson = Lesson::find($r->lesson_id);
                    $item['title'] = $lesson->title;

                    $analysis = Analysis::where('lesson_id', $r->lesson_id)->first();
                    if (count($analysis)) {
                        $item['analysis_guid'] = $analysis->guid;
                    } else {
                        $item['analysis_guid'] = '';
                    }

                    $item['advisory'] = empty($r->report) ? '' : url($r->report);
                    $item['is_homework'] = ($r->type == 2) ? true : false;
                    $item['created_at'] = date($r->created_at);
                    $item['status'] = $this->readingStatus[$r->status];

                    $item['pronunciationScore'] = (float)$r->pronunciation_score;
                    $item['intonationScore'] = (float)$r->intonation_score;
                    $item['stressScore'] = (float)$r->stress_score;
                    $item['fluencyScore'] = (float)$r->fluency_score;
                    $item['speedScore'] = (float)$r->speed_score;
                    $item['speedSituation'] = empty($r->speed_situation) ? "" : $r->speed_situation;
                    $item['speed'] = (float)$r->speed;
                    $item['overallScore'] = $r->score;
                } else {
                    continue;
                }
                array_push($resultArray['advisory'], $item);
            }
        }
        $resultArray['count'] = count($reading);
        return Response::json($resultArray);
    }

    /**
     * 某篇报告的详情
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function detail()
    {
        $input = Input::all();
        $guid = $input['guid'];

        $resultArray = array();
        $advisory = Advisory::where('guid', $guid)->first();

        if (count($advisory)) {
            $resultArray['id'] = $advisory->id;
            $resultArray['guid'] = $advisory->guid;
            $resultArray['read_id'] = $advisory->reading_id;

            $reading = Reading::find($advisory->reading_id);
            if (count($reading)) {
                $resultArray['pronunciationScore'] = (float)$reading->pronunciation_score;
                $resultArray['intonationScore'] = (float)$reading->intonation_score;
                $resultArray['stressScore'] = (float)$reading->stress_score;
                $resultArray['fluencyScore'] = (float)$reading->fluency_score;
                $resultArray['speedScore'] = (float)$reading->speed_score;
                $resultArray['speedSituation'] = empty($reading->speed_situation) ? "" : $reading->speed_situation;
                $resultArray['speed'] = (float)$reading->speed;
                $resultArray['overallScore'] = $reading->score;
                $resultArray['advisory'] = empty($reading->report) ? '' : url($reading->report);
                $resultArray['lesson_id'] = $reading->lesson_id;
            }

            $lesson = Lesson::find($reading->lesson_id);
            if (count($lesson)) {
                $resultArray['title'] = $lesson->title;
            }

            $analysis = Analysis::where('lesson_id', $reading->lesson_id)->first();
            if (count($analysis)) {
                $resultArray['analysis_guid'] = $analysis->guid;
            } else {
                $resultArray['analysis_guid'] = '';
            }
            $resultArray['status'] = $this->readingStatus[$reading->status];
            $resultArray['is_homework'] = ($reading->type == 2) ? true : false;
            $resultArray['created_at'] = date($reading->created_at);
        }

        return Response::json($resultArray);
    }

    /**
     * 获取某次录音的报告
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function read()
    {
        $input = Input::all();
        $readID = $input['readID'];

        $resultArray = array();
        $reading = Reading::find($readID);
        if (count($reading)) {
            $resultArray['read'] = array();
            $item = array();

            $item['id'] = $reading->id;
            $item['lesson_id'] = $reading->lesson_id;
            $lesson = Lesson::find($reading->lesson_id);
            if (count($lesson)) {
                $item['title'] = $lesson->title;
            }

            $analysis = Analysis::where('lesson_id', $reading->lesson_id)->first();
            if (count($analysis)) {
                $item['analysis_guid'] = $analysis->guid;
            } else {
                $item['analysis_guid'] = '';
            }

            $item['is_homework'] = ($reading->type == 2) ? true : false;
            $item['created_at'] = date($reading->created_at);

            $path = $reading->report;
            $item['pronunciationScore'] = (float)$reading->pronunciation_score;
            $item['intonationScore'] = (float)$reading->intonation_score;
            $item['stressScore'] = (float)$reading->stress_score;
            $item['fluencyScore'] = (float)$reading->fluency_score;
            $item['speedScore'] = (float)$reading->speed_score;
            $item['speed'] = (float)$reading->speed;
            $item['speedSituation'] = empty($reading->speed_situation) ? "" : $reading->speed_situation;
            $item['overallScore'] = $reading->score;

            $advisory = Advisory::where('reading_id', $readID)->first();
            if (count($advisory)) {
                $item['advisory_id'] = $advisory->id;
                $item['advisory_guid'] = $advisory->guid;
            } else {
                $item['advisory_id'] = 0;
                $item['advisory_guid'] = '';
            }
            $item['advisory'] = empty($path) ? '' : url($path);

            $item['status'] = $this->readingStatus[$reading->status];

            array_push($resultArray['read'], $item);
        }
        $resultArray['count'] = count($reading);
        return Response::json($resultArray);
    }

    /**
     * 根据课文unique编号获取报告
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function lessonUnique()
    {
        $input = Input::all();
        $lessonUnique = $input['lessonUnique'];

        $lessonIds = Lesson::where('lesson_unique', $lessonUnique)
                        ->get(array('id'))
                        ->toArray();
        if (!count($lessonIds)) {
            return Response::json(array('advisory' => array(), 'count' => 0, 'start' => $input['start']));
        }

        $reading = Reading::where('student_id', $input['studentID'])
                            ->whereIn('lesson_id', $lessonIds)
                            ->take($input['count'])
                            ->skip($input['start'])
                            ->orderBy($input['orderby'], $input['sort'])
                            ->get();

        $resultArray = array();
        $resultArray['read'] = array();
        if (count($reading)) {
            foreach ($reading as $key => $r) {
                $item = array();

                $item['id'] = $r->id;
                $item['lesson_id'] = $r->lesson_id;
                $lesson = Lesson::find($r->lesson_id);
                if (count($lesson)) {
                    $item['title'] = $lesson->title;
                } else {
                    $item['title'] = '';
                }

                $analysis = Analysis::where('lesson_id', $r->lesson_id)->first();
                if (count($analysis)) {
                    $item['analysis_guid'] = $analysis->guid;
                } else {
                    $item['analysis_guid'] = '';
                }

                $item['status'] = $this->readingStatus[$r->status];
                $item['is_homework'] = ($r->type == 2) ? true : false;
                $item['created_at'] = date($r->created_at);

                $item['pronunciationScore'] = (float)$r->pronunciation_score;
                $item['intonationScore'] = (float)$r->intonation_score;
                $item['stressScore'] = (float)$r->stress_score;
                $item['fluencyScore'] = (float)$r->fluency_score;
                $item['speedScore'] = (float)$r->speed_score;
                $item['speed'] = (float)$r->speed;
                $item['speedSituation'] = empty($r->speed_situation) ? "" : $r->speed_situation;
                $item['overallScore'] = $r->score;

                $advisory = Advisory::where('reading_id', $r->id)->first();
                if (count($advisory)) {
                    $item['advisory_id'] = $advisory->id;
                    $item['advisory_guid'] = $advisory->guid;
                } else {
                    $item['advisory_id'] = 0;
                    $item['advisory_guid'] = '';
                }
                $item['advisory'] = empty($r->report) ? '' : url($r->report);
                array_push($resultArray['read'], $item);
            }
        }
        $resultArray['count'] = count($reading);
        $resultArray['start'] = (int)($input['start']);

        return Response::json($resultArray);
    }
}
