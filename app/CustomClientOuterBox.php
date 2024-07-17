<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomClientOuterBox extends Model
{
    protected $table = 'custom_client_outer_boxes';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = [
        'box_id', 'client_id', 'channel_ids', 'product_ids', 'transit_days', 'max_item_count'
    ];

    public function client() {
        return $this->belongsTo(Client::class,'client_id','id');
    }
    
    public function package_material() {
        return $this->belongsTo(PackagingMaterials::class,'box_id','id');
    }
}
