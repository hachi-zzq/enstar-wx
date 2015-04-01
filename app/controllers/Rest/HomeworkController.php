<?php
namespace Enstar\Controller\Rest;

use Input;
use Response;
use Homework;
use Lesson;
use Student;
use Enstar\Library\HomeworkScore;

/**
 * RestAPI 作业类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class HomeworkController extends BaseController
{

    /**
     * 获取某个学生的所有作业
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function homeworks()
    {
        $input = Input::all();
        $studentID = $input['student_id'];

        $student = Student::find($studentID);
        if (count($student)) {
            $classID = $student->class_id;
        } else {
            $classID = 0;
        }

        $homeworksArray = array();
        $homeworks = Homework::where('user_id', $studentID)
                            ->where('class_id', $classID)
                            ->take($input['count'])
                            ->skip($input['start'])
                            ->orderBy($input['orderby'], $input['sort'])
                            ->get();
        if (count($homeworks)) {
            $homeworksArray['homeworks'] = array();
            foreach ($homeworks as $key => $h) {
                $item = array();
                $item['id'] = $h->id;
                $item['lesson_id'] = $h->lesson_id;
                $item['type'] = $h->type;
                $item['type_value'] = $h->type_value;
                $item['description'] = $h->description;
                $item['start_time'] = $h->start_time;
                $item['end_time'] = $h->end_time;

                $homeworkScore = HomeworkScore::calculateScore($studentID, $h->id);
                $item['score'] = $homeworkScore['complete_value'];
                $item['status'] = $homeworkScore['status'];

                $lesson = Lesson::find($h->lesson_id);
                if (count($lesson)) {
                    $item['title'] = $lesson->title;
                } else {
                    $item['title'] = '';
                }
                array_push($homeworksArray['homeworks'], $item);
            }
            $homeworksArray['count'] = count($homeworks);
        }

        return Response::json($homeworksArray);
    }

    /**
     * 获取某个学生的所有作业
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function detail()
    {
        $input = Input::all();
        $homeworkID = $input['id'];
        $studentID = $input['student_id'];

        $homeworkArray = array();
        $homework = Homework::find($homeworkID);
        if (count($homework)) {
            $homeworkArray['id'] = $homework->id;
            $homeworkArray['lesson_id'] = $homework->lesson_id;
            $homeworkArray['type'] = $homework->type;
            $homeworkArray['type_value'] = $homework->type_value;
            $homeworkArray['description'] = $homework->description;
            $homeworkArray['start_time'] = $homework->start_time;
            $homeworkArray['end_time'] = $homework->end_time;

            $homeworkScore = HomeworkScore::calculateScore($studentID, $homeworkID);;
            $homeworkArray['score'] = $homeworkScore['complete_value'];
            $homeworkArray['status'] = $homeworkScore['status'];

            $lesson = Lesson::find($homework->lesson_id);
            if ($lesson) {
                $homeworkArray['title'] = $lesson->title;
            } else {
                $homeworkArray['title'] = '';
            }
        }

        return Response::json($homeworkArray);
    }
}
