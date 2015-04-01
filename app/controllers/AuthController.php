<?php

class AuthController extends BaseController {

    /**
     * @用于服务器接入的测试
     * check token
     */
    public function checkToken()
    {
        echo Input::get("echostr");
    }
}
