<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierDocumentsLink extends Model
{
    protected $fillable = [
        'supplier_id',
        'url',
        'name',
        'description',
        'date',
    ];
}
