<?php

namespace Enstar\Controller\Admin;

use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use \Notice;
use \Lesson;
use \School;
use \Classes;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-12-8 下午2:01
 */
class NoticeController extends BaseController
{

    /**
     * #公告
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function notice()
    {
        $notice = Notice::paginate(20);
        foreach($notice as $n){
            $objClass = Classes::find($n->class_id);
            $n->class = $objClass->name;
            $n->school = School::find($objClass->school_id)->name;
        }
        return View::make('admin.system.notice_index')->with('notices',$notice);
    }

    /**
     * @修改notice
     * @param $notice_id
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function modify($notice_id){
        $notice = Notice::find($notice_id);
        $classes = Classes::all();
        return View::make('admin.system.notice_modify')->with('notice',$notice)
                                                        ->with('classes',$classes);
    }


    /**
     * #公告修改
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function postModify()
    {
        $inputData = Input::only('notice_id','title','content','class');
        $validator = Validator::make($inputData,array(
            'notice_id'=>'required',
            'title'=>'required',
            'content'=>'required',
            'class'=>'required',
        ));

        if($validator->fails()){
            return Redirect::route('adminNotice')->with('error_tips',$validator->messages()->first());
        }
        $notice = Notice::find($inputData['notice_id']);
        $notice->title = $inputData['title'];
        $notice->content  = $inputData['content'];
        $notice->class_id  = $inputData['class'];
        $notice->save();
        return Redirect::route('adminNoticeIndex')->with('success_tips','修改成功');
    }

    /**
     * #删除
     * @param $notice_id
     */
    public function destroy($notice_id)
    {
        Notice::find($notice_id)->delete();
        return Redirect::route('adminNoticeIndex')->with('success_tips','删除成功');
    }
}
