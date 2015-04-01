<?php

namespace Enstar\Controller\Admin;

use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use \Unit;
use \Lesson;
use J20\Uuid\Uuid;
use \Analysis;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-12-3 下午3:20
 */

class ReportController extends BaseController{


    /**
     * #报告
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function index()
    {
        $reports = Analysis::paginate(20);
        foreach($reports as $k=>$r){
            if( ! Lesson::find($r->lesson_id)){
                unset($reports[$k]);
            }
        }
        return View::make('admin.report.index')->with('reports',$reports);
    }


}