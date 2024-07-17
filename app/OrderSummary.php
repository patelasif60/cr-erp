<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderSummary extends Model
{
    protected $table = 'order_summary';

    protected $fillable = ['order_status', 'old_status', 'release_date', 'must_ship_today', 'shipment_type'];

    public function client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }

    public function order_type() {
        return $this->belongsTo(OrderTypes::class,'order_type_id','id');
    }    
    public function status() {
        return $this->belongsTo(OrderSummaryStatus::class, 'order_status', 'id');
    }
    public function orderDetail() {
        return $this->hasMany(OrderDetail::class,'order_number','etailer_order_number');
    }
    public function OrderPackage() {
       // return $this->hasManyt(OrderPackage::class, 'etailer_order_number', 'order_id');
        return $this->hasManyThrough(
            OrderPackage::class,
            OrderDetail::class,
            'order_number', // Foreign key on the environments table...
            'order_id', // Foreign key on the deployments table...
            'etailer_order_number', // Local key on the projects table...
            'sub_order_number' // Local key on the environments table...
        );
    }
}
