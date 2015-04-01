<?php

/**
 * ES帮助类
 * Class ESHelp
 * @author Jun<jun.zhu@enstar.com>
 * @copyright AUTOTIMING INC PTE. LTD.
 */
class ESHelp
{

    /**
     * 通用返回前端JSON格式
     * @param $code
     * @param $msg
     * @param null $url
     * @return string
     * @author Jun<jun.zhu@enstar.com>
     */
    public static function json_response($code, $msg, $url = NULL)
    {
        $response = array('code' => $code, 'msg' => $msg, 'redirectUrl' => $url);
        return json_encode($response);
    }


    /**
     * #句子处理
     * @param $rawContent
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     *  @demo
     * -------------------------------------
     *  [[A]] i am a good boy
     *  ^[[B]] yes
     *  ^^[[A]] nice to meet you
     */
    public static function sentenceHandler($rawContent){
        $arrRet = array();
        $arrLine = explode("\n",$rawContent);
        if($arrLine){
            foreach($arrLine as $k=>$line){
                $line = rtrim($line,"\r");
                if($line == '') continue;
//                $arrRet[$k]['sentence'] = preg_replace('/\]/','',preg_replace('/\[/','',trim($line,'^')));
                $arrRet[$k]['sentence'] = preg_replace('/\[\[([^\]]+)\]\]/','',trim($line,'^ '));

                if(preg_match('/^\^[^\^]/',$line)){
                    $arrRet[$k]['format'] = 'L';
                }
                if(preg_match('/^\^\^/',$line)){
                    $arrRet[$k]['format'] = 'P';
                }
                if(preg_match('/^[^\^]/',$line)){
                    $arrRet[$k]['format'] = null;
                }
                if(preg_match('/\[\[(.*?)\]\]/',$line,$match)){
                    $arrRet[$k]['prefix'] = $match[1];
                }else{
                    $arrRet[$k]['prefix'] = null;
                }
            }
        }
        return $arrRet;
    }


    /**
     * 时间转换
     * @param $the_time
     * @return string
     * @author Jun<jun.zhu@enstar.com>
     */
    function time_tran($the_time){
        $now_time = date("Y-m-d H:i:s",time());
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if($dur < 0){
            return $the_time;
        }else{
            if($dur < 60){
                //return $dur.'秒前';
                return '刚刚';
            }else{
                if($dur < 3600){
                    //return floor($dur/60).'分钟前';
                    return '刚刚';
                }else{
                    if($dur < 86400){
                        return floor($dur/3600).'小时前';
                    }else{
                        if($dur < 259200){//3天内
                            return floor($dur/86400).'天前';
                        }else{
                            return $the_time;
                        }
                    }
                }
            }
        }
    }


    /**
     * @前台渲染课文格式
     * @param $lessonContent
     * @author zhengqian.zhu@enstar.com
     */
    static function handerLessonContent($lessonContent){
        $rawContent = $lessonContent;
        $rawContent = "<p>".$rawContent."</p>";
        $rawContent = preg_replace('/(\^\^)/','</p><p>',$rawContent);
        $rawContent = str_replace('<p></p>','',$rawContent);
        $rawContent = str_replace('{','',$rawContent);
        $rawContent = str_replace('}','',$rawContent);
        $rawContent = str_replace('^', '<br />', $rawContent);
        return $rawContent;
    }

    /**
     * 获取IP地址
     * @return string
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     */
    public static function getClientIP()
    {
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        elseif (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        elseif (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        elseif (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    /**
     * 验证手机号码
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $mobile
     * @return boolean
     */
    public static function verifyMobile($mobile)
    {
        if (preg_match('/^[1][34578]\d{9}$/', $mobile)) {
            return true;
        } else {
            return false;
        }
    }
}
