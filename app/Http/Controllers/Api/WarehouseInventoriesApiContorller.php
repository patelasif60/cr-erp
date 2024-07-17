<?php

namespace App\Http\Controllers\Api;

use App\AisleMaster;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\WarehouseInventories;
use App\MasterShelf;
use App\BayMaster;
use App\MasterProduct;
use App\BackStockPallet;
use App\BackStockPalletItem;

class WarehouseInventoriesApiContorller extends Controller
{
	/*
		Method 		: getAllInventories
		Description : Use for get all Inventories item
	*/
    public function getAllInventories() {
    	$result = WarehouseInventories::get();
    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400];
			return response($response, 400);
    	}
    }

    /*
		Method 		: getAllInventories
		Description : Use for get all Inventories item
	*/
    public function getProductsByBayId(Request $request) {
    	$validator = Validator::make($request->all(), [
            'master_bay_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $result = WarehouseInventories::select('id','warehouse','zone', 'sku', 'quantity')->where('master_bay_id', $request->master_bay_id)->with('warehouse_name')->get();
        if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400];
			return response($response, 400);
    	}
    }

    
	public function addProduct(Request $request){
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        
		$validator = Validator::make($request->all(), [
	    		'aisle_id' => 'required',
	            'bay_id' => 'required',
	            'shelf' => 'required',
	            'slot' => 'required',
	            'ETIN' => 'required',
	            // 'max_qty' => 'required',

				// 'cur_qty' => 'required',

				'parent_id' => 'required',
        	]);

        if ($validator->fails()) {
           	return response(["error" => true, 'message'=>$validator->errors()->all()], 422);
        }
		if($request->location_type_id !== 2 && $request->location_type_id !== 3 && $request->location_type_id !== 4){
			if($request->cur_qty > $request->max_qty ){
				return response(["error" => true, 'message'=>'Current quantity is greter than max quantity'], 400);
			}
		}
        
        $count=0;
        $location = '';
        $bayNumber = BayMaster::find($request->bay_id);
        $shelf = MasterShelf::where('aisle_id', $request->aisle_id)
                ->where('bay_id', $request->bay_id)
                ->where('shelf', $request->shelf)
				->where('slot', $request->slot)
				->where('parent_id', $request->parent_id)
				->get();

		$address = "";
		
		$cur_qty = isset($request->cur_qty) ? $request->cur_qty : 0;
		if(count($shelf)>0){
			$ETINArray = $shelf->pluck('ETIN')->toArray(); 
			if (in_array($request->ETIN,$ETINArray)) {
				return response(["error" => true, 'message' => 'Product already present in the location!'], 406);
			}
		}
		if(count($shelf)>0){
			$ETINArray = $shelf->pluck('ETIN')->toArray(); 
			if (in_array($request->ETIN,$ETINArray)) {
				return response(["error" => true, 'message' => 'Product already present in the location!'], 406);
			}
			//dd($shelf);
			foreach($shelf as $key=>$val)
			{
				$location = $val->location_type_id;
				$address = $val->address;
				if($val->ETIN == null){
					
					InventoryAdjustmentLog([
						'ETIN' => $val->ETIN,
						'location' => $val->address,
						'starting_qty' => $val->cur_qty,
						'ending_qty' => ($cur_qty),
						'total_change' => $cur_qty,
						'user' => $user_id,
						'reference' => 'Warehouse Inventory',
						'reference_value' => json_encode($request->all()),
						'reference_description' => 'Updating Qty: addProduct'
					]);
					

					$val->ETIN = $request->ETIN;
					$val->max_qty = $request->max_qty;
					$val->cur_qty = $cur_qty;
					$val->save();

					
					$count++;
					break;
				}
			}
			if($count == 0)
			{
				$shelf = MasterShelf::create([
		            'aisle_id'    => $request->aisle_id,
		            'bay_id' => $request->bay_id,
		            'shelf' => $request->shelf,
		            'slot'  => $request->slot,
		            'ETIN'  => $request->ETIN,
		            'max_qty' => $request->max_qty,
		            'cur_qty' => $cur_qty,
		            'address' => $address,
            		'location_type_id' => $location,
					'parent_id' => $request->parent_id
            	]);
				$address = $shelf->address;
				InventoryAdjustmentLog([
					'ETIN' => $shelf->ETIN,
					'location' => $shelf->address,
					'starting_qty' => 0,
					'ending_qty' => ($cur_qty),
					'total_change' => $cur_qty,
					'user' => $user_id,
					'reference' => 'Warehouse Inventory',
					'reference_value' => json_encode($request->all()),
					'reference_description' => 'Updating Qty: addProduct'
				]);
			}

			
			$mpt = MasterProduct::where('ETIN', $request->ETIN)->where(function($q){
				$q->whereNull('parent_ETIN');
				$q->orwhere('parent_ETIN','');
			})->first();
			$childProducts = MasterProduct::where('parent_ETIN', 'like', '%'.$request->ETIN.'%')->where('is_approve', 1)->get();
			
			if (isset($childProducts) && count($childProducts) > 0) {

				$mp_units_in_pack = (isset($mpt->unit_in_pack) ? $mpt->unit_in_pack : 0);
				foreach ($childProducts as $cp) {
					
					$multiplication_factor = isset($mp_units_in_pack) && isset($cp->unit_in_pack) && $mp_units_in_pack > 0 && $cp->unit_in_pack > 0
								? floor($mp_units_in_pack / $cp->unit_in_pack)
								: 0;
					
					$ch_m = MasterShelf::create([
						'aisle_id'    => $request->aisle_id,
						'bay_id' => $request->bay_id,
						'shelf' => $request->shelf,
						'slot'  => $request->slot,
						'ETIN'  => $cp->ETIN,
						'max_qty' => $multiplication_factor > 0 && $request->max_qty > 0 ? $request->max_qty * $multiplication_factor : 0,
						'cur_qty' => $multiplication_factor > 0 ? $multiplication_factor * $cur_qty : 0,
						'address' => $address,
						'location_type_id' => $location,
						'parent_id' => $request->parent_id
					]);

					InventoryAdjustmentLog([
						'ETIN' => $ch_m->ETIN,
						'location' => $ch_m->address,
						'starting_qty' => 0,
						'ending_qty' => ($ch_m->cur_qty),
						'total_change' => $ch_m->cur_qty,
						'user' => $user_id,
						'reference' => 'Warehouse Inventory',
						'reference_value' => json_encode($request->all()),
						'reference_description' => 'Updating Qty: addProduct'
					]);
				}
			}

			return response(["error" => false, 'message' => 'Product added successfully!'], 200);
		}
		else{
			return response(["error" => true, 'message' => 'Shelf not found!'], 406);
		}
	}
	public function editProduct(Request $request,$id) 
	{
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);
		// if($request->type == 'bin' || $request->type == 'pallte')
		// {
		// 	$validator = Validator::make($request->all(), [
	 //    		'aisle_id' => 'required',
	 //            'bay_id' => 'required',
	 //            'ETIN' => 'required',
	 //            'max_qty' => 'required',
		// 		'cur_qty' => 'required',
	 //    	]);
		// }
		// else
		// {
			$validator = Validator::make($request->all(), [
	    		'aisle_id' => 'required',
	            'bay_id' => 'required',
	            'ETIN' => 'required',
	            'max_qty' => 'required',
				'cur_qty' => 'required',
	    	]);
		// }

		if ($validator->fails()) {
	    	return response(["error" => true, 'message'=>$validator->errors()->all()], 422);
		}
		if($request->cur_qty > $request->max_qty ){
    		return response(["error" => true, 'message'=>'Current quantity is greter than max quantity'], 400);
    	}
		$shelf = MasterShelf::find($id);
		$bayNumber = BayMaster::find($request->bay_id);
		if($shelf)
		{
			$aisle = AisleMaster::where('id', $request->aisle_id)->first();
			if (!$aisle) {
				return response(["error" => true, 'message'=>'Not found Aisle with Id: ' . $request->aisle_id], 404);
			}
			
			$product = MasterProduct::where('ETIN',$request->ETIN)->first();
			$shelfValidate = MasterShelf::where('aisle_id', $request->aisle_id)
                ->where('bay_id', $request->bay_id)
                ->where('shelf', $request->shelf)
				->where('slot', $request->slot)
				->first();
			
			if ($shelfValidate->id != $id && $shelfValidate->ETIN && $shelfValidate->ETIN != $request->ETIN) {
				return response(["error" => true, 'message' => 'Product already present in the location!'], 406);
			}

			if($product){
				/*$shelf->aisle_id = $request->aisle_id;
	            $shelf->bay_id = $request->bay_id;
	            $shelf->location_type_id = $request->location_type_id;
	            if($request->type == 'bin' || $request->type == 'pallte')
				{	
					$shelf->address = $request->aisle_name.':'.$bayNumber->bay_number.':1'.':1';	
				}
				else{
					$shelf->address = $aisle->aisle_name.':'.$bayNumber->bay_number.':'.$shelf->shelf.':'.$shelf->slot;	 
				}*/
				$shelfValidate_starting_qty = $shelfValidate->cur_qty;
				$shelf_starting_qty = $shelf->cur_qty;
				if ($shelfValidate->id != $id) {
					if ($shelfValidate->ETIN == $request->ETIN) {
						$shelfValidate->cur_qty = $shelfValidate->cur_qty + $request->cur_qty;
						if ($shelfValidate->cur_qty > $shelfValidate->max_qty ) {
							return response(["error" => true, 'message' => 'Current quantity cannot be more than max quantity!'], 400);
						}
					} else {
						$shelfValidate->ETIN = $request->ETIN;
						$shelfValidate->max_qty = $request->max_qty;
						if ($request->cur_qty > $shelfValidate->max_qty ) {
							return response(["error" => true, 'message' => 'Current quantity cannot be more than max quantity!'], 400);
						}
						$shelfValidate->cur_qty = $request->cur_qty;
					}
					$shelf->ETIN = null;
					$shelf->max_qty = null;
					$shelf->cur_qty = null;
				} else {

					if ($request->cur_qty > $shelf->max_qty ) {
						return response(["error" => true, 'message' => 'Current quantity cannot be more than max quantity!'], 400);
					}

					$shelf->max_qty = $request->max_qty;
					$shelf->cur_qty = $request->cur_qty;
				}

				InventoryAdjustmentLog([
                    'ETIN' => $shelfValidate->ETIN,
                    'location' => $shelfValidate->address,
                    'starting_qty' => $shelfValidate_starting_qty,
                    'ending_qty' => $shelfValidate->cur_qty,
                    'total_change' => ($shelfValidate_starting_qty - $shelfValidate->cur_qty),
                    'user' => $user_id,
                    'reference' => 'Warehouse Inventory',
                    'reference_value' => json_encode($request->all()),
                    'reference_description' => 'Updating Qty: editProduct'
                ]);

				InventoryAdjustmentLog([
                    'ETIN' => $shelf->ETIN,
                    'location' => $shelf->address,
                    'starting_qty' => $shelf_starting_qty,
                    'ending_qty' => $shelf->cur_qty,
                    'total_change' => ($shelf_starting_qty - $shelf->cur_qty),
                    'user' => $user_id,
                    'reference' => 'Warehouse Inventory',
                    'reference_value' => json_encode($request->all()),
                    'reference_description' => 'Updating Qty: editProduct'
                ]);
				
				$shelfValidate->save();
				$shelf->save();

				return response(["error" => false, 'message' => 'Shelf updated successfully!'], 200);
			}
			else
			{
				return response(["error" => true, 'message' => 'Product not found!'], 406);
			}	
		}
		else
		{
			return response(["error" => true, 'message' => 'Shelf not found!'], 406);
		}	
	}
	public function editProductLocation (Request $request) {
		$validator = Validator::make($request->all(), [
	    		'aisle_id' => 'required',
	            'bay_id' => 'required',
	            'shelf' => 'required',
	            'slot' => 'required',
	            'location_type_id' => 'required',
	    ]);
	    if ($validator->fails()) {
	    	return response(["error" => true, 'message'=>$validator->errors()->all()], 422);
		}
		$shelfValidate = MasterShelf::where('aisle_id', $request->aisle_id)
                ->where('bay_id', $request->bay_id)
                ->where('shelf', $request->shelf)
				->where('slot', $request->slot)
				->update(['location_type_id' => $request->location_type_id]);
		return response(["error" => false, 'message' => 'Location updated successfully!'], 200);
	}
	public function editProductQuntity(Request $request,$id){
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);
		$validator = Validator::make($request->all(), [
    		'ETIN' => 'required',
            'max_qty' => 'required',
			'cur_qty' => 'required',
	    ]);
	    if ($validator->fails()) {
	    	return response(["error" => true, 'message'=>$validator->errors()->all()], 422);
		}
		if($request->cur_qty > $request->max_qty ){
    		return response(["error" => true, 'message'=>'Current quantity is greter than max quantity'], 400);
    	}

		
		$shelf = MasterShelf::find($id);
		InventoryAdjustmentLog([
			'ETIN' => $shelf->ETIN,
			'location' => $shelf->address,
			'starting_qty' => $shelf->cur_qty,
			'ending_qty' => $request->cur_qty,
			'total_change' => ($shelf->cur_qty - $request->cur_qty),
			'user' => $user_id,
			'reference' => 'Warehouse Inventory',
			'reference_value' => json_encode($request->all()),
			'reference_description' => 'Updating Qty: editProductQuntity'
		]);
		

		$shelf->ETIN = $request->ETIN;
		$shelf->max_qty = $request->max_qty;
		$shelf->cur_qty = $request->cur_qty;
		$shelf->save();
		return response(["error" => false, 'message' => 'Updated successfully!'], 200);
	}

	public function TransferProduct(Request $request){
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);
		$validator = Validator::make($request->all(), [
				'qty' => 'required',
				'location' => 'required'
			]);

		if ($validator->fails()) {
			return response(["error" => true, 'message'=>$validator->errors()->all()], 422);
		}
		
		$ShelfData = $request->ShelfData;
		$pallet_number = $ShelfData['pallet_number'];
		$warehouse = NULL;
		if(isset($ShelfData['bay']['aisle_name']['warehouse_id'])){
			$warehouse = $ShelfData['bay']['aisle_name']['warehouse_id'];
		}

		if($warehouse == ''){
			return response(["error" => true, 'message' => 'Something is wrong with the current location warehouse'], 400);
		}
		$location = $request->location;
		$qty = $request->qty;
		$new_location = MasterShelf::where('address',$location)->first();
		// dd($new_location);
		if(!$new_location){
			return response(["error" => true, 'message' => $new_location. ' Does not exist'], 400);
		}

		$new_location_warehouse = NULL;
		if(isset($new_location->ailse->warehouse_id)){
			$new_location_warehouse = $new_location->ailse->warehouse_id;
		}

		if($new_location_warehouse == ''){
			return response(["error" => true, 'message' => 'Something is wrong with the new location warehouse'], 400);
		}

		if($new_location_warehouse !== $warehouse){
			return response(["error" => true, 'message' => 'Warehouse has to be same for both the locations'], 400);
		}

		if($qty > $ShelfData['cur_qty']){
			return response(["error" => true, 'message' => 'Qty can not be greated than current qty'], 400);
		}

		$check_location_with_product = MasterShelf::where('address',$location)->where('ETIN',$ShelfData['ETIN'])->first();
		if($check_location_with_product){
			$max_qty = $check_location_with_product->max_qty;
			$new_qty = $qty + $check_location_with_product->cur_qty;
			$qty_required = $max_qty - $check_location_with_product->cur_qty;
			if($check_location_with_product->location_type_id == 1){
				if(($max_qty != '') &&  (($new_qty) > ($max_qty)) && $check_location_with_product->location){
					return response(["error" => true, 'message' => 'Max Qty Violation Error for the location '.$location. ' required qty is '.$qty_required], 400);
				}

				if($max_qty == ''){
					if($qty > 100){
						$max_qty = $qty;
					}else{
						$max_qty = 100;
					}
				}
				
			}

			InventoryAdjustmentLog([
				'ETIN' => $check_location_with_product->ETIN,
				'location' => $check_location_with_product->address,
				'starting_qty' => $check_location_with_product->cur_qty,
				'ending_qty' => $new_qty,
				'total_change' => ($check_location_with_product->cur_qty - $new_qty),
				'user' => $user_id,
				'reference' => 'Warehouse Inventory',
				'reference_value' => json_encode($request->all()),
				'reference_description' => 'Updating Qty: TransferProduct'
			]);

			$check_location_with_product->max_qty = $max_qty;
			$check_location_with_product->cur_qty = $new_qty;
			$check_location_with_product->save();

			$old_location = MasterShelf::find($ShelfData['id']);
			InventoryAdjustmentLog([
				'ETIN' => $old_location->ETIN,
				'location' => $old_location->address,
				'starting_qty' => $old_location->cur_qty,
				'ending_qty' => ($old_location->cur_qty - $qty),
				'total_change' => ($old_location->cur_qty - ($old_location->cur_qty - $qty)),
				'user' => $user_id,
				'reference' => 'Warehouse Inventory',
				'reference_value' => json_encode($request->all()),
				'reference_description' => 'Updating Qty: TransferProduct'
			]);

			$old_location->cur_qty = ($old_location->cur_qty - $qty);
			$old_location->save();

			if($pallet_number != ''){
				$BackStockPallet = BackStockPallet::where('pallet_number',$pallet_number)->first();
				if($BackStockPallet){
					$BackStockPalletItem = BackStockPalletItem::where('backstock_pallet_id',$BackStockPallet->id)->where('ETIN',$ShelfData['ETIN'])->first();
					if(($BackStockPalletItem->quantity - $qty) >= 0){
						$BackStockPalletItem->quantity = $BackStockPalletItem->quantity - $qty;
						$BackStockPalletItem->save();
					}
				}

				
				UserLogs([
					'user_id' => $user_id,
					'action' => 'Scan',
					'task' => 'Backstock',
					'details' => $ShelfData['ETIN'].' from pallet #' .$pallet_number.' has been transfered',
					'type' => 'CWMS'
				]);
			}

			if($old_location->cur_qty == 0){
				$old_location->delete();
			}
		}else{
			if($new_location->ETIN == ''){
				$max_qty = $new_location->max_qty;
				$new_location->ETIN = $ShelfData['ETIN'];
				InventoryAdjustmentLog([
					'ETIN' => $new_location->ETIN,
					'location' => $new_location->address,
					'starting_qty' => $new_location->cur_qty,
					'ending_qty' => ($qty),
					'total_change' => ($new_location->cur_qty - ($qty)),
					'user' => $user_id,
					'reference' => 'Warehouse Inventory',
					'reference_value' => json_encode($request->all()),
					'reference_description' => 'Updating Qty: TransferProduct'
				]);
				$new_location->cur_qty = $qty;
				if($new_location->location_type_id == 1){
					$qty_required = $max_qty - $new_location->cur_qty;
					if(($max_qty != '') && (($qty) > ($max_qty)) && $new_location->location){
						return response(["error" => true, 'message' => 'Max Qty Violation Error for the location '.$location. ' required qty is '.$qty_required], 400);
					}
					if($max_qty == ''){
						if($qty > 100){
							$max_qty = $qty;
						}else{
							$max_qty = 100;
						}
					}
				}
				$new_location->max_qty = $max_qty;
				$new_location->save();
			}else{
				$max_qty = $new_location->max_qty;
				if($new_location->location_type_id == 1){
					if($qty > 100){
						$max_qty = $qty;
					}else{
						$max_qty = 100;
					}
				}
				
				MasterShelf::create([
					'aisle_id'    => $new_location->aisle_id,
					'bay_id' => $new_location->bay_id,
					'shelf' => $new_location->shelf,
					'slot'  => $new_location->slot,
					'ETIN'  => $ShelfData['ETIN'],
					'max_qty' => $max_qty,
					'cur_qty' => $qty,
					'address' => $new_location->address,
					'location_type_id' => $new_location->location_type_id,
					'parent_id' => $new_location->parent_id
				]);
				InventoryAdjustmentLog([
					'ETIN' => $ShelfData['ETIN'],
					'location' => $new_location->address,
					'starting_qty' => 0,
					'ending_qty' => ($qty),
					'total_change' => '+'.$qty,
					'user' => $user_id,
					'reference' => 'Warehouse Inventory',
					'reference_value' => json_encode($request->all()),
					'reference_description' => 'Updating Qty: TransferProduct'
				]);
			}

			$old_location = MasterShelf::find($ShelfData['id']);
			InventoryAdjustmentLog([
				'ETIN' => $ShelfData['ETIN'],
				'location' => $old_location->address,
				'starting_qty' => $old_location->cur_qty,
				'ending_qty' => ($old_location->cur_qty - $qty),
				'total_change' => '-'.$qty,
				'user' => $user_id,
				'reference' => 'Warehouse Inventory',
				'reference_value' => json_encode($request->all()),
				'reference_description' => 'Updating Qty: TransferProduct'
			]);
			$old_location->cur_qty = ($old_location->cur_qty - $qty);
			$old_location->save();

			if($pallet_number != ''){
				$BackStockPallet = BackStockPallet::where('pallet_number',$pallet_number)->first();
				if($BackStockPallet){
					$BackStockPalletItem = BackStockPalletItem::where('backstock_pallet_id',$BackStockPallet->id)->where('ETIN',$ShelfData['ETIN'])->first();
					if(($BackStockPalletItem->quantity - $qty) >= 0){
						$BackStockPalletItem->quantity = $BackStockPalletItem->quantity - $qty;
						$BackStockPalletItem->save();
					}
				}

				
				UserLogs([
					'user_id' => $user_id,
					'action' => 'Scan',
					'task' => 'Backstock',
					'details' => $ShelfData['ETIN'].' from pallet #' .$pallet_number.' has been transfered',
					'type' => 'CWMS'
				]);
			}

			if($old_location->cur_qty == 0){
				$old_location->delete();
			}
		}

		return response(["error" => false, 'message' => 'Success'], 200);


	}
}
