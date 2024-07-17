<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickPackMethod extends Model
{
   
    protected $table = 'pick_pack_method';

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
