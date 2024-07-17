<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'title',
        'email',
        'office_phone',
        'cell_phone',
        'contact_note',
        'is_primary',
    ];
}
