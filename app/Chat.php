<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chat';

    protected $fillable = [
        'id',
        'type_id',
        'type',
        'chat',
        'user_id'
    ];
}
