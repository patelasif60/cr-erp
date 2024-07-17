<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackagingcomponentsSetting extends Model
{
    protected $table = 'packaging_compontents_setting';
    
    public $timestamps = true;
    
    protected $guarded = ['id'];

    public function PackagingMaterials()
    {
        return $this->belongsTo(\App\PackagingMaterials::class, 'child_packaging_materials_id');
    }
    public function ProductTemperature()
    {
        return $this->belongsTo(\App\ProductTemperature::class, 'product_temperature_id');
    }
}
