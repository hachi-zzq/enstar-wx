<?php
namespace Enstar\Controller\Rest;

use Illuminate\Support\Facades\Redis;
use Config;
use RestLog;
use Session;
use Response;
use Device;
use Input;
use Validator;

/**
 * RestAPI 基类
 * @author Hanxiang<hanxiang.qiu@enstar.com>
 */
class BaseController extends \BaseController
{
    public function index()
    {
        return Response::json(array(
            'request_id' => 0,
            'msgcode' => '10000',
            'message' => 'success',
            'response' => null,
            'version' => 'v1.0',
            'servertime' => time()
        ));
    }

    /**
     * 接口状态TODO
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public function startup()
    {
        $input = Input::all();
        $validator = Validator::make($input, array(
            'device_num' => 'required',
            'device_type' => 'required'
        ));
        if($validator->fails()){
            return $this->encodeResult('20001', $validator->messages()->first(), null);
        }

        $device = Device::where('num1', $input['device_num'])->first();
        if (!$device) {
            $newDevice = new Device();
            $newDevice->num1 = $input['device_num'];
            $newDevice->type = $input['device_type'];
            $newDevice->text = (isset($input['device_text'])) ? serialize($input['device_text']) : '';
            $newDevice->save();
        }

        $asrStatus = $this->getASRStatus();
        $response = array(
            'latest_version' => '0.5',
            'latest_version_build' => Config::get('app.latest_version_build'),
            'server_status' => 'ALLOW',
            'server_stauts_text' => '正常',
            'asr_status' => $asrStatus['status'],
            'asr_status_text' => $asrStatus['text'],
            'upgrade_url' => 'http://fir.im/73ch'
        );
        return $this->encodeResult('10000', 'success', $response);
    }

    /**
     * 获取 ASR 服务状态
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @return string
     */
    private function getASRStatus()
    {
        $redis = Redis::connection();
        $key = Config::get('app.enstar_asr_status_key');

        $status = array('status' => 'ON', 'text' => '正常');
        try {
            $qstr = $redis->get($key);
            if ($qstr == 'on') {
                return $status;
            } elseif ($qstr == 'off') {
                $status['status'] = 'OFF';
                $status['text'] = '不可用';
                return $status;
            } else {
                $status['status'] = 'OFF';
                $status['text'] = '不可用';
                return $status;
            }
        } catch (\Exception $e) {
            return array('status' => 'OFF', 'text' => '不可用');
        }
    }

    /**
     * 编码统一返回格式TODO
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $msgcode
     * @param $message
     * @param $response
     * @return array
     */
    protected function encodeResult($msgcode, $message = NULL, $response = NULL)
    {
        $log = new RestLog();

        // exception 'Exception' with message 'Serialization of 'Symfony\Component\HttpFoundation\File\UploadedFile' is not allowed'
        // $log->request = serialize(\Input::all());
        // $log->request = serialize(\Input::except('readFile'));
        $log->request = serialize(\Input::except('readFile'));
        $log->request_route = \Route::currentRouteName();
        // $log->response = serialize($response);
        $log->msgcode = $msgcode;
        $log->message = $message;
        $log->client_ip = \Request::getClientIp();
        $log->client_useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
        $log->user_id = (Session::get('uid')) ? Session::get('uid') : 0;
        $log->save();

        $result = array(
            'request_id' => $log->id,
            'msgcode' => $msgcode,
            'message' => $message,
            'response' => $response,
            'version' => 'v1.0',
            'servertime' => time()
        );

        return Response::json($result);
    }

    /**
     * 处理可选参数
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    protected function getOptionalInput()
    {
        $input = Input::all();
        $start = isset($input['start']) ? $input['start'] : (int)0;
        $count = isset($input['count']) ? $input['count'] : (int)10;

        if (isset($input['order'])) {
            switch ($input['order']) {
                case '+created_at':
                    $orderby = 'created_at';
                    $sort = 'asc';
                    break;
                case '-created_at':
                    $orderby = 'created_at';
                    $sort = 'desc';
                default:
                    $orderby = 'created_at';
                    $sort = 'desc';
                    break;
            }
        } else {
            $orderby = 'created_at';
            $sort = 'desc';
        }

        return array(
            'start' => $start,
            'count' => $count,
            'orderby' => $orderby,
            'sort' => $sort
        );
    }
}
