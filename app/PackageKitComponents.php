<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageKitComponents extends Model
{
    protected $table = 'package_kit_components';

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
