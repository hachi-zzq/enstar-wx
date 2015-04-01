<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Feedback extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'feedback';

    protected $dates = ['deleted_at'];
}