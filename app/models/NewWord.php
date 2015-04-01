<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class NewWord extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'new_words';

    protected $dates = ['deleted_at'];
}