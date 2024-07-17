<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialWarehouseTdCount extends Model
{
    protected $table = 'material_warehouse_td_count';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = [
        'material_id', 'wh_id', 'transit_days', 'count'
    ];
    
    public function material() {
        return $this->belongsTo(PackagingMaterials::class, 'material_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo(WareHouse::class, 'wh_id', 'id');
    }
}
