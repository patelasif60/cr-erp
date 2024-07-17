<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingWarehouseEligibilities extends Model
{
    protected $table = 'shipping_warehouse_eligibilities';

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
