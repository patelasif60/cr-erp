<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FedexAuthToken extends Model
{
    protected $table = 'fedex_auth_token';
    protected $fillable = ['token', 'expiry_date_time', 'created_at', 'updated_at'];
}