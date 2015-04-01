<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Book extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'books';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

    public function units()
    {
        return $this->hasMany('Unit');
    }

    public function lessons()
    {
        return $this->hasMany('Lesson');
    }

    /**
     * #教材下的课文总数
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function getLessonCount()
    {
        return Lesson::where('book_id',$this->id)->count();
    }

    /**
     * #教材的最新版本
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function getLastVersion(){
        return self::where('book_unique',$this->book_unique)->orderBy('created_at','DESC')->take(1)->first()->version;
    }


    /**
     * #获取教材、单元目录树
     * @author zhengqian.zhu@enstar.com
     * @return mixed
     */
    public static  function getBookLessonTree(){
        $books = self::all()->toArray();
        if(count($books)){
            foreach($books as &$v){
                $v['units'] = Unit::where('book_id',$v['id'])->get()->toArray();
                if($v['status'] == 1){
                    $v['disabled'] = 'true';
                    foreach($v['units'] as $u){
                        $u['disabled'] = 'true';
                    }
                }else{
                    $v['disabled'] = 'false';
                    foreach($v['units'] as $u){
                        $u['disabled'] = 'false';
                    }
                }
            }
        }
        return $books;
    }

    /**
     * #重写delete
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function delete(){
        Unit::where('book_id',$this->id)->delete();
        Lesson::where('book_id',$this->id)->delete();
        return parent::delete();
    }

    /**
     * #book下所有的unit
     * @param $book_id
     * @return mixed
     */
    public static  function getUnits($book_id){
        return Unit::where('book_id',$book_id)->get();
    }

    public static function getFirstBook(){
        $obj = self::all();
        return count($obj) ? $obj->first()->id : 0;
    }


    /**
     * #拷贝book,创建新版本
     * @author zhengqian.zhu@enstar.com
     */
    public function copyBookNewVersion(){
        $obj = new Book();
        $obj->name = $this->name;
        $obj->title = $this->title;
        $obj->subtitle = $this->subtitle;
        $obj->description = $this->description;
        $obj->cover = $this->cover;
        $obj->version = sprintf('%.1f',$this->getLastVersion()+1);
        $obj->book_unique = $this->book_unique;
        $obj->publisher = $this->publisher;
        $obj->publish_time = $this->publish_time;
        $obj->status = 0;
        $obj->tag = sprintf("由%s V%s创建的新版本",$this->name,$this->version);
        $obj->save();
        return $obj->id;
    }


}
