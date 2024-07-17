<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierContact extends Model
{
    protected $table = "supplier_contacts";

    protected $fillable = [
        'supplier_id',
        'name',
        'title',
        'email',
        'office_phone',
        'cell_phone',
        'contact_notes',
        'is_primary',
    ];
}
