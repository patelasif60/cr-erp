<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickerConfigration extends Model
{
    protected $table = 'picker_configrations';

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
