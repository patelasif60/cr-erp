<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $guarded = ['id'];

    protected $table = 'carriers';
}
