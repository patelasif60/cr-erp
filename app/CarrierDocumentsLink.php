<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarrierDocumentsLink extends Model
{
    protected $fillable = [
        'carrier_id',
        'url',
        'name',
        'description',
        'date',
    ];
}
