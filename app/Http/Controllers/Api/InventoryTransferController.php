<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WareHouse;
use App\MasterShelf;
use App\MasterProduct;
use App\ProductVariance;
use App\AisleMaster;
use App\MasterShelfLotAndExpiry;
use App\TransferInventoryDetails;
use Illuminate\Support\Facades\Validator;

class InventoryTransferController extends Controller
{
    public function getAllWarehouses(){
        $warehouses = WareHouse::all();

        if(count($warehouses) > 0){
            $response = ["error"=>false, "data" => $warehouses];
            return response($response, 200);
        }
        else{
            $response = ["error"=>true, 'message' => 'No Warehouse found', "data" => ''];
            return response($response, 200);
        }
        
    }

    public function getAllLocationByWarehouse(Request $request){

        $allAisles = AisleMaster::where('warehouse_id', $request->input('warehouse_id'))->select('id')->get();

        if(count($allAisles) == 0){
            $response = ["error" => true, "message" => 'No location found with selected warehouse'];
			return response($response, 400);
        }

        $address = MasterShelf::whereIN('aisle_id', $allAisles)->select('address')->groupBy('address')->get();
        $addressArray = [];
        foreach($address as $row){
            $addressArray[] = $row->address;
        }

        $response = ["error"=>false, "data" => $addressArray];
        return response($response, 200);

    }

    public function transaferProductQTY(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $validator = Validator::make($request->all(), [
            'current_upc' => 'required',
            'current_warehouse' => 'required',
            'current_location' => 'required',
            'current_qty' => 'required',
            'transfer_warehouse' => 'required',
            'transfer_location' => 'required'
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }
        $upc = $request->current_upc;
        $getProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if(!$getProduct){
            $response = ["error"=>true, 'message' => 'No product found into MPT'];
            return response($response, 400);
        }

        $check_current_location_obj = MasterShelf::leftJoin('master_aisle',function($q){
            $q->on('master_aisle.id','=','master_shelf.aisle_id');
        })->select('master_shelf.*')->where('address', $request->current_location)->where('master_aisle.warehouse_id',$request->current_warehouse)->get();
        if(count($check_current_location_obj) == 0){
            $response = ["error"=>true, 'message' => 'No location found as '.$request->current_location];
            return response($response, 400);
        }

        $check_current_location = $check_current_location_obj->toArray();

        $check_pick_location_obj = MasterShelf::leftJoin('master_aisle',function($q){
            $q->on('master_aisle.id','=','master_shelf.aisle_id');
        })->select('master_shelf.*')->where('address', $request->transfer_location)->where('master_aisle.warehouse_id',$request->transfer_warehouse)->get();
        if(count($check_pick_location_obj) == 0){
            $response = ["error"=>true, 'message' => 'Destination location not found'];
            return response($response, 400);
        }
        $check_destination_location = $check_pick_location_obj->toArray();
        
        
        $current_location_key = array_search($getProduct->ETIN, array_column($check_current_location, 'ETIN'));
        if($current_location_key === false){
            $response = ["error"=>true, 'message' => 'Product does not exist in selected location'];
            return response($response, 400);
        }

        $current_location = $check_current_location[$current_location_key];
        if($request->current_qty > $current_location['cur_qty']){
            $response = ["error"=>true, 'message' => 'There is not enough qty to transfer from location: '.$current_location['address']];
            return response($response, 400);
        }
        
        $destination_location_key = array_search($getProduct->ETIN, array_column($check_destination_location, 'ETIN'));
        if($destination_location_key === false){
            $response = ["error"=>true, 'message' => 'Product does not exist in transafer location'];
            return response($response, 400);
        }

        if(isset($check_destination_location[$destination_location_key])){
            $destination_location = $check_destination_location[$destination_location_key];
            
            if((($destination_location['cur_qty'] + $request->input('current_qty')) > $destination_location['max_qty']) && $destination_location['location_type_id'] == 1){
                $diff = $destination_location['max_qty'] - $destination_location['cur_qty'];
                $response = ["error"=>true, 'message' => 'There is no enough Qty'];
                return response($response, 400);
            }
        }

        $CurrentMSLE = NULL;
        if($request->lot != ''){
            $CurrentMSLE = MasterShelfLotAndExpiry::where('ETIN',$getProduct->ETIN)->where('address',$request->current_location)->where('lot',$request->lot)->first();
            if(!$CurrentMSLE){
                $response = ["error"=>true, 'message' => 'Lot number does not found with the ETIN and address'];
                return response($response, 400);
            }

            if($request->input('current_qty') > $CurrentMSLE->qty){
                $response = ["error"=>true, 'message' => 'Do not have enough qty at selected lot number'];
                return response($response, 400);
            }
        }
        

        $MasterShelf = new MasterShelf();
        $MasterShelf->TransferQty($request,$user_id,$check_destination_location,$destination_location_key,$current_location,$CurrentMSLE);#,$CurrentMSLE
        
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Transfer Invetory',
            'details' => $getProduct->ETIN.' tranfer from ' .$request->current_location.' to '.$request->transfer_location,
            'type' => 'CWMS'
        ]);

        $response = ["error"=>false, 'message' => 'Transfer Completed'];
        return response($response, 200);

    }

    public function markoutProduct(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $validator = Validator::make($request->all(), [
            'scan_upc' => 'required',
            // 'lot' => 'required',
            'scan_address' => 'required',
            'qty' => 'required',
            'markout' => 'required',
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }

        $upc = $request->scan_upc;
        $getProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();

        if(!$getProduct){
            $response = ["error"=>true, 'message' => 'Product not found'];
            return response($response, 400);
        }

        $locationProduct = MasterShelf::where('ETIN', $getProduct->ETIN)->where('address', $request->input('scan_address'))->first();
        if(!$locationProduct){
            $response = ["error"=>true, 'message' => 'Product not found to selected location'];
            return response($response, 400);
        }

        if($locationProduct->cur_qty - $request->input('qty') < 0){
            $response = ["error"=>true, 'message' => 'Product quantity in this location is '.$locationProduct->cur_qty.", unable to markout"];
            return response($response, 400);
        }
        
        if($request->lot != ''){
            $CurrentMSLE = MasterShelfLotAndExpiry::where('ETIN',$getProduct->ETIN)->where('address',$request->scan_address)->where('lot',$request->lot)->first();
            if(!$CurrentMSLE){
                $response = ["error"=>true, 'message' => 'Lot number does not found with the ETIN and address'];
                return response($response, 400);
            }

            if($request->input('qty') > $CurrentMSLE->qty){
                $response = ["error"=>true, 'message' => 'Selected lot # does not have enough qty'];
                return response($response, 400);
            }
        }
        

        $aisle = AisleMaster::where('id', $locationProduct->aisle_id)->first();
        if(!$aisle){
            $response = ["error"=>true, 'message' => 'Ailse Info could not be find'];
            return response($response, 400);
        }
        $warehouse = WareHouse::find($aisle->warehouse_id);

        $productVariance = new ProductVariance();
        $productVariance->ETIN = $getProduct->ETIN;
        $productVariance->warehouse_id = $warehouse->id;
        $productVariance->qty = $request->input('qty');
        $productVariance->reason = $request->input('markout');
        $productVariance->exp_date = $request->input('exp_date');
        $productVariance->action = $request->input('action');
        $productVariance->notes = $request->input('notes');
        $productVariance->address = $request->input('scan_address');
        $productVariance->save();

        $starting_qty = $locationProduct->cur_qty;
        $ending_qty = $starting_qty - $request->input('qty');
        $locationProduct->cur_qty = $ending_qty;
        $locationProduct->save();
        if($request->lot != ''){
            $CurrentMSLE->qty = $CurrentMSLE->qty - $request->qty;
            $CurrentMSLE->save();
        }

        InventoryAdjustmentLog([
            'ETIN' => $getProduct->ETIN,
            'location' => $request->scan_address,
            'starting_qty' => $starting_qty,
            'ending_qty' => $ending_qty,
            'total_change' => '-'.$request->qty,
            'user' => $user_id,
            'reference' => 'Markout',
            'reference_value' => json_encode($request->all()),
            'reference_description' => 'ETIN: '.$getProduct->ETIN.' has been markedout'
        ]);

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Markout',
            'details' => $getProduct->ETIN.' Has been Marked out',
            'type' => 'CWMS'
        ]);

        return response(["error" => false, 'message' => 'Product markout successfully!'], 200);

    }
}
