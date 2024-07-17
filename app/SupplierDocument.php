<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierDocument extends Model
{
    protected $fillable = [
        'supplier_id',
        'type',
        'name',
        'description',
        'date',
        'document',

    ];
}
