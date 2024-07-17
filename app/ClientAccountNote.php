<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientAccountNote extends Model
{
    protected $fillable = [
        'client_id',
        'event',
        'details',
        'user',
        'date_and_time',
        'added_by'
    ];
}
