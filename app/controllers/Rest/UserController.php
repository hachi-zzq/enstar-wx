<?php
namespace Enstar\Controller\Rest;

//use Enstar\Library\Sms;
use Illuminate\Support\Facades\Validator;
use Input;
use Enstar\Library\LeanCloudService\Sms;
use SmsCode;
use Config;
use User;
use Device;
use J20\Uuid\Uuid;
use Feedback;
use Session;
use Datetime;

class UserController extends BaseController
{
    /**
     * TODO 首页，获取统计数据
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function home()
    {
        $result = array(
            'book' => array(
                'id' => 1,
                'title' => '新概念英语第二册',
                'subtitle' => 'Progress & Practice',
                'version' => "1.0",
                'cover' => 'http://dev.ncerocket.com/upload/cover/book1.png',
                'publisher' => '朗文出版社',
                'publish_time' => '2008-12-30',
                'description' => '一本神奇的书'
            ),
            'statistics' => array(
                'total' => 96,
                'learned' => 36,
                'complete' => 25
            ),
            'last_learned' => array(
                'lesson_id' => 21,
                'lesson_key' => 'B2U0L1',
                'title' => 'Lesson 1 A private conversation'
            )
        );
        return $this->encodeResult('10500', 'success', $result);
    }

    /**
     * 发送短信验证码
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function sendSmsCode()
    {
        // get params
        $input = Input::all();
        $validator = Validator::make($input, array(
            'mobile' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('20101', $validator->messages()->first(), null);
        }

        // verify mobile number
        $mobile = $input['mobile'];
        $verify = \ESHelp::verifyMobile($mobile);
        if (!$verify) {
            return $this->encodeResult('20102', 'mobile invalid', null);
        }

        // check frequency
        $ip = \ESHelp::getClientIP();
        $smsCode = SmsCode::whereRaw('mobile=? OR ip=?', array($mobile, $ip))
                          ->orderBy('created_at', 'desc')
                          ->first();

        if ($smsCode) {
            if (time() - strtotime($smsCode->created_at) < Config::get('app.sms_interval_seconds')) {
                return $this->encodeResult('20103', 'sms busy', null);
            }
        }

        // save new smsCode
        $user = User::where('mobile', $mobile)->first();
        $type = ($user) ? 2 : 1;
        $newSmsCode = new SmsCode();
        $newSmsCode->mobile = $mobile;
        $newSmsCode->code = '0';
        $newSmsCode->type = $type;
        $newSmsCode->ip = $ip;
        $newSmsCode->save();

        // send sms
        $sent = Sms::send($mobile);

        // return
        if ($sent) {
            return $this->encodeResult('10100', 'success', null);
        } else {
            return $this->encodeResult('20104', 'fail, send sms error', null);
        }
    }

    /**
     * 登录
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function login()
    {
        // get params
        $input = Input::all();
        $validator = Validator::make($input, array(
            'mobile' => 'required',
            'code' => 'required',
            'device_num' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('20201', $validator->messages()->first(), null);
        }

        // select device
        $device = Device::where('num1', $input['device_num'])->first();
        if (!$device) {
            return $this->encodeResult('20202', 'device invalid', null);
        }
        $device_id = $device->id;

        $checkCode = Sms::verify($input['mobile'], $input['code']);
        if (!$checkCode) {
            return $this->encodeResult('20203', 'code invalid', null);
        }

        // update user token
        $today = new Datetime();
        $token = Uuid::v4(false);
        $token_expiration = $today->modify('+7 days');

        $user = User::where('mobile', $input['mobile'])->first();
        $user->token = $token;
        $user->token_expiration = $token_expiration;
        $user->device_id = $device_id;
        $user->ip = \ESHelp::getClientIP();
        $user->save();

        // update device last sync time
        $device->last_sync_time = date('Y-m-d H:i:s');
        $device->save();

        // TODO
        $response = array(
            'token' => $token
        );
        return $this->encodeResult('10200', 'success', $response);
    }

    /**
     * 注册
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function signup()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'mobile' => 'required',
            'code' => 'required',
            'device_num' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('20301', $validator->messages()->first(), null);
        }

        // verify code
        $checkCode = Sms::verify($input['mobile'], $input['code']);
        if (!$checkCode) {
            return $this->encodeResult('20303', 'code invalid', null);
        }

        // check device
        $device = Device::where('num1', $input['device_num'])->first();
        if (!$device) {
            return $this->encodeResult('20302', 'device invalid', null);
        }

        // add new user
        $today = new Datetime();
        $token_expiration = $today->modify('+7 days');
        $token = Uuid::v4(false);

        $user = new User();
        $user->mobile = $input['mobile'];
        $user->token = $token;
        $user->token_expiration = $token_expiration;
        $user->device_id = $device->id;
        $user->ip = \ESHelp::getClientIP();
        $user->save();

        // TODO
        $response = array(
            'token' => $token
        );
        return $this->encodeResult('10300', 'success', $response);
    }

    /**
     * 提交意见反馈
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function feedback()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'content' => array('required', 'max:200'),
            'contact' => array('min:0', 'max:50')
        ));
        if($validator->fails()){
            return $this->encodeResult('20401', $validator->messages()->first(), null);
        }

        $contact = (isset($input['contact'])) ? $input['contact'] : '';
        $feedback = new Feedback();
        $feedback->user_id = Session::get('uid');
        $feedback->content = $input['content'];
        $feedback->contact = $contact;
        $feedback->save();

        return $this->encodeResult('10400', 'success', null);
    }
}
