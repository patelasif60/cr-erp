<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseInventory extends Model
{
    protected $table = 'warehouse_inventories';

    public function warehouse_name(){
        return $this->belongsTo(Warehouse::class,'warehouses','warehouse');
    }
}