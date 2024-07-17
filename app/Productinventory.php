<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productinventory extends Model
{
    protected $table = 'product_inventory';

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

    public function werehouse()
    {
        return $this->belongsTo(\App\WareHouse::class, 'warehouse_id','id');
    }
    public function masterProduct(){
        return $this->belongsTo(\App\MasterProduct::class,'master_product_id','id');
    }
}
