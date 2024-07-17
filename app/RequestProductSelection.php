<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestProductSelection extends Model
{
    protected $table = 'request_product_selection';
    protected $fillable = [
        'request_type',
        'request_field'
    ];
}
