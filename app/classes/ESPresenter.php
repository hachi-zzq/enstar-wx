<?php
/**
 * 
 *
 * Date: 14-12-9
 * Time: 下午4:54
 * @author Jun<jun.zhu@enstar.com>
 * @copyright AUTOTIMING INC PTE. LTD.
 */

class ESPresenter extends Illuminate\Pagination\Presenter {

    public function getActivePageWrapper($text)
    {
        //return '<li class="current"><span>'.$text.'</span></li>';
        return '<li class="current"><a href="javascript:void(0)">'.$text.'</a></li>';
    }

    public function getDisabledTextWrapper($text)
    {
        //return '<li class="disabled"><span>'.$text.'</span></li>';
        return '<li class="unavailable"><a href="javascript:void(0)">'.$text.'</a></li>';
    }

    public function getPageLinkWrapper($url, $page, $rel = null)
    {
        $rel = is_null($rel) ? '' : ' rel="'.$rel.'"';

        return '<li><a href="'.$url.'"'.$rel.'>'.$page.'</a></li>';

        //return '<li><a href="'.$url.'">'.$page.'</a></li>';
    }

}

