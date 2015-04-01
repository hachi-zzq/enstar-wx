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
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-21 下午4:42
 */
class UnitController extends BaseController
{

    /**
     * index
     * @author zhuzhengqian
     */
    public function index()
    {
        $objUnit = Unit::orderBy('sort')->paginate(20);
        if(count($objUnit)){
            foreach($objUnit as &$unit){
                $objBook = Book::find($unit->book_id);
                $unit->bookName = $objBook->name;
                $unit->version = $objBook->version;
                $unit->status = $objBook->status == 0 ? '未发布' : "已发布";
            }
        }
        return View::make('admin.unit.index')->with('units',$objUnit);
    }



    /**
     * #添加单元
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function postCreate()
    {
        $inputData = Input::only('book','unit_name');
        $validator = Validator::make($inputData,array(
            'book'=>'required',
            'unit_name'=>'required|min:3|max:20'
        ));
        if($validator->fails()){
            return Response::json(array(
                'msg_code'=>-1,
                'message'=>sprintf("error message: %s",$validator->messages()->first()),
                'data'=>null
            ));
            exit;
        }

        if(Book::find($inputData['book'])->status == 1){
            return Response::json(array(
                'msg_code'=>-2,
                'message'=>'该单元已经发布，禁止修改',
                'data'=>null
            ));
            exit;
        }

        //TODO #校验重名
        $objUnit = new Unit();
        $objUnit->book_id = $inputData['book'];
        $objUnit->name = $inputData['unit_name'];
        $objUnit->unit_unique = Uuid::v4(false);
        $objUnit->status = 0;
        $objUnit->save();
        return  Response::json(array(
            'msg_code'=>0,
            'message'=>'添加成功',
            'data'=>null
        ));
    }

    /**
     * #单元修改
     * @param $unit_id
     *
     */
    public function modify()
    {
        $inputData = Input::only('unit_id','unit_name');
        $validator = Validator::make($inputData,array(
            'unit_id'=>'required',
            'unit_name'=>'required|min:3|max:20'
        ));
        if($validator->fails()){
            return Response::json(array(
                'msg_code'=>-1,
                'message'=>sprintf("error message: %s",$validator->messages()->first()),
                'data'=>null
            ));
            exit;
        }
        //TODO #校验重名
        $objUnit = Unit::find($inputData['unit_id']);
        if($objUnit->status == 1){
            return Response::json(array(
                'msg_code'=>-2,
                'message'=>'该单元已经发布，禁止修改',
                'data'=>null
            ));
            exit;
        }

        $objUnit->name = $inputData['unit_name'];
        $objUnit->save();
        return  Response::json(array(
            'msg_code'=>0,
            'message'=>'修改成功',
            'data'=>null
        ));
    }

    /**
     * #删除单元
     * @param $unit_id
     * @author zhengqian.zhu@enstar.com
     */
    public function destroy($unit_id)
    {
        $obj = Unit::find($unit_id);
        if($obj->status == 1){
            return Redirect::route('adminUnitIndex')->with('error_tips','该单元已经发布，禁止修改');
        }
        if(Lesson::where('unit_id',$unit_id)->get()->count()){
            return Redirect::route('adminUnitIndex')->with('error_tips','该单元下下有课文，禁止删除');
        }
        $obj->delete();
        return Redirect::route('adminUnitIndex')->with('success_tips','删除成功');
    }


    /**
     * @单元排序
     * @param null
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function multiSort()
    {
        $inputData = Input::only('sort');
        foreach($inputData['sort'] as $unit_id=>$sort){
            $obj = Unit::find($unit_id);
            $obj->sort = $sort;
            $obj->save();
        }
        return Redirect::route('adminUnitIndex')->with('success_tips','排序成功');
    }

}

