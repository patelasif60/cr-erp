<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\MasterShelf;
use App\BayMaster;
use App\LocationType;
use App\MasterProduct;
use App\Http\Resources\LocationType\LocationTypeResource;
use App\Http\Resources\Shelf\ShelfResource;
use App\Http\Resources\Product\ProductResource;
use App\AisleMaster;
use App\MasterShelfLotAndExpiry;
use App\ExpirationLotManagement;

class MasterShelfApiController extends Controller
{
	/*
		Method 		: createShelf
		Description : Create new shelf for a Bay
	*/
    public function createShelf(Request $request){
        $validator = Validator::make($request->all(), [
            'aisle_id' => 'required',
            'bay_id' => 'required',
            'total_slotes' => 'required',
            'location_type' => 'required',
            'parent_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 422);
        }

        $aisleMaster = AisleMaster::find($request->aisle_id);

        $shelf = MasterShelf::where('aisle_id', $request->aisle_id)
                ->where('bay_id', $request->bay_id)->max('shelf');
        // $bayNumber = BayMaster::find($request->bay_id);

        $bay_obj = BayMaster::where('id',$request->bay_id)->first();
        $bay_number = 1;
        if($bay_obj){
            $bay_number = $bay_obj->bay_number;
        }

        $shelf = $shelf + 1;
        
        $parent_id = $request->parent_id;
        if (isset($parent_id) && $parent_id > 0) {
            $parent_shelf = MasterShelf::where('id', $parent_id)->first();
            if (!isset($parent_shelf)) {
                return response(["error" => true, 'message' => 'Wrong Parent Shelf Id!'], 400);
            }
            $parent_address = $parent_shelf->address;
        }

        for($i = 0; $i < $request->total_slotes; $i++){
            $slot = $i+1;
            $masterShelf = new MasterShelf;
            $masterShelf->aisle_id = $request->aisle_id;
            $masterShelf->bay_id = $request->bay_id;
            $masterShelf->shelf =  $shelf;
            $masterShelf->slot = $slot;
            $masterShelf->location_type_id = $request->location_type;
            $masterShelf->parent_id = $parent_id; 
            $masterShelf->product_temp_id = $request->product_temp_id;
            if (isset($parent_id) && $parent_id > 0) {
                $masterShelf->address = $parent_address . '#' . $bay_number.':'.$shelf . ':' . $slot;
            } else {
                // $masterShelf->address = $aisleMaster->aisle_name . ':' . $shelf . ':' . $slot;
                $masterShelf->address = $aisleMaster->aisle_name . ':'.$bay_number.':' . $shelf.':'.$slot;
            }            
            $result = $masterShelf->save();

            if(!$result){
                return response(["error" => true, 'message' => 'Shelf/Slot not created successfully!'], 406);
            }
        }

    	return response(["error" => false, 'message' => 'Shelf created successfully!'], 200);
    }

	public function getAllLocationType(Request $request){
		$allLocationType = LocationType::get();
        $data = LocationTypeResource::collection($allLocationType);

        if (count($allLocationType->toArray()) > 0) {
        	$response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
			return response($response, 200);
        }
        else{
        	$response = ["error" => true, "message" => 'Data not found!'];
			return response($response, 400);
        }
	}
    public function getAllShelfByBayId(Request $request,$id,$address=null)
    {
        $address = $request->address;
        $id = $request->id;   
        if($address == 'null'){
            $address = '';
        }
        $allShelf_obj = MasterShelf::leftJoin('processing_groups', 'processing_groups.id', '=', 'master_shelf.product_temp_id')->select('master_shelf.*','processing_groups.group_name')->with('child')->where('bay_id', $id);
        if($address != ''){
            $allShelf_obj->where('master_shelf.address',$address);
        }
        $allShelf = $allShelf_obj->orderBy('shelf')->orderBy('slot')->get();
        $data = ShelfResource::collection($allShelf);

        if (count($allShelf->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => false, "message" => 'Data not found!',"data" => $data];
            return response($response,200);
        }
    }

    public function getlocationHirarchyByBayId(Request $request,$id)
    {
        $allShelf = MasterShelf::with('child')->where('bay_id', $id)->orderBy('shelf')->orderBy('slot')->get();
        $data = ShelfResource::collection($allShelf);
        $response = ["error" => false, "message" => 'Data not found!',"data" => $data];
        return response($response,200);
        
    }
    public function getOnlyShelfByBayId(Request $request, $id, $parent_id)
    {
        $allShelf = MasterShelf::where('bay_id', $id)->where('parent_id', $parent_id)->distinct()->orderBy('shelf')->get(['shelf', 'parent_id']);

        if (count($allShelf->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $allShelf];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }
    public function getSlotByShelfBay(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'shelf' => 'required',
        ]);
        if ($validator->fails()) {
            return response(["error" => true,"message" => $validator->errors()->all()], 400);
        }

        $allSlot = MasterShelf::where('bay_id', $id)->where('shelf',$request->shelf)->distinct()->select('slot')->orderBy('slot')->get();

        if (count($allSlot->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $allSlot];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }
    public function getAllProducts(Request $request){
        $allProducts_obj = MasterProduct::where('is_approve', 1)->whereNull('parent_ETIN');
        if($request->search)
        {
            $search = $request->search;
            $allProducts = $allProducts_obj->where(function($q) use($search){
                $q->where('ETIN','LIKE','%'.$search.'%');
                $q->orWhere('full_product_desc','LIKE','%'.$search.'%');
                $q->orWhere('product_listing_name','LIKE','%'.$search.'%');
                $q->orWhere('upc','LIKE','%'.$search.'%');
                $q->orWhere('gtin','LIKE','%'.$search.'%');
            });
        }
        
        if($request->warehouse_id){
            $allProducts_obj->whereRaw('FIND_IN_SET(\''.$request->warehouse_id.'\',warehouses_assigned)');
        }
        $allProducts = $allProducts_obj->take(50)->get();
        $data = ProductResource::collection($allProducts);

        if (count($allProducts->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }

    public function getAllApprovedProducts(Request $request){
        $allProducts_obj = MasterProduct::where('is_approve', 1);
        if($request->search)
        {
            $search = $request->search;
            $allProducts = $allProducts_obj->where(function($q) use($search){
                $q->where('ETIN','LIKE','%'.$search.'%');
                $q->orWhere('full_product_desc','LIKE','%'.$search.'%');
                $q->orWhere('product_listing_name','LIKE','%'.$search.'%');
                $q->orWhere('upc','LIKE','%'.$search.'%');
                $q->orWhere('gtin','LIKE','%'.$search.'%');
            });
        }
        $allProducts = $allProducts_obj->take(50)->get();
        $data = ProductResource::collection($allProducts);

        if (count($allProducts->toArray()) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }

    public function geProtLotExp(Request $request){
        $data = [];
        $upc = $request->upc;
        $address = $request->address;
        if($upc != ''){

            $pro_obj = MasterProduct::where('is_approve', 1);
            $pro_obj->where(function($q) use($upc){
                $q->where('ETIN','LIKE','%'.$upc.'%');
                $q->orWhere('upc','LIKE','%'.$upc.'%');
                $q->orWhere('gtin','LIKE','%'.$upc.'%');
            });

            $pro = $pro_obj->first();
            if($pro){
                $allProducts_obj = MasterShelfLotAndExpiry::where('ETIN',$pro->ETIN)->where('address',$address);
                $data = $allProducts_obj->get();
            }

        }
        
        
        $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
        return response($response, 200);
        
        
    }

    public function deleteProduct(Request $request,$id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $shelf = MasterShelf::find($id);
        $ailse = $shelf->ailse;
        $warehouse_id = isset($ailse->warehouse_id) ? $ailse->warehouse_id : null;

        $Products = MasterProduct::whereRaw('FIND_IN_SET(\''.$shelf->ETIN.'\',parent_ETIN)')->pluck('ETIN');
        $get_all_shelf_product = MasterShelf::leftjoin('master_aisle',function($join){
            $join->on('master_aisle.id','=','master_shelf.aisle_id');
        })->whereIN('ETIN',$Products)->where('master_aisle.warehouse_id',$warehouse_id)->select('master_shelf.id')->get();
        if($get_all_shelf_product){
            foreach($get_all_shelf_product as $row_pro){
                
                InventoryAdjustmentLog([
                    'ETIN' => $row_pro->ETIN,
                    'location' => $row_pro->address,
                    'starting_qty' => $row_pro->cur_qty,
                    'ending_qty' => 0,
                    'total_change' => '-'.$row_pro->cur_qty,
                    'user' => $user_id,
                    'reference' => 'Delete Product',
                    'reference_value' => 'Shelf id: '.$row_pro->id,
                    'reference_description' => 'Deleting product from the location: deleteProduct'
                ]);

                $row_shelf = MasterShelf::find($row_pro->id);
                $row_shelf->ETIN = null;
                $row_shelf->max_qty = null;
                $row_shelf->cur_qty = null;
                $row_shelf->save();

                

            }
        }
        if($shelf)
        {
            InventoryAdjustmentLog([
                'ETIN' => $shelf->ETIN,
                'location' => $shelf->address,
                'starting_qty' => $shelf->cur_qty,
                'ending_qty' => 0,
                'total_change' => '-'.$shelf->cur_qty,
                'user' => $user_id,
                'reference' => 'Delete Product',
                'reference_value' => 'Shelf id: '.$id,
                'reference_description' => 'Deleting product from the location: deleteProduct'
            ]);

            $shelf->ETIN = null;
            $shelf->max_qty = null;
            $shelf->cur_qty = null;
            $shelf->save();
            return response(["error" => false, 'message' => 'Shelf product delete successfully!'], 200);
        }
        return response(["error" => true, 'message' => 'data not found'], 404);
    }
    public function deleteSlot(Request $request,$id){
        $shelf = MasterShelf::find($id);

    
        if($shelf)
        {
            $get_child_product = MasterShelf::where('parent_id',$id)->first();
            if($get_child_product){
                return response(["error" => true, 'message' => 'Can not delete the position please delete child positions first'], 404);        
            }
           $shelf->delete();
            return response(["error" => false, 'message' => 'Slot delete successfully!'], 200);
        }
        return response(["error" => true, 'message' => 'data not found'], 404);
    }
    public function getShelfById(Request $request,$id)
    {
        $shelf = MasterShelf::find($id);
        $data = new ShelfResource($shelf);

        if ($shelf) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        }
    }
    public function doRestock(Request $request,$etin,$addr) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $location = DB::table('master_shelf')
        ->join('location_type', 'master_shelf.location_type_id', '=', 'location_type.id')
        ->where('master_shelf.ETIN', $etin)->where('location_type.id', 5)->where('address',$addr)->first();
        
        if (!$location) {
            return response(["error" => true, 'message' => 'No Put Away location found for ETIN: ' . $etin], 404);
        }

        $qtyCanBeAdded = $location->max_qty - $location->cur_qty;
        if ($location->cur_qty == $location->max_qty) {
            return response(["error" => true, 'message' => 'Already stock full for ETIN: ' . $etin],400);
        }

        $backStock = DB::table('master_shelf')
            ->join('location_type', 'master_shelf.location_type_id', '=', 'location_type.id')
            ->where('master_shelf.ETIN', $etin)->where('location_type.id', 2)->first();
        if (!$backStock) {
            return response(["error" => true, 'message' => 'No backstock location found for ETIN: ' . $etin],404);
        }

        $backStockQty = $backStock->cur_qty ? $backStock->cur_qty : 0;
        $backStockstarting_qty = $backStock->cur_qty;
        $locationstarting_qty = $location->cur_qty;
        if ($backStockQty == 0) {
            return response(["error" => true, 'message' => 'No item in backstock'], 404);
        }

         if ($backStockQty < $qtyCanBeAdded) {
            $location->cur_qty = $location->cur_qty + $backStockQty;
            $backStock->cur_qty = 0;
        } 
        else {
            $location->cur_qty = $location->max_qty;
            $backStock->cur_qty = $backStock->cur_qty - $qtyCanBeAdded;
        }

        InventoryAdjustmentLog([
            'ETIN' => $backStock->ETIN,
            'location' => $backStock->address,
            'starting_qty' => $backStockstarting_qty,
            'ending_qty' => ($backStock->cur_qty),
            'total_change' => ($backStockstarting_qty - $backStock->cur_qty),
            'user' => $user_id,
            'reference' => 'ReStock',
            'reference_value' => json_encode($request->all()),
            'reference_description' => 'Updating Qty: doRestock'
        ]);

        InventoryAdjustmentLog([
            'ETIN' => $location->ETIN,
            'location' => $location->address,
            'starting_qty' => $locationstarting_qty,
            'ending_qty' => ($location->cur_qty),
            'total_change' => ($locationstarting_qty - $location->cur_qty),
            'user' => $user_id,
            'reference' => 'ReStock',
            'reference_value' => json_encode($request->all()),
            'reference_description' => 'Updating Qty: doRestock'
        ]);

        
        MasterShelf::where('address', $location->address)->update(['cur_qty' => $location->cur_qty]);
        MasterShelf::where('address', $backStock->address)->update(['cur_qty' => $backStock->cur_qty]);
        
        return response(["error" => false, 'message' => 'Restock Complete'], 200);
    }

    public function getShelfInfoByAddres(Request $request){
        $shelfinfo = MasterShelf::where('aisle_id', $request->aisle_id)->where('bay_id',$request->bay_id)->where('shelf',$request->shelf)->where('slot',$request->slot)->first();
        return response(["error" => false, 'message' => 'Success','data' => $shelfinfo], 200);
    }

    public function getProductInventoryInfo(Request $request){
		$request->validate([
			'upc' => 'required'
		]);
		
		$upc = $request->upc;
        $warehouse_id = $request->warehouse_id;
        $masterProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
			$q->orWhere('ETIN',$upc);
            $q->orWhere('product_listing_name','LIKE','%'.$upc.'%');
        })->first();
        if (!$masterProduct) {
            return response(["error" => true, 'message' => 'Put to side for MGR review'], 404);
        } else if (!$masterProduct->ETIN) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 404);
        }

        $product_inventory = MasterShelf::leftJoin('master_shelf_lot_and_expiry',function($join){
            $join->on('master_shelf_lot_and_expiry.ETIN','=','master_shelf.ETIN');
            $join->on('master_shelf_lot_and_expiry.address','=','master_shelf.address');
        })->select('master_shelf.*','master_shelf_lot_and_expiry.lot','master_shelf_lot_and_expiry.exp_date','master_shelf_lot_and_expiry.qty as lot_qty')->where('master_shelf.ETIN',$masterProduct->ETIN)->orderBy('master_shelf.address','ASC')->get();
		// $product_inventory = $masterProduct->masterShelf;
        $result = [];

        if($product_inventory){
            foreach($product_inventory as $row_p_i){
                $order_warehouse = isset($row_p_i->ailse->warehouse_id) ? $row_p_i->ailse->warehouse_id : NULL;
                if($warehouse_id == $order_warehouse){
                    // $MS = new MasterShelf;
                    // $EXPLOT = $MS->GetMasterShelpLotAndExp([
                    //     'ETIN' => $masterProduct->ETIN,
                    //     'address' => $row_p_i->address
                    // ]);
                    
                    $pd = [
                        'ETIN' => $masterProduct->ETIN,
                        'upc' => $masterProduct->upc,
                        'product_listing_name' => $masterProduct->product_listing_name,
                        'address' => $row_p_i->address,
                        'max_qty' => $row_p_i->max_qty,
                        'cur_qty' => $row_p_i->cur_qty,
                        'location_type' => isset($row_p_i->location_type->type) ? $row_p_i->location_type->type : '',
                        'warehouse' => isset($row_p_i->ailse->warehouse_name->warehouses) ? $row_p_i->ailse->warehouse_name->warehouses :  '',
                        'lot_qty' => $row_p_i->lot_qty,
                        'exp_date' => $row_p_i->exp_date,
                        'lot' => $row_p_i->lot
                        // 'lot_exp' => $EXPLOT
                    ];
                    $result[] = $pd;
                }
                
            }
        }

        $child_products = MasterProduct::whereRaw('FIND_IN_SET(\''.$masterProduct->ETIN.'\',parent_ETIN)')->get();
        if($child_products){
            foreach($child_products as $row_C_P){
                $child_product_inventory = $row_C_P->masterShelf;
                if($child_product_inventory){
                    foreach($child_product_inventory as $row_c_p_i){
                        $order_warehouse = isset($row_c_p_i->ailse->warehouse_id) ? $row_c_p_i->ailse->warehouse_id : NULL;
                        if($warehouse_id == $order_warehouse){
                            $MS = new MasterShelf;
                            $EXPLOT = $MS->GetMasterShelpLotAndExp([
                                'ETIN' => $row_C_P->ETIN,
                                'address' => $row_c_p_i->address
                            ]);
                            $pd = [
                                'ETIN' => $row_C_P->ETIN,
                                'upc' => $row_C_P->upc,
                                'product_listing_name' => $row_C_P->product_listing_name,
                                'address' => $row_c_p_i->address,
                                'max_qty' => $row_c_p_i->max_qty,
                                'cur_qty' => $row_c_p_i->cur_qty,
                                'location_type' => $row_c_p_i->location_type->type,
                                'warehouse' => isset($row_c_p_i->ailse->warehouse_name->warehouses) ? $row_c_p_i->ailse->warehouse_name->warehouses :  '',
                                'lot_exp' => $EXPLOT
                            ];
                            $result[] = $pd;
                        }
                    }
                }       
            }
        }

        if (count($result) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $result];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        } 

        
	}
}