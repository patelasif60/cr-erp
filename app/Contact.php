<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'client_id',
        'supplier_id',
        'carrier_id',
        'name',
        'title',
        'email',
        'office_phone',
        'cell_phone',
        'contact_note',
        'is_primary',
        'is_contract',
    ];
}
