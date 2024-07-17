<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientDocument extends Model
{
    protected $fillable = [
        'client_id',
        'type',
        'name',
        'description',
        'date',
        'document',
    ];
}
