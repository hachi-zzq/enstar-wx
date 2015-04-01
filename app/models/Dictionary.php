<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Dictionary extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'dictionary';

    protected $dates = ['deleted_at'];

    protected $hidden = array('status', 'deleted_at');
}
