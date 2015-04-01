<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Advisory extends Eloquent {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'advisories';

    /**
     * SoftDeletingTrait
     */
    protected $dates = ['deleted_at'];



}
