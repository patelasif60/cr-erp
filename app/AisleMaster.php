<?php

namespace App;
use App\ProductTemperature;
use App\WareHouse;

use Illuminate\Database\Eloquent\Model;

class AisleMaster extends Model
{
    protected $table = 'master_aisle';

    protected $fillable = [ 'warehouse_id','aisle_name', 'product_temp_id' ];

    public function storage_type(){
        return $this->belongsTo(ProcessingGroups::class, 'product_temp_id', 'id');
    }

    public function warehouse_name(){
        return $this->belongsTo(WareHouse::class,'warehouse_id','id');
    }
    public function shelfFromAisle(){
        return $this->hasMany(\App\MasterShelf::class, 'aisle_id');
    }
}
