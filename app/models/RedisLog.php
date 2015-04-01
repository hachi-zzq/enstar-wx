<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RedisLog extends Eloquent
{
    use SoftDeletingTrait;

    protected $table = 'redis_logs';

    protected $dates = ['deleted_at'];
}
