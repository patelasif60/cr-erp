<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\BayMaster;
use App\MasterShelf;
use App\WarehouseInventories;
use App\LocationType;
use App\Http\Resources\Bay\BayResource;
use Log;

class MasterBayApiController extends Controller
{
	/*
		Method 		: getBayByAisle
		Description : Use for get Bay by Aisle
	*/
    public function getBayByAisle(Request $request,$id, $parent_id) {
    	$allBay = BayMaster::where('aisle_id', $id)->where('parent_id', $parent_id)->orderBy('id','ASC')->get();
        $data = BayResource::collection($allBay);
    	
        // if (count($allBay->toArray()) > 0) {
    		$response = ["error"=>false, 'message' => 'Data found successfully', "data" => $data];
			return response($response, 200);
    	// }
    	// else{
    	// 	$response = ["error" => true, "message" => 'Data not found!'];
		// 	return response($response, 400);
    	// }
    }
    public function getBayAisle(Request $request, $id, $parent_id) {
        $allBay = BayMaster::where('aisle_id', $id)->where('parent_id', $parent_id)
                    ->select('id','bay_number','type','parent_id')->orderBy('id','ASC')->get();
        
        // if (count($allBay->toArray()) > 0) {
            $response = ["error"=>false, 'message' => 'Data found successfully', "data" => $allBay];
            return response($response, 200);
        // }
        // else{
        //  $response = ["error" => true, "message" => 'Data not found!'];
        //  return response($response, 400);
        // }
    }

    /*
		Method 		: getBayById
		Description : Use for get Bay by Bay
	*/
    public function getBayById(Request $request,$id) {
    	$bayResult = BayMaster::find($id);
        $data = new BayResource($bayResult);

    	if ($bayResult) {
    		$response = ["error" => false, 'message' => 'Data found successfully', "data" => $data];
			return response($response, 200);
    	}
    	else{
    		$response = ["error" => true, "message" => 'Data not found!'];
			return response($response, 400);
    	}
    }

    /*
        Method      : getAllBay
        Description : Use for get All the Bay 
    */
    public function getAllBay(Request $request){
        $allBay = BayMaster::orderByDesc('id')->get();
        $data = BayResource::collection($allBay);

        if (count($allBay->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }

    /*
		Method 		: createBay
		Description : Use for Create the Bay
	*/
    public function createBay(Request $request) {
    	$validator = Validator::make($request->all(), [
            'aisle_id' => 'required',
            'type' => 'required', /* Pallet Rack, Metro, Pallet, Bin, Muscle Rack */
            'parent_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }
        $parent_id = $request->parent_id;
        $bayNumber = BayMaster::where('aisle_id', $request->aisle_id)->where('parent_id',$parent_id)->max('bay_number');

        
        if (isset($parent_id) && $parent_id > 0) {
            $parent_shelf = MasterShelf::where('id', $parent_id)->first();
            if (!isset($parent_shelf)) {
                return response(["error" => true, 'message' => 'Wrong Parent Shelf Id!'], 400);
            }
        }
                    
        $bayNumber = $bayNumber + 1;
        Log::channel('WMS')->info('Creating new Bay');
        $bayMaster = new BayMaster;
        $bayMaster->aisle_id = $request->aisle_id;
        $bayMaster->type = $request->type;
        $bayMaster->parent_id = isset($parent_id) ? $parent_id : 0;
        $bayMaster->bay_number = $bayNumber;
        $result = $bayMaster->save();
        Log::channel('WMS')->info('New Bay Created: '.$bayMaster->id);

    	if($result){
            Log::channel('WMS')->info('Bay created successfully');
    		return response(["error" => false, 'message' => 'Bay created successfully!'], 200);
    	}
    	else{
            Log::channel('WMS')->info('Bay not created!');
    		return response(["error" => true, 'message' => 'Bay not created!'], 406);
    	}
    }


    public function addPosition(Request $request) {
    	$validator = Validator::make($request->all(), [
            'aisle_id' => 'required',
            'total_positions' => 'required', /* Pallet Rack, Metro, Pallet, Bin, Muscle Rack */
            'parent_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }
        $parent_id = $request->parent_id;
        $total_positions = $request->total_positions;
        $bayNumber = BayMaster::where('aisle_id', $request->aisle_id)->where('parent_id',$parent_id)->max('bay_number');

        
        if (isset($parent_id) && $parent_id > 0) {
            $parent_shelf = MasterShelf::where('id', $parent_id)->first();
            if (!isset($parent_shelf)) {
                return response(["error" => true, 'message' => 'Wrong Parent Shelf Id!'], 400);
            }
        }
                    
        if(($bayNumber + $total_positions) > 25){
            return response(["error" => true, 'message' => 'Max Position can not be greater than 25'], 400);
        }
        
        $bayNumber = $bayNumber + 1;
        for($i = $bayNumber; $i < ($bayNumber + $total_positions); $i++){
            Log::channel('WMS')->info('Creating new Bay');
            $bayMaster = new BayMaster;
            $bayMaster->aisle_id = $request->aisle_id;
            $bayMaster->parent_id = isset($parent_id) ? $parent_id : 0;
            $bayMaster->bay_number = $i;
            $result = $bayMaster->save();
            Log::channel('WMS')->info('New Bay Created: '.$bayMaster->id);
        }
        
        
        

    	// if($result){
            Log::channel('WMS')->info('Bay created successfully');
    		return response(["error" => false, 'message' => 'Bay created successfully!'], 200);
    	// }
    	
    }

    /*
		Method 		: editeBay
		Description : Use for Edit the Bay
	*/
    public function editeBay(Request $request) {
    	$validator = Validator::make($request->all(), [
    		'bay_id' => 'required',
            'type' => 'required', /* Bay, Metro, Pallet, Bin, Muscle Rack */
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }
        /* CHECK ASILE/BAY/SHELF IS AVAILABLE OR NOT */
        $where = [
        		'id' => $request->bay_id
        	];
        $bayResult = BayMaster::select('id')->where($where)->get();
        if (count($bayResult->toArray()) == 0) {
        	return response(["error" => true, 'message' => 'Bay or shelf location not available!'], 406);
        }

        $bayResult->type = $request->type;
        $result = $bayResult->save();
        
    	if($result){
    		return response(["error" => false, 'message' => 'Bay updated successfully!'], 200);
    	}
    	else{
    		return response(["error" => true, 'message' => 'Bay not upadated!'], 406);
    	}
    }

    /*
		Method 		: deleteBay
		Description : Use for Delete particular one Bay
	*/
    public function deleteBay(Request $request,$id){
        
        $bayMaster = BayMaster::find($id);
        if($bayMaster)
        {
            if($bayMaster->type !== ''){
                $Pro = MasterShelf::where('bay_id',$id)->where('cur_qty','>',0)->get();
                if(count($Pro) > 0){
                    return response(["error" => true, 'message' => 'Products has been assiged please delete the product first'], 404);            
                }
                $bayMaster->type = NULL;
                $bayMaster->save();
                MasterShelf::where('bay_id',$id)->delete();
            }else{
                $bayMaster->delete();
                MasterShelf::where('bay_id',$id)->delete();
            }
            
            return response(["error" => false, 'message' => 'Bay deleted successfully!'], 200);
        }
        return response(["error" => true, 'message' => 'data not found'], 404);

   //  	$validator = Validator::make($request->all(), [
   //          'bay_id' => 'required'
   //      ]);
   //      if ($validator->fails()) {
   //          return response(["error" => false,"message" => $validator->errors()->all()], 422);
   //      }
        
   //      $warehosue_result = DB::table('warehouse_inventories')
			// 			        ->selectRaw('warehouse')
			// 			        ->where('master_bay_id', '=', $request->bay_id)
			// 			        ->get();
   //      if (count($warehosue_result->toArray()) == 0) {
	  //   	$bay= DB::table('master_bay')->where('bay', $request->bay_id)->delete();
	  //   	if ($bay) {
			// 	return response(["error" => false, 'message' => 'Bay deleted successfully!'], 200);
			// }
			// else{
	  //   		return response(["error" => true, 'message' => 'Bay not deleted!'], 406);
	  //   	}
   //      }
   //      else{
   //  		return response(["error" => true, 'message' => 'Bay is not empty! Some products are there.'], 406);
   //  	}
    }
}