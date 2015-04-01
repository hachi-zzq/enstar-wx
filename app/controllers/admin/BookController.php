<?php
namespace Enstar\Controller\Admin;

use \View;
use \Input;
use \Validator;
use \Response;
use \Request;
use \Redirect;
use \Book;
use J20\Uuid\Uuid;
use \Unit;
use Illuminate\Support\Facades\Redis;
use \Lesson;
use \Sentence;
/**
 * author zhengqian.zhu <zhengqian.zhu@enstar.com>
 * DateTime: 14-11-19 下午3:27
 */
class BookController extends BaseController
{

    /**
     * book index page
     * @author zhengqian.zhu@enstar.com
     * @return null
     */
    public function index()
    {
//        header('content-type:text/html;charset=utf8');
//        $obj = \DB::table('books')
//            ->select(\DB::raw('max(id) as id,name,title,subtitle,cover,description,version'))
//            ->groupBy('book_unique')
//            ->get();
//        print_r($obj);exit;
//        if(Input::has('book_name')){
//            $inputName = Input::get('book_name');
//            $obj = $obj->where('name','like',"%$inputName%");
//        }
//
//        if(Input::has('new')){
//            $obj = $obj->group('book_unique');
//        }
//
//        $obj = $obj->get();
//        print_r($obj);exit;

        if(Input::has('book_name')){
            $inputName = Input::get('book_name');
            $bookList = Book::where('name','like',"%$inputName%")->orderBy('sort')->paginate(20);
        }else{
            $inputName = '';
            $bookList = Book::orderBy('sort')->paginate(20);
        }
        return View::make('admin.book.index')->with('bookList',$bookList)
                                              ->with('input_name',$inputName);
    }


    /**
     * #创建教材
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function create()
    {
        return View::make('admin.book.create');
    }

    /**
     * #创建教材psot
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public function postCreate()
    {
        $inputData = Input::all();
        $validator = Validator::make($inputData,array(
            'book_name'=>'required',
            'book_title'=>'required',
            'book_subtitle'=>'',
            'description'=>'',
            'publisher'=>'',
            'publish_time'=>'',
            'cover'=>'required'
        ));
        if($validator->fails()){
            return Redirect::route('adminBookCreate')->with('error_tips',$validator->messages()->first())
                                                      ->with('flash_session',Input::all());
        }
        if(Input::hasFile('cover') and Input::file('cover')->isValid()){
            $file = Input::file('cover');
            if( ! in_array($file->getClientOriginalExtension(),array('jpg','jpeg','gif','png'))){
                return Redirect::route('adminBookCreate')->with('error_tips','文件格式不正确')
                    ->with('flash_session',Input::all());
            }
            $destinationFile = sprintf('%s.%s',Uuid::v4(false),$file->getClientOriginalExtension());
            $destinationPath = public_path().'/upload/cover/'.date('Ymd');
            !is_dir($destinationPath) and mkdir($destinationPath,0777,true);
            $file->move(public_path().'/upload/cover/'.date('Ymd'),$destinationFile);
        }

        $objBook = new Book();
        $objBook->name = $inputData['book_name'];
        $objBook->title = $inputData['book_title'];
        $objBook->subtitle = $inputData['book_subtitle'];
        $objBook->description = $inputData['description'];
        $objBook->version = '1.0';
        $objBook->cover = sprintf('%s/%s','/upload/cover/'.date('Ymd'),$destinationFile);
        $objBook->book_unique = Uuid::v4(false);
        $objBook->publisher = $inputData['publisher'];
        $objBook->publish_time = $inputData['publish_time'];
        $objBook->tag = '新创建的教材';
        $objBook->status = 0;
        $objBook->save();

        return Redirect::route('adminBookIndex')->with('success_tips','新教材创建成功，版本号为'.$objBook->version);
    }

    /**
     * #删除教材，如果发布了，不可以删除
     * @param $book_id
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function destroy($book_id)
    {
        $obj = Book::find($book_id);
        if($obj->status == 1){
            return Redirect::route('adminBookIndex')->with('error_tips','所在教材已经发布，不可以删除，如果您有改动，可以创建新版本在进行修改');
        }
        $obj->delete();
        return Redirect::route('adminBookIndex')->with('success_tips','删除成功');
    }


    /**
     * #发布教材
     * @param $book_id
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function publish($book_id)
    {
        $obj = Book::find($book_id);
        !$obj and \App::abort(404);
        if($obj->status == 1){
            //已经发布
            return Redirect::route('adminBookIndex')->with('error_tips',sprintf("%s 已经发布了，不能再发布",$obj->name));
        }

        if( ! count($lessons = Lesson::where('book_id',$book_id)->get())){
            return Redirect::route('adminBookIndex')->with('error_tips',sprintf("%s 下面没有课文，请先添加课文",$obj->name));
        }

        //检查旗下所有的lesson是否已经匹配成功，如果有失败的不允许发布
        foreach($lessons as $l){
            if($l->status !==2){
                return Redirect::route('adminBookIndex')->with('error_tips',sprintf("%s 匹配失败，请先确保该教材下所有的课文匹配成功",$l->title));
            }
        }

        $obj->status = 1;
        $obj->save();

        //更新教材下所有单元的status
        Unit::where('book_id', $book_id)->update(array('status' => 1));


        return Redirect::route('adminBookIndex')->with('success_tips',sprintf("%s 发布成功",$obj->name));
    }

    /**
     * @创建新版本
     * @param $bookId
     * @author zhengqian.zhu@enstar.com
     * @return null
     */
    public function createNewVersion($bookId)
    {
        $objBook = Book::find($bookId);
        //拷贝book
        $newBookId = $objBook->copyBookNewVersion();
        //拷贝unit
        $units = Unit::where('book_id',$bookId)->get();
        //有单元
        if($units){
            $arrUnit = array();//old unit new unit对应
            foreach($units as $u){
                $objUnit = new Unit();
                $objUnit->name = $u->name;
                $objUnit->book_id = $newBookId;
                $objUnit->unit_unique = $u->unit_unique;
                $objUnit->status = 0;
                $objUnit->save();
                $arrUnit[$u->id] = $objUnit->id;
            }
        }


        $lessons = Lesson::where('book_id',$bookId)->get();
        if(count($lessons)){
            foreach($lessons as $l){
                //拷贝lesson
                $objLesson = new Lesson();
                $objLesson->title = $l->title;
                $objLesson->guid = Uuid::v4(false);
                $objLesson->raw_content = $l->raw_content;
                $objLesson->asr_content = $l->asr_content;
                $objLesson->audio = $l->audio;
                $objLesson->book_id = $newBookId;
                $objLesson->unit_id = null;
                if($units && $arrUnit){
                    $objLesson->unit_id = $arrUnit[$l->unit_id];
                }
                $objLesson->sort = $l->sort;
                $objLesson->version = sprintf('%.1f',$l->version+1);
                $objLesson->tag = $l->tag;
                $objLesson->lesson_unique  = $l->lesson_unique;
                $objLesson->status = 0;
                $objLesson->save();
                //拷贝句子
                $sentences = Sentence::where('lesson_id',$l->id)->get();
                if(count($sentences)){
                    foreach($sentences as $sent){
                        $objSentence = new Sentence();
                        $objSentence->lesson_id = $objLesson->id;
                        $objSentence->raw_sentence = $sent->raw_sentence;
                        $objSentence->asr_sentence = $sent->asr_sentence;
                        $objSentence->sort = $sent->sort;
                        $objSentence->prefix = $sent->prefix;
                        $objSentence->format = $sent->format;
                        $objSentence->type = $sent->type;
                        $objSentence->save();
                    }
                }
            }
        }

        return Redirect::route('adminBookIndex')->with('success_tips',sprintf("%s创建新版本成功",$objLesson->name));

    }


    /**
     * @课文排序
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function sort()
    {
        $inputData = Input::only('sort');
        //TODO Validator
        foreach($inputData['sort'] as $bookId=>$sort){
            $obj = Book::find($bookId);
            $obj->sort = $sort;
            $obj->save();
        }
        return Redirect::route('adminBookIndex')->with('success_tips','排序成功！');
    }


}

