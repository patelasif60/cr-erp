<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPackage extends Model
{
    protected $table = 'order_packages';

    protected $fillable = [
        'etailer_weight',
        'shipping_response',
        'tracking_number',
        'label_image',
        'shipping_label_creation_time',
        'dry_ice_pallet_Lb',
        'dry_ice_block_Lb'
    ];

    public function packaging_material(){
        return $this->belongsTo(PackagingMaterials::class,'box_used','id');
    }
    
    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
}
