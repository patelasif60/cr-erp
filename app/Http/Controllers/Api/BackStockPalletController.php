<?php

namespace App\Http\Controllers\Api;

use App\MasterProduct;
use App\BackStockPallet;
use App\BackStockPalletItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\MasterShelf;
use App\PurchasingDetail;
use App\PurchasingSummary;
use App\ReceivingDetail;
use App\PurchaseOrderExpAndLot;
use App\MasterShelfLotAndExpiry;
use App\Http\Resources\Shelf\ShelfResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BackStockPallet\BackStockPalletResource;

class BackStockPalletController extends Controller
{
    
    public function getLocation($warehouseId, $typeId) {

        $location = DB::table('master_shelf')
            ->join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
            ->where('master_aisle.warehouse_id', $warehouseId)
            ->where('master_shelf.location_type_id', $typeId)
            ->get(['master_shelf.*']);
        
            if (count($location->toArray()) > 0) {
                $response = ["data" => $location, 'message' => 'Data found successfully', 'status' => 200];
                return response($response, 200);
            } else{
                $response = ["message" => 'Data not found!', 'status' => 400, 'error' => false,'data' => []];
                return response($response, 200);
            }
    }

    public function storeBackStockPalletItems(Request $request) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $validator = Validator::make($request->all(), [
            'pallet_id' => 'required',
            'address' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        $bsp = BackStockPallet::where('id', $request->pallet_id)->first();
        if (!$bsp) {
            return response(['error' => true, 'message' => 'Backstock pallet Id: ' . $request->pallet_id . ' is invalid.'], 404);
        }

        $items = BackStockPalletItem::where('backstock_pallet_id', $request->pallet_id)->get();
        if (!$items || count($items) <= 0) {
            return response(['error' => true, 'message' => 'No Items for Backstock pallet Id: ' . $request->pallet_id], 404);
        }
        $warehouse_id = $bsp->warehouse_id;
        $location = MasterShelf::with(['ailse'])->whereHas('ailse',function($q) use($warehouse_id){
            $q->where('warehouse_id','=',$warehouse_id);
        })->where('address', $request->address)->first();
        if (!$location) {
            return response(['error' => true, 'message' => 'Address not found'], 404);
        }

        if($location->location_type_id !== 2){
            return response(['error' => true, 'message' => 'Location has to be backstock location'], 404);
        }

        if($location->ailse->warehouse_id !== $bsp->warehouse_id){
            return response(['error' => true, 'message' => 'Location has to be from same warehouse'], 404);
        }

        if($location->parent != 0){
            return response(['error' => true, 'message' => 'Backstock Pallet can be stored directly to the Aisle shelf/slot'], 404);
        }

        if($location->ETIN != ''){
            return response(['error' => true, 'message' => 'Location is not empty'], 404);
        }

        if(isset($location->bay_name->type) && $location->bay_name->type !== 'Pallet Rack'){
            return response(['error' => true, 'message' => 'Backstock Pallet can only be assigned to Pallet Rack'], 404);
        }
        if($request->bol_number != ''){
            if($items){
                foreach($items as $row_item){
                    $quantity = $row_item->quantity;
                    $GetReceiving = ReceivingDetail::where('etin', $row_item->ETIN)->where('bol_number', $request->bol_number)->first();
                    if($GetReceiving){
                        $qty_remaining = $GetReceiving->qty_remaining;
                        if($row_item->quantity > $qty_remaining){
                            return response(['error' => true, 'message' => 'Qty of ' . $row_item->ETIN.' Can not be greater than required'], 404);   
                            // $qty_remaining
                        }
                    }
                    if($request->bol_number == ''){
                        $decriment_qty = MasterShelf::where('address', $row_item->location)->where('ETIN',$row_item->ETIN)->first();
                        if($decriment_qty){
                            if($quantity > $decriment_qty->cur_qty){
                                return response(['error' => true, 'message' => 'Qty of ' . $row_item->ETIN.' is not enough at location '.$row_item->location], 404);   
                            }
                        }
                    }

                    if($row_item->lot != ''){
                        $POEL = PurchaseOrderExpAndLot::where('bol_number',$request->bol_number)->where('ETIN',$row_item->ETIN)->where('lot',$row_item->lot)->first();
                        $GRLot = new PurchaseOrderExpAndLot();
                        $GetReceivedLot = $GRLot->GetReceivedLot([
                            'ETIN' => $row_item->ETIN,
                            'lot' => $row_item->lot,
                            'bol_number' => $request->bol_number
                        ]);
                        if(!$POEL){
                            return response(['error' => true, 'message' => 'Could find lot and etin combination in purchase order ' . $row_item->ETIN], 400);
                        }
                        if($POEL){
                            if(($row_item->quantity + $GetReceivedLot) > $POEL->qty){
                                return response(['error' => true, 'message' => 'Qty of ' . $row_item->ETIN.' and LOT # '.$row_item->lot.' Can not be greater than required'], 404);   
                            }
                        }
                    }
                }
            }
        }
        
        foreach($items as $row_item){
            $quantity = $row_item->quantity;
            $check_if_address_is_empty = MasterShelf::with(['ailse'])->whereHas('ailse',function($q) use($warehouse_id){
                $q->where('warehouse_id','=',$warehouse_id);
            })->where('address', $request->address)->whereNull('ETIN')->where('location_type_id',2)->first();
            if($check_if_address_is_empty){
                $check_if_address_is_empty->cur_qty = $quantity;
                $check_if_address_is_empty->ETIN = $row_item->ETIN;
                $check_if_address_is_empty->pallet_number = $bsp->pallet_number;
                $check_if_address_is_empty->save();
                InventoryAdjustmentLog([
                    'ETIN' => $row_item->ETIN,
                    'location' => $request->address,
                    'starting_qty' => 0,
                    'ending_qty' => $quantity,
                    'total_change' => '+'.$quantity,
                    'user' => $user_id,
                    'reference' => 'BackStockPallet',
                    'reference_value' => 'pallet_id: '.$request->pallet_id,
                    'reference_description' => 'Assigning ETIN to location while Backstock Pallet'
                ]);
            }else{
                $check_pallet = MasterShelf::with(['ailse'])->whereHas('ailse',function($q) use($warehouse_id){
                    $q->where('warehouse_id','=',$warehouse_id);
                })->where('address', $request->address)->where('ETIN',$row_item->ETIN)->where('location_type_id',2)->first();
                if($check_pallet){
                    $starting_qty = $check_pallet->cur_qty;
                    $check_pallet->cur_qty = $check_pallet->cur_qty + $quantity;
                    $check_pallet->pallet_number = $bsp->pallet_number;
                    $check_pallet->save();
                    InventoryAdjustmentLog([
                        'ETIN' => $row_item->ETIN,
                        'location' => $request->address,
                        'starting_qty' => $starting_qty,
                        'ending_qty' => $check_pallet->cur_qty,
                        'total_change' => '+'.$quantity,
                        'user' => $user_id,
                        'reference' => 'BackStockPallet',
                        'reference_value' => 'pallet_id: '.$request->pallet_id,
                        'reference_description' => 'Updating ETIN Qty while Backstock Pallet'
                    ]);
                }else{
                    MasterShelf::create([
                        'aisle_id' => $location->aisle_id,
                        'bay_id' => $location->bay_id,
                        'shelf' => $location->shelf,
                        'slot' => $location->slot,
                        'address' => $location->address,
                        'location_type_id' => $location->location_type_id,
                        'ETIN' => $row_item->ETIN,
                        'cur_qty' => $quantity,
                        'pallet_number' => $bsp->pallet_number
                    ]);

                    InventoryAdjustmentLog([
                        'ETIN' => $row_item->ETIN,
                        'location' => $location->address,
                        'starting_qty' => 0,
                        'ending_qty' => $quantity,
                        'total_change' => '+'.$quantity,
                        'user' => $user_id,
                        'reference' => 'BackStockPallet',
                        'reference_value' => 'pallet_id: '.$request->pallet_id,
                        'reference_description' => 'Adding ETIN to location while Backstock Pallet'
                    ]);    
                }
            }

            if($row_item->lot != ''){
                $MSLE = MasterShelfLotAndExpiry::where('ETIN',$row_item->ETIN)->where('address',$request->address)->where('lot',$row_item->lot)->first();
                if($MSLE){
                    $MSLE->qty = $MSLE->qty + $row_item->quantity;
                    $MSLE->save();
                }else{
                    $MSLE = new MasterShelfLotAndExpiry;
                    $MSLE->ETIN = $row_item->ETIN;
                    $MSLE->address = $request->address;
                    $MSLE->qty = $row_item->quantity;
                    $MSLE->exp_date = $row_item->exp_date;
                    $MSLE->lot = $row_item->lot;
                    $MSLE->warehouse = $warehouse_id;
                    $MSLE->save();
                }
            }

            if($request->bol_number == ''){
                $decriment_qty = MasterShelf::with(['ailse'])->whereHas('ailse',function($q) use($warehouse_id){
                    $q->where('warehouse_id','=',$warehouse_id);
                })->where('address', $row_item->location)->where('ETIN',$row_item->ETIN)->first();
                if($decriment_qty){
                    $starting_qty = $decriment_qty->cur_qty;
                    $decriment_qty->cur_qty = $starting_qty - $quantity;
                    $decriment_qty->save();
                    InventoryAdjustmentLog([
                        'ETIN' => $row_item->ETIN,
                        'location' => $row_item->location,
                        'starting_qty' => $starting_qty,
                        'ending_qty' => $decriment_qty->cur_qty,
                        'total_change' => '-'.$quantity,
                        'user' => $user_id,
                        'reference' => 'BackStockPallet',
                        'reference_value' => 'pallet_id: '.$request->pallet_id,
                        'reference_description' => 'Decriment ETIN Qty from location while Backstock Pallet'
                    ]);
                }
                
            }else{
                $RD = ReceivingDetail::where('etin',$row_item->ETIN)->where('bol_number',$request->bol_number)->first();
                if($RD){
                    $remaining = $RD->qty_remaining - $quantity;
                    $RD->qty_remaining = $remaining;
                    $RD->save();

                    if($remaining == 0){
                        PurchasingDetail::where('bol_number',$request->bol_number)->where('etin',$row_item->ETIN)->update([
                            'status' => 'Received',
                            'reference' => 'BackStock',
                            'reference_date' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                

                
            }
        }
        
        $bsp->address = $request->address;
        $bsp->location_type_id = $location->location_type_id;
        $bsp->save();

        if($request->bol_number != ''){
            $bol_number = $request->bol_number;
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Backstock',
                'details' => 'Bol #'.$bol_number.' and pallet # '.$bsp->pallet_number.' Backstocked',
                'type' => 'CWMS',
                'bol_number' => $bol_number
            ]);
        }else{
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Backstock',
                'details' => 'Pallet # '.$bsp->pallet_number.' Backstocked',
                'type' => 'CWMS'
            ]);
        }


        return response(['error' => false, 'message' => 'Items stocking complete'], 200);
    }

    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->warehouse_id == "") $request->warehouse_id = 1;
        $result_obj = BackStockPallet::select('backstock_pallet.*', DB::raw('CASE WHEN clients.company_name IS NULL THEN suppliers.name ELSE clients.company_name  END as client_name'))->leftJoin('purchasing_details',function($q){
            $q->on('purchasing_details.bol_number','=','backstock_pallet.bol_number');
        })->leftJoin('clients',function($q){
            $q->on('clients.id','=','purchasing_details.client_id');
        })
        ->leftJoin('suppliers',function($q){
            $q->on('suppliers.id','=','purchasing_details.supplier_id');
        })->where('backstock_pallet.warehouse_id',$request->warehouse_id);

        $limit = 15;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 1;
        if (isset($request->page)) {
            $page = $request->page;
        }

        if(isset($request->text) && $request->text != ''){
			$search = $request->text;
			$result_obj->where(function($q) use($search){
				$q->where('backstock_pallet.pallet_number','LIKE','%'.$search.'%');
                $q->orwhere('backstock_pallet.address','LIKE','%'.$search.'%');
                $q->orwhere('backstock_pallet.bol_number','LIKE','%'.$search.'%');
			});
		}

        if(isset($request->sortBy['id'])){
			if($request->sortBy['desc']){
				$sort = 'DESC';
			}else{
				$sort = 'ASC';
			}
            $sortBy = $request->sortBy['id'];
			$result_obj->orderByRaw("CAST($sortBy AS UNSIGNED), $sortBy $sort");
		}else{
            $sort = 'DESC';
            $sortBy = 'backstock_pallet.id';
            $result_obj->orderBy($sortBy, $sort);
        }


        $result_obj->groupBy('backstock_pallet.id');

        $offset = ($page - 1) * $limit;

        $all_obj = $result_obj;
        $all_result = $all_obj->get();
        $total_records = count($all_result);
        $result_obj->skip($offset)->take($limit);
        $result = $result_obj->get();
        
        $data = BackStockPalletResource::collection($result);
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $data,
            'total_records' => $total_records
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->bol_number != ''){
            $check = BackStockPallet::where('bol_number',$request->bol_number)->whereNull('bol_number')->first();
            if($check){
                $BackStockPallet = $check;
            }else{
                $BackStockPallet = new BackStockPallet();
            }
            $BackStockPallet->bol_number = $request->bol_number;
        }else{
            $BackStockPallet = new BackStockPallet();
        }
        
        $BackStockPallet->pallet_number	= time();
        $BackStockPallet->warehouse_id = $request->warehouse_id;
        $BackStockPallet->save();

        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $BackStockPallet
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result =  BackStockPallet::find($id);
        $data = new BackStockPalletResource($result);
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $BackStockPallet = BackStockPallet::find($id)->delete();
        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }

    public function add_item(Request $request, $id){
        $header = $request->header('Authorization');
        $user_id = ExtractToken($header);
        $upc = $request->upc;
        $masterProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if (!$masterProduct) {
            return response(["error" => true, 'message' => 'Product not found for UPC: ' . $request->upc], 404);
        } else if (!$masterProduct->ETIN) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 404);
        }
        $child_product = NULL;
        if($masterProduct->parent_ETIN != ''){
            $child_product = $masterProduct;
            $parents = [];
            $parent_ETIN = explode(',',$masterProduct->parent_ETIN);
            if(count($parent_ETIN) > 1){
                if($parent_ETIN){
                    foreach($parent_ETIN as $row_parent){
                        $row_pro = MasterProduct::where('ETIN',$row_parent)->first();
                        if($row_pro){
                            $parents[] = $row_pro;
                        }
                    }
                }
                return response(['error' => false, 'message' => 'Please select products', 'parents' => $parents], 200);        
            }else if(count($parent_ETIN) == 1){
                $masterProduct = MasterProduct::where('ETIN',$parent_ETIN[0])->first();
            }
            
        }

        if(!isset($masterProduct->ETIN)) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 404);
        }

        if($request->bol_number != ''){
            $check_in_receiving_details = ReceivingDetail::where('bol_number',$request->bol_number)->where('ETIN',$masterProduct->ETIN)->first();
            if(!$check_in_receiving_details){
                if(isset($child_product->parent_ETIN) && $child_product->parent_ETIN != ''){
                    $masterProduct = MasterProduct::where(function($q) use($upc){
                        $q->where('upc', $upc);
                        $q->orWhere('gtin',$upc);
                        $q->orWhere('ETIN',$upc);
                    })->first();
                    $check_in_receiving_details = ReceivingDetail::where('bol_number',$request->bol_number)->where('ETIN',$masterProduct->ETIN)->first();
                    if(!$check_in_receiving_details){
                        return response(["error" => true, 'message' => 'UPC Does not present in this BOL'], 404);    
                    }
                }else{
                    return response(["error" => true, 'message' => 'UPC Does not present in this BOL'], 404);
                }
            }
        }

        
        if($request->location != ''){
            $check_address = MasterShelf::where('address',$request->location)->where('ETIN',$masterProduct->ETIN)->first();
            if(!$check_address){
                return response(["error" => true, 'message' => 'UPC Does not belongs to this Location'], 404);
            }
        }
        
        
        // $data = $this->quantityCalculation($id,$masterProduct->ETIN);
        // if($data['quantity'] == 0){
        //     return response(["error" => true, 'message' => 'No quantity found. You can not add item ' . $request->upc], 404);   
        //  }

        $POEL = [];
        if($request->bol_number != ''){
            $POEL = PurchaseOrderExpAndLot::where('bol_number',$request->bol_number)->where('ETIN',$masterProduct->ETIN)->get();
            $newPoEL = [];
            if($POEL){
                foreach($POEL as $rowPOEL){
                    $GRLot = new PurchaseOrderExpAndLot();
                    $GetReceivedLot = $GRLot->GetReceivedLot([
                        'ETIN' => $rowPOEL->ETIN,
                        'lot' => $rowPOEL->lot,
                        'bol_number' => $rowPOEL->bol_number
                    ]);
                    if($GetReceivedLot < $rowPOEL->qty){
                        $newPoEL[] = $rowPOEL;
                    }
                }
            }
            $POEL = $newPoEL;
        }

        $BackStockPalletItem = BackStockPalletItem::where('ETIN', $masterProduct->ETIN)->where('backstock_pallet_id',$id)->where('location',$request->location)->first();
        if ($BackStockPalletItem) {
            if(count($POEL) == 0){
                $BackStockPalletItem->quantity = $BackStockPalletItem->quantity + 1;
                if($request->bol_number != '' && $check_in_receiving_details->qty_remaining < $BackStockPalletItem->quantity){
                    return response(["error" => true, 'message' => 'Qty should not be more than '.$check_in_receiving_details->qty_remaining], 404);
                }
                $BackStockPalletItem->save();
            }
        } else {
            $BackStockPalletItem = new BackStockPalletItem;
            $BackStockPalletItem->ETIN = $masterProduct->ETIN;
            $BackStockPalletItem->location = $request->location;
            $BackStockPalletItem->backstock_pallet_id = $id;
            $BackStockPalletItem->quantity =  count($POEL) == 0 ? 1 : 0;
            $BackStockPalletItem->save();
        }

        if($request->bol_number != ''){
            $bol_number = $request->bol_number;
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Scan',
                'task' => 'Backstock',
                'details' => 'Scan Item '.$masterProduct->ETIN.' for bol '.$bol_number,
                'type' => 'CWMS',
                'bol_number' => $bol_number
            ]);
        }else{
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Scan',
                'task' => 'Backstock',
                'details' => 'Scan Item '.$masterProduct->ETIN.' for pallet id '.$id,
                'type' => 'CWMS'
            ]);
        }

        if(count($POEL) > 0){
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'POEL' => $POEL,
                'ROW' => $BackStockPalletItem
            ]);
        }

        return response()->json([
            'error' => false,
            'message' => 'Success',
           
        ]);
    }

    
    public function get_pallet_items($id){
        $result = BackStockPalletItem::with(['products' => function($q){
            $q->select('id','ETIN','product_listing_name','upc','upc_scanable','gtin_scanable','unit_upc_scanable','unit_gtin_scanable');
        }])->where('backstock_pallet_id',$id)->orderBy('updated_at','DESC')->get();

        $BackStockPallet = BackStockPallet::where('id',$id)->first();
        $data = [];
        if($result){
            foreach($result as $row){
                $allShelf = MasterShelf::where('pallet_number',$BackStockPallet->pallet_number)->where('ETIN',$row->ETIN)->leftJoin('processing_groups', 'processing_groups.id', '=', 'master_shelf.product_temp_id')->select('master_shelf.*','processing_groups.group_name')->get();
                $row->ShelfData = ShelfResource::collection($allShelf);
            }
        }
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $result
        ]);
    }

    public function editItem(Request $request){
        $header = $request->header('Authorization');
        $user_id = ExtractToken($header);
        $data = $request->all();
        $validator = Validator::make($data, [
            '*.id' => 'required',
            '*.quantity' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $pallet = NULL;
        foreach ($data as $val) {
            $detail = BackStockPalletItem::where('id', $val['id'])->first();
            if ($detail) {
                $detail->quantity = $val['quantity'];
                $detail->save();

                if($pallet == ''){
                    $BackStockPallet = BackStockPallet::where('id',$detail->backstock_pallet_id)->first();
                    if($BackStockPallet){
                        $pallet = $BackStockPallet->pallet_number;
                    }
                }
            } else {
                return response(['errors' => true, 'message' => 'Invalid Id: ' . $val['id']], 404);
            }
        }
        if($pallet != ''){
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Backstock',
                'details' => 'Pallet # '.$pallet.' Items Updated',
                'type' => 'CWMS'
            ]);
        }
        return response(['errors' => false, 'message' => 'Data updated in backstock pallet items.'], 200);
    }

    public function SelectLot(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $ETIN = $request->ETIN;
        $location = $request->location;
        $lot = $request->lot;
        $exp_date = $request->exp_date;
        $backstock_pallet_id = $request->backstock_pallet_id;
        $bol_number = $request->bol_number;

        $BI = BackStockPalletItem::where('ETIN',$ETIN)->where('backstock_pallet_id',$backstock_pallet_id)->where('lot',$lot)->where('exp_date',$exp_date)->first();
        $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$ETIN)->where('lot',$lot)->where('exp_date',$exp_date)->first();
        if($BI){
            if(($BI->quantity + 1) > $POEL->qty){
                return response()->json([
                    'error' => true,
                    'message' => 'Qty can not be greater than required qty'
                ],400);
            }
            $BI->quantity = $BI->quantity + 1;
            $BI->save();
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Select',
                'task' => 'BackStock Pallet',
                'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  bol '.$bol_number,
                'type' => 'CWMS',
                'bol_number' => $bol_number
            ]);
            
        }else{
            $BI = BackStockPalletItem::where('ETIN',$ETIN)->where('backstock_pallet_id',$backstock_pallet_id)->whereNull('lot')->whereNull('exp_date')->first();
            if($BI){
                if(($BI->quantity + 1) > $POEL->qty){
                    return response()->json([
                        'error' => true,
                        'message' => 'Qty can not be greater than required qty'
                    ],400);
                }

                $BI->lot = $lot;
                $BI->exp_date = $exp_date;
                $BI->quantity = $BI->quantity + 1;
                $BI->save();
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'BackStock Pallet',
                    'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  bol '.$bol_number,
                    'type' => 'CWMS',
                    'bol_number' => $bol_number
                ]);
            }else{

                $BackStockPalletItem = new BackStockPalletItem;
                $BackStockPalletItem->ETIN = $ETIN;
                $BackStockPalletItem->backstock_pallet_id = $backstock_pallet_id;
                $BackStockPalletItem->quantity =  1;
                $BackStockPalletItem->lot = $lot;
                $BackStockPalletItem->exp_date = $exp_date;
                $BackStockPalletItem->save();

                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'BackStock Pallet',
                    'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  bol '.$bol_number,
                    'type' => 'CWMS',
                    'bol_number' => $bol_number
                ]);
            }

        }


    }

    public function UpdateLot(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $ETIN = $request->ETIN;
        $lot = $request->lot;
        $exp_date = $request->exp_date;
        $bol_number = $request->bol_number;
        $backstock_pallet_id = $request->backstock_pallet_id;


        $pt_away = BackStockPalletItem::find($request->id);
        $ETIN = $pt_away->ETIN;
        $pt = BackStockPalletItem::where('ETIN',$ETIN)->where('backstock_pallet_id',$backstock_pallet_id)->where('lot',$lot)->where('exp_date',$exp_date)->where('id','!=',$request->id)->first();
        if($pt){
            return response()->json([
                'error' => true,
                'message' => 'Lot is already selected'
            ],400);
        }
        
        $pt_away->lot = $lot;
        $pt_away->exp_date = $exp_date;
        $pt_away->save();
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Select',
            'task' => 'BackStock Pallet',
            'details' => 'Lot '.$lot.' has been has been Changed  '.$ETIN.'  &  bol '.$bol_number,
            'type' => 'CWMS',
            'bol_number' => $bol_number
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Success'
        ],200);
        
    }

    public function BSItemInfo($id){
        $BackStockPallet = BackStockPalletItem::find($id);
        $BPallet = BackStockPallet::where('id',$BackStockPallet->backstock_pallet_id)->first();
        $POEL = PurchaseOrderExpAndLot::where('bol_number',$BPallet->bol_number)->where('ETIN',$BackStockPallet->ETIN)->get();
        $newPoEL = [];
        if($POEL){
            foreach($POEL as $rowPOEL){
                $GRLot = new PurchaseOrderExpAndLot();
                $GetReceivedLot = $GRLot->GetReceivedLot([
                    'ETIN' => $rowPOEL->ETIN,
                    'lot' => $rowPOEL->lot,
                    'bol_number' => $rowPOEL->bol_number
                ]);
                if($GetReceivedLot < $rowPOEL->qty){
                    $newPoEL[] = $rowPOEL;
                }
            }
        }
        $POEL = $newPoEL;
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'POEL' => $POEL,
            'ROW' => $BackStockPallet
        ]);
    }

    public function deleteItem(Request $request,$id)
    {
        $header = $request->header('Authorization');
        $user_id = ExtractToken($header);
        $BackStockPallet = BackStockPalletItem::find($id);
        $BPallet = BackStockPallet::where('id',$BackStockPallet->backstock_pallet_id)->first();
        if($BPallet){
            $pallet = $BPallet->pallet_number;
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Click',
                'task' => 'Backstock',
                'details' => ''.$BackStockPallet->ETIN.' Has been deleted from Pallet # '.$pallet,
                'type' => 'CWMS'
            ]);
        }

        

        $BackStockPallet->delete();
        
        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }
    public function checkDiscrepency($id) {
        $backStockPallet = BackStockPallet::find($id);    
        $backStockPalletItem = BackStockPalletItem::where('backstock_pallet_id',$id);
        $backStockPalletItemEtin = $backStockPalletItem->pluck('ETIN')->toArray();
        $backStockPurchaseOrder = $backStockPalletItem->pluck('purchase_order')->toArray();
        if(count($backStockPurchaseOrder)>0)
        {
            $po = [];
            foreach($backStockPurchaseOrder as $key=>$val)
            {
                $po[$key] = explode(',',$val);
            }
            $arraySingle = array_unique(call_user_func_array('array_merge', $po));
        }
        $purchasingDetail = ReceivingDetail::whereIn('po',$arraySingle)->where('warehouse_id',$backStockPallet->warehouse_id);
        
        $purchasingDetailEtin = array_unique($purchasingDetail->pluck('etin')->toArray());
        $responses = [];$i = 0;
        foreach($purchasingDetailEtin as $val){
            $masterProduct = MasterProduct::where('ETIN', $val)->whereNull('parent_ETIN')->first();
            $data = $this->quantityCalculation($id,$val);
            $received = $backStockPallet->BackstockPalletItem->where('ETIN', $val)->sum('quantity');
            $missing = $data['quantity'] - $received;
            if ($missing > 0) {
                $response['upc'] = $masterProduct->upc;
                $response['detail'] = 'Missing';
                $response['name'] = $masterProduct['product_listing_name'];
                $response['expected'] = $data['quantity'];
                $response['received'] = $received;
                $responses[$i++] = $response;
            }
        }
        return response(["error" => false, 'data' => $responses, 'message' => 'Data Found'], 200);
    }

    
    public function  quantityCalculation($id,$ETIN){
        $backStockPallet = BackStockPallet::find($id);
        $query = ReceivingDetail::where('etin',$ETIN)->where('qty_remaining','>',0)->where('warehouse_id',$backStockPallet->warehouse_id);
        $data['quantity'] = $query->sum('qty_remaining');
        return $data;
    }

    public function AddItemToTransferedPallet(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $validator = Validator::make($request->all(), [
            'upc' => 'required',
            'qty' => 'required',
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        $id = $request->id;
        $BPallet = BackStockPallet::find($id);
        if(!$BPallet){
            return response(["error" => true, 'message' => 'Pallet not found'], 404);   
        }

        $location = MasterShelf::where('address', $BPallet->address)->first();

        $upc = $request->upc;
        $qty = $request->qty;
        $masterProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if (!$masterProduct) {
            return response(["error" => true, 'message' => 'Product not found for UPC: ' . $request->upc], 404);
        } else if (!$masterProduct->ETIN) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 404);
        }
        $child_product = NULL;
        if($masterProduct->parent_ETIN != ''){
            $child_product = $masterProduct;
            $parents = [];
            $parent_ETIN = explode(',',$masterProduct->parent_ETIN);
            if(count($parent_ETIN) > 1){
                if($parent_ETIN){
                    foreach($parent_ETIN as $row_parent){
                        $row_pro = MasterProduct::where('ETIN',$row_parent)->first();
                        if($row_pro){
                            $parents[] = $row_pro;
                        }
                    }
                }
                return response(['error' => false, 'message' => 'Please select products', 'parents' => $parents], 200);        
            }else if(count($parent_ETIN) == 1){
                $masterProduct = MasterProduct::where('ETIN',$parent_ETIN[0])->first();
            }
            
        }

        if(!isset($masterProduct->ETIN)) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 404);
        }

        $checkPalletItem = BackStockPalletItem::where('backstock_pallet_id',$id)->where('ETIN',$masterProduct->ETIN)->first();
        if($checkPalletItem){
            $checkPalletItem->quantity = $checkPalletItem->quantity + $qty;
            $checkPalletItem->save();
        }else{
            $checkPalletItem = new BackStockPalletItem;
            $checkPalletItem->ETIN = $masterProduct->ETIN;
            $checkPalletItem->backstock_pallet_id = $id;
            $checkPalletItem->quantity =  $qty;
            $checkPalletItem->save();
        }

        $check_if_address_is_empty = MasterShelf::where('address', $BPallet->address)->where('location_type_id',2)->whereNull('ETIN')->first();
        if($check_if_address_is_empty){
            $check_if_address_is_empty->cur_qty = $quantity;
            $check_if_address_is_empty->ETIN = $row_item->ETIN;
            $check_if_address_is_empty->pallet_number = $BPallet->pallet_number;
            $check_if_address_is_empty->save();
            InventoryAdjustmentLog([
                'ETIN' => $row_item->ETIN,
                'location' => $BPallet->address,
                'starting_qty' => 0,
                'ending_qty' => $quantity,
                'total_change' => '+'.$quantity,
                'user' => $user_id,
                'reference' => 'BackStockPallet',
                'reference_value' => 'pallet_id: '.$id,
                'reference_description' => 'Assigning ETIN Qty to location while Backstock Pallet add item directly to backstock location'
            ]);
        }else{
            $check_pallet = MasterShelf::where('address', $BPallet->address)->where('ETIN',$masterProduct->ETIN)->where('location_type_id',2)->first();
            if($check_pallet){
                $starting_qty = $check_pallet->cur_qty;
                $check_pallet->cur_qty = $starting_qty + $qty;
                $check_pallet->pallet_number = $BPallet->pallet_number;
                $check_pallet->save();
                InventoryAdjustmentLog([
                    'ETIN' => $masterProduct->ETIN,
                    'location' => $BPallet->address,
                    'starting_qty' => $starting_qty,
                    'ending_qty' => $check_pallet->cur_qty,
                    'total_change' => '+'.$qty,
                    'user' => $user_id,
                    'reference' => 'BackStockPallet',
                    'reference_value' => 'pallet_id: '.$id,
                    'reference_description' => 'Updating ETIN Qty to location while Backstock Pallet add item directly to backstock location'
                ]);

            }else{
                MasterShelf::create([
                    'aisle_id' => $location->aisle_id,
                    'bay_id' => $location->bay_id,
                    'shelf' => $location->shelf,
                    'slot' => $location->slot,
                    'address' => $location->address,
                    'location_type_id' => $location->location_type_id,
                    'ETIN' => $masterProduct->ETIN,
                    'cur_qty' => $qty,
                    'pallet_number' => $BPallet->pallet_number
                ]);    
                InventoryAdjustmentLog([
                    'ETIN' => $masterProduct->ETIN,
                    'location' => $location->address,
                    'starting_qty' => 0,
                    'ending_qty' => $qty,
                    'total_change' => '+'.$qty,
                    'user' => $user_id,
                    'reference' => 'BackStockPallet',
                    'reference_value' => 'pallet_id: '.$id,
                    'reference_description' => 'Adding ETIN Qty to location while Backstock Pallet add item directly to backstock location'
                ]);    
            }
        }

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Backstock',
            'details' => $masterProduct->ETIN.' has been added to pallet #'.$BPallet->pallet_number.'',
            'type' => 'CWMS'
        ]);


    }
}
 