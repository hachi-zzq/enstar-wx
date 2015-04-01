<?php
namespace Enstar\Controller\Admin;

use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use \Student;
use \Lesson;
use \Reading;
use \Advisory;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-21 下午4:42
 */
class AdvisoryController extends BaseController
{

    /**
     * index
     * @author zhuzhengqian
     */
    public function index()
    {
        $advisory = Advisory::orderBy('created_at','DESC')->paginate(20);
        return View::make('admin.advisory.index')->with('advisory',$advisory);
    }





}

