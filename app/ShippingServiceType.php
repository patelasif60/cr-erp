<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingServiceType extends Model
{
    protected $table = 'shipping_service_types';

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
