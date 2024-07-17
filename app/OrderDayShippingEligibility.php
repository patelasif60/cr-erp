<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDayShippingEligibility extends Model
{
    protected $table = 'order_day_shipping_eligibilities';

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
