<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IceSubChart extends Model
{
    protected $table = 'ice_sub_chart';

     /**
     * Enable timestamps.
     *
     * @var array
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
