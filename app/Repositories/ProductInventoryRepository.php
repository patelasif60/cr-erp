<?php

namespace App\Repositories;
use App\Productinventory;
use App\MasterProduct;
use App\AisleMaster;
use App\WareHouse;
use DB;

/**
 * Repository class for model.
 */
class ProductInventoryRepository extends BaseRepository
{
    public function getProductInventorylist($request){
        // $warehouse = WareHouse::all();
        // foreach($warehouse as $key=>$val){
        //     dd($warehouse->masterAisle);
        // }
        // return Productinventory::all();
    }
    public function getChildProductInventorylist($request){
        return MasterProduct::where('is_approve', 1)->where('parent_ETIN',$request->ETIN)->get();
    }
    public function store($data)
    {
        $count = Productinventory::where('ETIN',$data['ETIN'])->whereIn('warehouse_id',$data['warehouses'])->count();
        if($count > 0)
        {
            return false;
        }
        $msterProduct = DB::table('master_product')->where('ETIN',$data['ETIN'])->first();
        foreach($data['warehouses'] as $key=>$val)
        {
            Productinventory::create([
                'ETIN'   => $data['ETIN'],
                'warehouse_id'   => $val,
                'master_product_id' =>$msterProduct->id,
                'each_qty' => ($msterProduct->pack_form_count * $msterProduct->unit_in_pack),
            ]);
        }
        return true;
    }
    public function update($data)
    {
        $count = Productinventory::where('ETIN',$data['ETIN'])->whereIn('warehouse_id',$data['warehouses'])->where('id','!=',$data['id'])->count();
        if($count > 0)
        {
            return false;
        }
        $msterProduct = DB::table('master_product')->where('ETIN',$data['ETIN'])->first();
        $productInventory = Productinventory::find($data['id']); 
        $productInventory->ETIN = $data['ETIN'];
        $productInventory->inventory = $data['inventory'];
        $productInventory->master_product_id = $msterProduct->id;
        $productInventory->each_qty =  $msterProduct->pack_form_count * $msterProduct->unit_in_pack * $data['inventory'] ;
        $productInventory->save();
        return true;
    }
    public function eachQty($warehouse_id){
        return AisleMaster::where('warehouse_id',$warehouse_id)->first();
    }
}