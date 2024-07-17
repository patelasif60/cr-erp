<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarrierDocument extends Model
{
    protected $fillable = [
        'carrier_id',
        'type',
        'name',
        'description',
        'date',
        'document',
    ];
}
