<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Warehouse;

class WarehouseInventories extends Model {
    protected $table = 'warehouse_inventories';

    public function warehouseList(){
        return Warehouse::select('id','warehouses as warehouseName')->get(); 
    }

    public function warehouse_name(){
        return $this->belongsTo(Warehouse::class,'warehouses','warehouse');
    }
}