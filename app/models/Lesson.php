<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Lesson extends Eloquent
{

    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lessons';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

    public function book()
    {
        return $this->belongsTo('Book', 'book_id');
    }

    public function sentences()
    {
        return $this->hasMany('Sentence');
    }

    public function analyses()
    {
        return $this->hasMany('Analysis');
    }

    /**
     * #重写delete
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public function delete()
    {
        Analysis::where('lesson_id', $this->id)->delete();
        Sentence::where('lesson_id', $this->id)->delete();
        return parent::delete();
    }

    /**
     * 获取课文标题
     * @param $id
     * @return mixed
     * @author Jun<jun.zhu@enstar.com>
     */
    public static function getLessonTitle($id)
    {
        $obj = Lesson::find($id);
        return $obj ? $obj->title : '';
    }

    /**
     * #lesson最新版本
     * @param $lesson_unique
     * @return mixed
     * @author zhengqian.zhu@enstar.com
     */
    public static function getLastVersion($lesson_unique)
    {
        return self::where('lesson_unique', $lesson_unique)->orderBy('created_at', 'DESC')->take(1)->first()->version;
    }


    /**
     * 根据guid查找课文
     * @param $guid
     * @return mixed
     */
    public static function getByGuid($guid)
    {
        return Lesson::where('guid', $guid)->first();
    }

    /**
     * 根据标题搜索课文
     * @param $kw
     * @return mixed
     */
    public static function search($kw, $num = 0,$language='en-gb')
    {
        $query = Lesson::where('title', 'like', '%' . $kw . '%');
        if ($language) {
            $query = $query->where('language', $language);
        }
        if ($num > 0) {
            $query = $query->take($num);
        }
        return $query->get();
    }
}
