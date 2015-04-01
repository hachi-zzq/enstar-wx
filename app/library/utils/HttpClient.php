<?php namespace Enstar\Utils;

/*
 * @brief url封装类，将常用的url请求操作封装在一起
 * */
class HttpClient
{
    private $error;

    public function __construct()
    {
        $this->error = '';
    }

    /**
     * combineURL
     * 拼接url
     * @param string $baseURL 基于的url
     * @param array $keysArr 参数列表数组
     * @return string           返回拼接的url
     */
    public function combineURL($baseURL, $keysArr)
    {
        $combined = $baseURL . "?";
        $valueArr = array();

        foreach ($keysArr as $key => $val) {
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&", $valueArr);
        $combined .= ($keyStr);

        return $combined;
    }

    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url 请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public function get_contents($url)
    {
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        //-------请求为空
        if (empty($response)) {
            $this->error = 'Server can not request https. Maybe not open curl support, please open curl support and restart web server.';
        }

        return $response;
    }

    /**
     * get
     * get方式请求资源
     * @param string $url 基于的baseUrl
     * @param array $keysArr 参数列表数组
     * @return string         返回的资源内容
     */
    public function get($url, $keysArr)
    {
        $combined = $this->combineURL($url, $keysArr);
        return $this->get_contents($combined);
    }

    /**
     * post
     * post方式请求资源
     * @param string $url 基于的baseUrl
     * @param array $keysArr 请求的参数列表
     * @param int $flag 标志位
     * @return string           返回的资源内容
     */
    public function post($url, $keysArr, $flag = 0)
    {

        $ch = curl_init();
        if (!$flag) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $keysArr);
        curl_setopt($ch, CURLOPT_URL, $url);
        $ret = curl_exec($ch);

        curl_close($ch);
        return $ret;
    }

    /**
     * 下载远程文件到本地
     * @param $url
     * @param string $save_dir 文件保存路径
     * @param string $filename 文件名
     * @param int $type 文件类型
     * @return array
     */
    public function download($url, $save_dir = '', $filename = '', $type = 0)
    {
        if (trim($url) == '') {
            return array('file_name' => '', 'save_path' => '', 'error' => 1);
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (trim($filename) == '') {//保存文件名
            $ext = strrchr($url, '.');
//            if ($ext != '.gif' && $ext != '.jpg') {
//                return array('file_name' => '', 'save_path' => '', 'error' => 3);
//            }
            $filename = time() . $ext;
            dd($filename);
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 3;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);

            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $data = ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'w');
        fwrite($fp2, $data);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }
}
