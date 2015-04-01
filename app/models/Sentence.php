<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Sentence extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sentences';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];

}
