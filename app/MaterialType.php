<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    protected $table = 'material_type';
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

    public function packagingMaterials()
    {
        return $this->hasMany(\App\PackagingMaterials::class, 'material_type_id');
    }
}
