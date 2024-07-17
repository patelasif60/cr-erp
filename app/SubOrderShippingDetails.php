<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubOrderShippingDetails extends Model
{

    protected $table = 'sub_order_shipping_details';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = [
        'sub_order_number',
        'ship_to_name', 
        'ship_to_address_type', 
        'ship_to_address1', 
        'ship_to_address2', 
        'ship_to_address3', 
        'ship_to_city', 
        'ship_to_state', 
        'ship_to_zip', 
        'ship_to_country', 
        'ship_to_phone', 
        'shipping_method', 
        'delivery_notes', 
        'customer_shipping_price', 
    ];
}