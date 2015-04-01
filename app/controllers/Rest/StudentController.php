<?php
namespace Enstar\Controller\Rest;

use Input;
use Response;
use Student;
use Hash;
use Classes;
use School;
use ClassUser;
use User;
use UserProfile;
use CardStudent;

/**
 * RestAPI 学生类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class StudentController extends BaseController
{
    /**
     * 学生登录验证
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function login()
    {
        $input = Input::all();
        $username = $input['username'];
        $password = $input['password'];

        $student = Student::where('username', $username)->first();
        if (!count($student)) {
            return Response::json(array('student_id' => 0, 'checkStatus' => 0));
        }

        if (!Hash::check($password, $student->password)) {
            return Response::json(array('student_id' => 0, 'checkStatus' => 1));
        }        

        return Response::json(array(
            'student_id' => $student->id,
            'name' => $student->name,
            'avatar' => $student->avatar
        ));
    }

    /**
     * 学生个人信息
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function profile()
    {
        $input = Input::all();
        $studentID = $input['student_id'];

        $resultArray = array();

        $student = Student::find($studentID);
        if (!count($student)) {
            return null;
        }
        $resultArray['name'] = $student->name;
        $resultArray['status'] = ($student->status == 1) ? 'ACTIVATED' : 'UNACTIVATED';
        $resultArray['avatar'] = empty($student->avatar) ? '' : $student->avatar;
        $resultArray['gender'] = empty($student->gender) ? '' : $student->gender;

        // class
        $classArray = array();
        $class = Classes::find($student->class_id);
        if (count($class)) {
            $classArray['id'] = $class->id;
            $classArray['name'] = $class->name;
            $classArray['school_id'] = $class->school_id;
            $classArray['description'] = $class->description;
            $classArray['card_class_id'] = $class->card_class_id;
            $classArray['status'] = ($class->status == 1) ? 'ACTIVATED' : 'UNACTIVATED';
        }
        $resultArray['class'] = $classArray;

        // school
        $schoolArray = array();
        $school = School::where('id', $class->school_id)->where('status', 1)->first();
        if (count($school)) {
            $schoolArray['id'] = $school->id;
            $schoolArray['name'] = $school->name;
            $schoolArray['country'] = $school->country;
            $schoolArray['province'] = $school->province;
            $schoolArray['city'] = $school->city;
            $schoolArray['address'] = $school->address;
            $schoolArray['zip'] = $school->zip;
            $schoolArray['email'] = $school->email;
            $schoolArray['contacts'] = $school->contacts;
            $schoolArray['contacts_mobile'] = $school->contacts_mobile;
            $schoolArray['scale'] = $school->scale;
            $schoolArray['expiration_date'] = $school->expiration_date;
            $schoolArray['status'] = 'NORMAL';
            $schoolArray['logo'] = url($school->logo);
        }
        $resultArray['school'] = $schoolArray;

        // teacher-class
        $userClass = ClassUser::where('class_id', $student->class_id)->first();
        $teacher = User::find($userClass->user_id);
        if (count($teacher)) {
            $resultArray['teacher'] = $teacher->name;
        } else {
            $resultArray['teacher'] = '';
        }

        $teacherProfile = UserProfile::where('user_id', $userClass->user_id)->first();
        if (count($teacherProfile)) {
            $resultArray['teacher_mobile'] = $teacherProfile->mobile;
            $resultArray['teacher_email'] = $teacherProfile->email;
        } else {
            $resultArray['teacher_mobile'] = '';
            $resultArray['teacher_email'] = '';
        }
        return Response::json($resultArray);
    }

    /**
     * 获取公告
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function notices()
    {
        $input = Input::all();
        $studentID = isset($input['student_id']) ? $input['student_id'] : 0;

        $noticeArray = null;
        $student = Student::find($studentID);
        if (count($student)) {
            $classID = $student->class_id;
            $notice = Classes::find($classID)->notices->where('status', '=', 1)->first();
            if (count($notice)) {
                $noticeArray = array();
                $noticeArray['id'] = $notice->id;
                $noticeArray['title'] = $notice->title;
                $noticeArray['content'] = $notice->content;
                $noticeArray['class_id'] = $notice->class_id;
                $noticeArray['status'] = 'NORMAL';
                $noticeArray['created_at'] = date($notice->created_at);
            }
        }
        return Response::json($noticeArray);
    }

    /**
     * 设置头像
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function avatar()
    {
        $input = Input::all();
        $studentID = isset($input['student_id']) ? $input['student_id'] : 0;
        $avatar = isset($input['avatar']) ? $input['avatar'] : '';

        Student::where('id', $studentID)->update(array('avatar' => $avatar));
        return Response::json(array('r' => 1));
    }

    /**
     * 修改密码
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function changePassword()
    {
        $input = Input::all();
        $studentID = isset($input['student_id']) ? $input['student_id'] : 0;
        $oldPasswd = isset($input['old_passwd']) ? $input['old_passwd'] : 0;
        $newPasswd = isset($input['new_passwd']) ? $input['new_passwd'] : 0;

        $student = Student::find($studentID);
        if (count($student)) {
            if (Hash::check($oldPasswd, $student->password)) {
                Student::where('id', $studentID)->update(array('password' => Hash::make($newPasswd)));
            } else {
                return Response::json(array('r' => 3));
            }
        } else {
            return Response::json(array('r' => 2));
        }

        return Response::json(array('r' => 1));
    }

    /**
     * 账号激活
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function active()
    {
        $input = Input::all();
        $cardnum = isset($input['cardnum']) ? $input['cardnum'] : '0';
        $usernum = isset($input['usernum']) ? $input['usernum'] : '0';
        $password = isset($input['password']) ? $input['password'] : '0';

        $stuCard = CardStudent::where('card_number', $cardnum)->where('status', 0)->first();
        if (!$stuCard) {
            return Response::json(array('r' => 1));
        }

        $student = Student::where('username', $usernum)->where('status', 0)->first();
        if (!$student) {
            return Response::json(array('r' => 2));
        }

        // 是否为对应的班级卡
        $stuClass = Classes::find($student->class_id);
        if ($stuCard->card_class_id != $stuClass->card_class_id) {
            return Response::json(array('r' => 3));
        }

        // 是否为对应的学校
        if ($stuCard->school_id != $student->school_id) {
            return Response::json(array('r' => 4));
        }

        // 是否已过期
        if (date($stuCard->expiration_date) < date('Y-m-d H:i:s')) {
            return Response::json(array('r' => 5));
        }

        // 更新学生状态为已激活
        $student->password = Hash::make($password);
        $student->status = 1;
        $student->card_student_id = $stuCard->id;
        $student->save();

        // 更新学生卡状态为已使用
        $stuCard->status = 1;
        $stuCard->save();
        return Response::json(array('r' => 0));
    }
}
