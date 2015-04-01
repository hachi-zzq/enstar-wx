<?php

/**
 * 日期转换类，小学生专用版
 * Class ESDate
 * @author Jun<jun.zhu@enstar.com>
 * @copyright AUTOTIMING INC PTE. LTD.
 */
class ESDate
{
    function  __construct($inputTime)
    {
        $this->createtime = time();
        $this->gettime = strtotime($inputTime);;
    }

    function getSeconds()
    {
        return $this->createtime - $this->gettime;
    }

    function getMinutes()
    {
        return ($this->createtime - $this->gettime) / (60);
    }

    function getHours()
    {
        return ($this->createtime - $this->gettime) / (60 * 60);
    }

    function getDay()
    {
        return ($this->createtime - $this->gettime) / (60 * 60 * 24);
    }

    function getMonth()
    {
        return ($this->createtime - $this->gettime) / (60 * 60 * 24 * 30);
    }

    function getYear()
    {
        return ($this->createtime - $this->gettime) / (60 * 60 * 24 * 30 * 12);
    }

    function getTime()
    {
        if ($this->getYear() > 1) {
            if ($this->getYear() > 2) {
                return date("Y-m-d", $this->gettime);
                exit();
            }
            return intval($this->getYear()) . "年前";
            exit();
        }
        if ($this->getMonth() > 1) {
            return intval($this->getMonth()) . "月前";
            exit();
        }
        if ($this->getDay() > 1) {
            return intval($this->getDay()) . "天前";
            exit();
        }
        if ($this->getHours() > 1) {
            return intval($this->getHours()) . "小时前";
            exit();
        }
        if ($this->getMinutes() > 1) {
            //return intval($this->getMinutes()) . "分钟前";
            return '刚刚';
            exit();
        }
        if ($this->getSeconds() > 1) {
            //return intval($this->getSeconds() - 1) . "秒前";
            return '刚刚';
            exit();
        }
    }
}