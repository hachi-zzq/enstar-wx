<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Analysis extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'analyses';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

    /**
     * 获取课文原文的语速值
     * @author Hanxiang<hanxiang.qiu@enstar.com>
     * @param $id
     * @return int
     */
    public static function getSpeed($id)
    {
        $analysis = Analysis::find($id);
        if (!$analysis) {
            return Config::get('evaluate.speed.low');
        }

        try {
            $analysisJson = file_get_contents(public_path() . $analysis->path);
        } catch (\Exception $e) {
            return Config::get('evaluate.speed.low');
        }

        try {
            $analysisArray = json_decode($analysisJson, true);
        } catch (\Exception $e) {
            return Config::get('evaluate.speed.low');
        }

        if (isset($analysisArray['speed'])) {
            return round($analysisArray['speed']);
        } else {
            return Config::get('evaluate.speed.low');
        }
    }

}
