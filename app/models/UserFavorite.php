<?php

class UserFavorite extends Eloquent
{

    protected $table = 'user_favorites';


    public function lesson()
    {
        return $this->belongsTo('Lesson');
    }

    /**
     * 检测是否已经收藏过
     * @param $lessonId
     * @param $userId
     * @return bool
     */
    public static function isExist($lessonId, $userId)
    {
        return self::where('user_id', $userId)->where('lesson_id', $lessonId)->count() > 0;
    }

}