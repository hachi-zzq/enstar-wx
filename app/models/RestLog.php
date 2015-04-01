<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RestLog extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'rest_logs';

    protected $dates = ['deleted_at'];
}
