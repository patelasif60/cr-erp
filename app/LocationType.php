<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
     protected $table = 'location_type';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    public $timestamps = true;
    protected $hidden = ['created_at', 'updated_at'];
}
