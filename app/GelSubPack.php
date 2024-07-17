<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GelSubPack extends Model
{
     protected $table = 'gel_sub_pack';

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
