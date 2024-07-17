<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterProductKitComponents extends Model
{
    protected $table = 'master_product_kit_components';

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

    public function component_product_details(){
        return $this->belongsTo(MasterProduct::class,'components_ETIN','ETIN');
    }
}
