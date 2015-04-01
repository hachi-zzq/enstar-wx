<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Reading extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'reading';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

    public function advisories()
    {
        return $this->hasMany('Advisory');
    }

    public function lesson()
    {
        return $this->belongsTo('Lesson', 'lesson_id');
    }

    public function user()
    {
        return $this->belongsTo('User', 'user_id');
    }

    public static $status = array(
        0 => 'TOANALYZE',
        10 => 'ANALYZING',
        100 => 'SUCCESS',
        -1 => 'FAIL'
    );

}
