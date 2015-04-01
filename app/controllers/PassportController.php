<?php

class PassportController extends BaseController {


    /**
     * 登录页面
     * @return mixed
     * @author Jun<jun.zhu@enstar.com>
     */
    public function signin()
    {
        return View::make('signin');

    }

    /**
     * 用户登陆
     * @return string
     * @author Jun<jun.zhu@enstar.com>
     */
    public function postSignIn()
    {
        //表单验证规则
        $input = Input::only('username', 'password','remember');
        $rules = array(
            'username' => array('required', 'min:4', 'max:30'),
            'password' => array('required', 'min:6', 'max:20'),
        );
        $v = Validator::make($input, $rules);

        if ($v->fails()) {
            return ESHelp::json_response(-1,'格式不符');
        }


        //记住我
        if ($input['remember']) {
            $isRemember = true;
        } else {
            $isRemember = false;
        }

        //验证登录
        if (!Auth::attempt(array('username' => $input['username'], 'password' => $input['password']), $isRemember)) {
            return ESHelp::json_response('-2','账号密码不正确');
        }

        //状态验证
        $userStatus = Auth::user()->status;
        if ($userStatus == 0) { //被冻结
            return $this->json_response('-3', '账号被冻结');
        }

        //Todo:更新用户最后登录时间和IP

        return Redirect::route('adminHome');

    }

    /**
     * 注销登录
     * @return mixed
     * @author Jun<jun.zhu@enstar.com>
     */
    public function logout()
    {
        Auth::logout();
        return Redirect::route('signin');
    }






}
