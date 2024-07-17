<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EtailerService extends Model
{
    protected $table = 'etailer_services';

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

    public function upsShippingServiceType(){
        return $this->belongsTo(ShippingServiceType::class,'ups_service_type_id','id');
    }
     public function fdxShippingServiceType(){
        return $this->belongsTo(ShippingServiceType::class,'fedex_service_type_id','id');
    }
}
