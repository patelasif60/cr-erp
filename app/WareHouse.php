<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WareHouse extends Model
{
    protected $table = 'warehouses';
	    protected $fillable = [
        'warehouses',
    ];
	 public function warehouseList(){
        return $result = WareHouse::select('id','warehouses as warehouseName')->get()->toArray();
        // foreach ($result as $warehouselist){		 
		// 	 $warehouse[] = $warehouselist->warehouses;			 
		//  }
		//  return $warehouse;
    }
    public function iceChartTemplate()
    {
        return $this->belongsToMany(\App\IceChartTemplate::class, 'ice_chart_template_warehouse', 'warehouse_id', 'ice_chart_template_id');
    }
    public function gelPackTemplate()
    {
        return $this->belongsToMany(\App\GelPackTemplate::class, 'gel_pack_template_warehouse', 'warehouse_id', 'gel_pack_template_id');
    }
     public function masterAisle()
    {
        return $this->hasMany(\App\AisleMaster::class,'warehouse_id');   
    }
	
	public function getWareHouseId($wh){
		$whid = $warehouseid = $whArray = $warehouse = null;
		$id = []; 
		if(strpos($wh, ',') !== false){
			$whArray = explode(',', $wh);
			foreach($whArray as $warehouse){
				//echo $warehouse;
				$whid = WareHouse::where('warehouses',$warehouse)->first();
				$id[] = $whid->warehouse_id;				
			}
			
			$warehouseid = implode(", " , $id);
			//dd($warehouseid);
		} else {
			$whid = WareHouse::where('warehouses',$wh)->first();
			$warehouseid = $whid->warehouse_id;
			
		}
		return $warehouseid;
	}
}
