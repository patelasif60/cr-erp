<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderAutomaticUpgrades extends Model
{
    protected $table = 'order_automatic_upgrades';

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
    
    public function shippingServiceType(){
        return $this->belongsTo(ShippingServiceType::class,'service_type_id','id');
    }
    public function Client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }
}
