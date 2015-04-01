<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UserKv extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'user_kv';

    protected $dates = ['deleted_at'];
}