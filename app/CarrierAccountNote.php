<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarrierAccountNote extends Model
{
    protected $fillable = [
        'carrier_id',
        'event',
        'details',
        'user',
        'date_and_time',
        'added_by'
    ];
}
