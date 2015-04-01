<?php
namespace Enstar\Controller\Admin;

use \View;
use \Request;
use \Input;
use \Validator;
use \Enstar\Library\MQ;
use \User;
use \Redirect;
use \Hash;
use \School;
use \Cache;

/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-19 下午3:27
 */
class UserController extends BaseController
{

    /**
     * home index page
     * @author zhengqian.zhu@enstar.com
     * @return null
     */
    public function index()
    {
        $users = User::orderBy('created_at','DESC')->paginate(20);
        return View::make('admin.user.index')->with('users',$users);
    }


    /**
     * #创建用户
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function create()
    {
        return View::make('admin.user.create');
    }


    /**
     * #postcreate
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function postCreate()
    {
        $inputData = Input::only('username','name','password','re_password','type','school_id');
        $validator = Validator::make($inputData,array(
            'username'=>'required|min:4|max:30',
            'name'=>'required',
            'password'=>'required|same:re_password|min:6|max:20',
            're_password'=>'required',
            'type'=>'required',
            'school_id'=>'required',
        ));

        if($validator->fails()){
            return Redirect::route('adminUserCreate')->with('error_tips',$validator->messages()->first());
        }

        if($inputData['type'] != 1 && $inputData['school_id'] == 0){
            return Redirect::route('adminUserCreate')->with('error_tips','请选择学校');
        }

        //TODO 验证重复
        $user = new User();
        $user->username = $inputData['username'];
        $user->name = $inputData['name'];
        $user->password = Hash::make($inputData['password']);
        $user->school_id = $inputData['school_id'];
        $user->group_id = $inputData['type'];
        $user->status = 1;
        $user->save();
        return Redirect::route('adminUserIndex')->with('success_tips','用户创建成功');
    }

    /**
     * #修改
     * @param $user_id
     * @author zhengqian.zhu@enstar.com
     */
    public function modify($user_id)
    {
        $user = User::find($user_id);
        return View::make('admin.user.modify')->with('user',$user);
    }

    /**
     * #修改post
     * @param null
     * @author zhengqian.zhu@enstar.com
     */
    public function postModify()
    {
        $inputData = Input::only('user_id','username','name','password','re_password','type','school_id');
        $validator = Validator::make($inputData,array(
            'user_id'=>'required',
            'username'=>'required',
            'name'=>'required',
            'password'=>'',
            're_password'=>'',
            'type'=>'required',
            'school_id'=>'required',
        ));

        if($validator->fails()){
            return Redirect::route('adminUserCreate')->with('error_tips',$validator->messages()->first());
        }

        if($inputData['password'] != $inputData['re_password']){
            return Redirect::route('adminUserCreate')->with('error_tips','密码不一致');
        }

        //TODO 验证重复
        $user = User::find($inputData['user_id']);
        $user->username = $inputData['username'];
        $user->name = $inputData['name'];
        if($inputData['password']){
            $user->password = Hash::make($inputData['password']);
        }
        $user->school_id = $inputData['school_id'];
        $user->group_id = $inputData['type'];
        $user->status = 1;
        $user->save();
        return Redirect::route('adminUserIndex')->with('success_tips','用户修改成功');
    }

    /**
     * #删除用户
     * @param $user_id
     * @author zhengqian.zhu@enstar.com
     */
    public function destroy($user_id)
    {
        User::find($user_id)->delete();
        return Redirect::route('adminUserIndex')->with('success_tips','用户删除成功');
    }


}

