<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientChannelConfiguration extends Model
{
    protected $fillable = [
        'client_id',
        'channel',
        'store_url',
        'admin_url',
        'username',
        'password',
        'is_dne'
    ];
}
