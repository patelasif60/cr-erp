<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProcessingGroups extends Model
{
   
    protected $table = 'processing_groups';

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
