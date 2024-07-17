<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierAccountNote extends Model
{
    protected $fillable = [
        'supplier_id',
        'event',
        'details',
        'user',
        'date_and_time',
    ];
}
