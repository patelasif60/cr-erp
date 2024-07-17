<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientEvent extends Model
{
    protected $fillable = [
        'client_id',
        'event',
        'frequency',
        'day_and_time',
        'details',
        'owner',
    ];
}
