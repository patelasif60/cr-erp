<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\AisleMaster;
use App\MasterProduct;
use App\ProductTemperature;
use App\WarehouseInventories;
use App\WareHouse;
use App\Http\Resources\Aisle\AisleResource;
use App\Http\Resources\ProductTemperature\ProductTemperatureResource;
use App\MasterShelf;
use App\BayMaster;
use App\ProcessingGroups as AppProcessingGroups;
use ProcessingGroups;

class AisleApiController extends Controller
{
	
	/*
		Method 		: getAllAisleByWharehouse
		Description : Use for get All the Aisle by Warehouse
	*/
    public function getAllAisleByWharehouse(Request $request){
    	$validator = Validator::make($request->all(), [
            'warehouse_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 400);
        }

        $allAisle = AisleMaster::where('warehouse_id', $request->warehouse_id)->with('storage_type','warehouse_name')->get();
        //select('id','warehouse_id','aisle_name','product_temp_id')->
        $data = AisleResource::collection($allAisle);

        // if (count($allAisle->toArray()) > 0) {
        	$response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
			return response($response, 200);
        // }
        // else{
        // 	$response = ["error" => true, "message" => 'Data not found!'];
		// 	return response($response, 400);
        // }
    }

    /*
		Method 		: createAisle
		Description : Use for Create the Aisle
	*/
    public function createAisle(Request $request) {

    	$validator = Validator::make($request->all(), [
            'warehouse_id' => 'required',
            'aisle_name' => 'required',
            'product_temp_id' => 'required',
            // 'location_name' => 'required'
        ]);
        if ($validator->fails()) {
             return response(["error" => true,"message" => $validator->errors()->all()], 400);
        }
        $existingAisle = AisleMaster::where([
					        	'warehouse_id'=> $request->warehouse_id,
					        	'aisle_name'=> $request->aisle_name
					        	])->first();
        if ( $existingAisle ) {
			return response(["error" => false, 'message' => 'Product markout successfully!', 'data' => $existingAisle], 200);
        }
        
    	$AisleMaster = new AisleMaster;
    	$AisleMaster->warehouse_id = $request->warehouse_id;
    	$AisleMaster->aisle_name = $request->aisle_name;
    	$AisleMaster->product_temp_id = $request->product_temp_id;
        $AisleMaster->location_name = $request->location_name;
    	$result = $AisleMaster->save();
    	if($result){
    		return response(["error" => false, 'message' => 'Aisle created successfully!', 'data' => $AisleMaster],200);
    	}
    	else{
    		return response(["error" => true, 'message' => 'Aisle not created!'],406);
    	}
    }

	public function ProDetailByETIN($ETIN){
		$result = MasterProduct::where('ETIN',$ETIN)->first();
		if(!$result){
			$response = ["error" => true,"message" => 'Data not found!'];
			return response($response, 400);
		}

		$response = ["error" => false, 'message' => 'Data found successfully',"data" => $result, ];
		return response($response, 200);
	}

    /*
		Method 		: getProductStorageType
		Description : Use for Get Product Temprature
	*/
    public function getProductTemperatureList(Request $request) {
		$result = ProductTemperature::all();
			
    	if (count($result) > 0) {
    		$data = ProductTemperatureResource::collection($result);
    		$response = ["error" => false, 'message' => 'Data found successfully',"data" => $data, ];
			return response($response, 200);
    	}
    	else{
    		$response = ["error" => true,"message" => 'Data not found!'];
			return response($response, 400);
    	}
    }

    /*
		Method 		: getAllWareHouse
		Description : Use for Get Warehouse List
	*/
    public function getAllWareHouse(Request $request) {
    	$warehouses = new Warehouse();	 
		$result = $warehouses->warehouseList();
		if (count($result) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400];
			return response($response, 400);
    	}
    }

    /*
		Method 		: getAllAisle
		Description : Use for get All the Aisle 
	*/
    public function getAllAisle(Request $request){
		if($request->warehouse_id == "") $request->warehouse_id = 1;
		$limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

		$select_array = [];
        $allAisle_obj = AisleMaster::leftJoin('processing_groups', 'processing_groups.id', '=', 'master_aisle.product_temp_id')->leftJoin('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')->where('master_aisle.warehouse_id',$request->warehouse_id);

		$allAisle_obj->select([
			'master_aisle.id',
			'location_name',
			'master_aisle.warehouse_id',
			'warehouses.warehouses as warehouse_name',
			'aisle_name',
			'product_temp_id',
			'processing_groups.group_name as product_temperature'
		]);

		$select_array = [
			'id' => 'master_aisle.id',
			'location_name' => 'location_name',
			'warehouse_id' => 'master_aisle.warehouse_id',
			'warehouse_name' => 'warehouses.warehouses',
			'aisle_name' => 'aisle_name',
			'product_temp_id' => 'product_temp_id',
			'product_temperature' => 'processing_groups.group_name'
		];

		if(isset($request->sortBy['id'])){
			if($request->sortBy['desc']){
				$sort = 'DESC';
			}else{
				$sort = 'ASC';
			}
			$allAisle_obj->orderBy($select_array[$request->sortBy['id']],$sort);
		}



		if(isset($request->text) && $request->text != ''){
			$search = $request->text;
			$allAisle_obj->where(function($q) use($search){
				$q->where('location_name','LIKE','%'.$search.'%');
				$q->OrWhere('warehouses.warehouses','LIKE','%'.$search.'%');
				$q->OrWhere('aisle_name','LIKE','%'.$search.'%');
				$q->OrWhere('processing_groups.group_name','LIKE','%'.$search.'%');
			});
		}

		$all = $allAisle_obj;
		$all_ailse = $all->get();
		$totalRecords = count($all_ailse);
		if($limit != -1){
			$allAisle_obj->skip($offset)->take($limit);
		}
		
		$allAisle = $allAisle_obj->get();
        
        if (count($allAisle->toArray()) > 0) {
        	$response = ["error" => false, 'message' => 'Data found successfully',"data" => $allAisle,'totalRecords' => $totalRecords];
			return response($response, 200);
        }
        else{
        	$response = ["error" => true, "message" => 'Data not found!'];
			return response($response, 400);
        }
    }

	public function SearchAndGoLocation(Request $request){
		$text = $request->text;
		if($text == ''){
			$response = ["message" => 'Location can not be empty', 'status' => 400];
			return response($response, 400);
		}

		$aisle = AisleMaster::where('warehouse_id', $request->warehouse_id)->where('aisle_name',$text)->first();
		if($aisle){
			$response = ["data" => [
				'first' => $aisle->id,
				'second' => 0
			], 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
		}

		$shelf_address = MasterShelf::leftJoin('master_aisle',function($join){
			$join->on('master_aisle.id','=','master_shelf.aisle_id');
		})->where('address',$text)->where('master_aisle.warehouse_id',$request->warehouse_id)->select('master_shelf.*')->first();
		// dd($shelf_address->toArray());
		if($shelf_address){
			$response = ["data" => [
				'first' => $shelf_address->aisle_id,
				'second' => $shelf_address->id
			], 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
		}

		$response = ["message" => 'No Data Found', 'status' => 400];
		return response($response, 400);



	}
    
    /*
		Method 		: getAllAisleById
		Description : Use for get All the Aisle by id
	*/
    public function getAisleById(Request $request,$id,$parent_id){
    
        $allAisle = AisleMaster::where('id', $id)->with('storage_type','warehouse_name')->first();
        $data = new AisleResource($allAisle);
		$parent_info = [];
        if ($allAisle) {

			if ($parent_id > 0) {
				$ms = MasterShelf::leftjoin('master_bay',function($join){
					$join->on('master_bay.id','=','master_shelf.bay_id');
				})->select('master_shelf.*','master_bay.type')->where('master_shelf.id', $parent_id)->first();
				if (!$ms) {
					$response = ["error" => true, "message" => 'Invalid PArent Id'];
				}
				$data->aisle_name = $ms->address;
				$parent_info = $ms->toArray();

			}

        	$response = ["error" => false, 'message' => 'Data found successfully - '.$parent_id,"data" => $data,'parent_info' => $parent_info];
			return response($response, 200);
        }
        else{
        	$response = ["error" => true, "message" => 'Data not found!'];
			return response($response, 400);
        }
    }
    
    /*Edit Aisle */
    public function editAisle(Request $request,$id){
    	$validator = Validator::make($request->all(), [
            'warehouse_id' => 'required',
            'aisle_name' => 'required',
            'product_temp_id' => 'required',
            'location_name' => 'required'
        ]);
        if ($validator->fails()) {
             return response(["error" => true,"message" => $validator->errors()->all()], 400);
        }
        $aisleMaster = AisleMaster::find($id);
        $aisleMaster->warehouse_id = $request->warehouse_id;
    	$aisleMaster->aisle_name = $request->aisle_name;
    	$aisleMaster->product_temp_id = $request->product_temp_id;
        $aisleMaster->location_name = $request->location_name;
    	$result = $aisleMaster->save();
    	return response(["error" => false, 'message' => 'Aisle Edit successfully!'],200);
    }
    
    /*
		Method 		: deleteBay
		Description : Use for Delete particular one Bay
	*/
    public function deleteAisle(Request $request,$id, $delete = 0)
    {
		$aisle = AisleMaster::find($id);
		if($aisle)
		{
			if($delete == 0){
				$shelf_address = MasterShelf::where('aisle_id',$id)->select('id')->first();
				if($shelf_address){
					return response(["error" => true, 'message' => 'Location Has Fixture, Sub fixture or Products Assigned are you sure
					you want to delele this location?','delete' => true], 404);		
				}
			}
			
			
			if($delete == 1){
				$shelf_address = MasterShelf::where('aisle_id',$id)->where('cur_qty','>',0)->select('id')->first();
				if($shelf_address){
					return response(["error" => true, 'message' => 'This location has a product with positive qty, you can not delete this location'], 404);			
				}
				MasterShelf::where('aisle_id',$id)->delete();

			}

			$aisle->delete();
			return response(["error" => false, 'message' => 'Aisle deleted successfully!'], 200);
		}
    	return response(["error" => true, 'message' => 'data not found'], 404);
    }

	/*
		Method 		: getProcessingGroups
		Description : Fetching the List of processing Groups
	*/
    public function getProcessingGroups()
    {
		$pgs = AppProcessingGroups::get(['id', 'group_name'])->all();
		if($pgs && count($pgs) > 0)
		{
			return response(["error" => false, 'data' => $pgs], 200);
		}
    	return response(["error" => true, 'message' => 'Data not found'], 404);
    }

	public function UpdateBayType(Request $request){
		$BM = BayMaster::find($request->id);
		$BM->type = $request->type;
		$BM->save();
		return response(["error" => false, 'message' => 'Success'], 200);
	}

}