<?php
namespace Enstar\Library\LeanCloudService;

class Base
{
    protected $_header = array(
        'X-AVOSCloud-Application-Id: taej0ynj3z5w5kh3juuaerkynmzxcp0swjihz9bmtwwow2fq',
        'X-AVOSCloud-Application-Key: fo8nnmbfacpgwrs545dosi3imwget9k8vt66jamlw55ywica',
        'Content-Type: application/json'
    );

    protected function xCurl($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->_header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $resultJson = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($resultJson, true);
        if (empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}
