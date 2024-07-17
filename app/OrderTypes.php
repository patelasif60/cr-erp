<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderTypes extends Model
{
    protected $table = 'order_types';

    protected $fillable = ['name'];

    protected $guarded = ['id'];
}
