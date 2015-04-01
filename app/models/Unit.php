<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Unit extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'units';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

    public function lessons()
    {
        return $this->hasMany('Lesson');
    }

    /**
     * #unit下所有的lesson
     * @param $unit_id
     * @return mixed
     */
    public static function getLessons($unit_id){
        return Lesson::where('unit_id',$unit_id)->orderBy('sort')->get();
    }


    /**
     * #重写delete
     * @return mixed
     */
    public function delete(){
        Lesson::where('unit_id',$this->unit_id)->delete();
        return parent::delete();
    }



}
