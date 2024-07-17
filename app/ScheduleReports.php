<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScheduleReports extends Model
{
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
