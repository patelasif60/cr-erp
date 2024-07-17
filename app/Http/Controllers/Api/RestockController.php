<?php

namespace App\Http\Controllers\Api;

use App\MasterProduct;
use App\Restock;
use App\RestockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\MasterShelf;
use App\ProductRestock;
use App\PurchasingDetail;
use App\PurchasingSummary;
use App\ReceivingDetail;
use App\AisleMaster;
use App\OrderDetail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Restock\RestockResource;
use App\MasterShelfLotAndExpiry;

class RestockController extends Controller
{
    


    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->warehouse_id == "") $request->warehouse_id = 1;
        $result = Restock::whereNull('status')->where('warehouse_id',$request->warehouse_id)->get();
        $data = RestockResource::collection($result);
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $data,
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
        
        $Restock = new Restock();
        
        $Restock->pallet_number	= time();
        $Restock->warehouse_id = $request->warehouse_id;
        $Restock->save();

        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $Restock
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
        $result =  Restock::find($id);
        $data = new RestockResource($result);
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
        $Restock = Restock::find($id)->delete();
        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }

    public function add_item(Request $request){
        $upc = $request->upc;
        $backstock_location = $request->backstock_location;
        $pick_location = $request->pick_location;
        $lot = $request->lot;
        $filteredItems = $request->filteredItems;

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

        if($filteredItems){
            $ProductRestock  = ProductRestock::find($filteredItems[0]['id']);
            if(!$ProductRestock){
                return response(["error" => true, 'message' => 'Something went wrong'], 404); 
            }
        }

        $RestockItem = RestockItem::where('ETIN',$masterProduct->ETIN)->where('from_location',$backstock_location)->where('to_location',$pick_location)->where('lot',$lot)->where('tranfered',0)->first();
        if($RestockItem){
            $total_qty = RestockItem::where('ETIN',$masterProduct->ETIN)->where('from_location',$backstock_location)->where('to_location',$pick_location)->where('tranfered',0)->sum('quantity');
            if($total_qty > $ProductRestock->qty_to_restock){
                return response(["error" => true, 'message' => 'So many qty scanned'], 404); 
            }

            $RestockItem->quantity = $RestockItem->quantity + 1;
            $RestockItem->save();
        }else{
            $RestockItem = new RestockItem();
            $RestockItem->ETIN = $masterProduct->ETIN;
            $RestockItem->quantity = 1;
            $RestockItem->from_location = $backstock_location;
            $RestockItem->to_location = $pick_location;
            $RestockItem->lot = $lot;
            $RestockItem->upc = $masterProduct->upc;
            $RestockItem->product_listing_name = $masterProduct->product_listing_name;
            $RestockItem->user_id = $request->user_id;
            $RestockItem->warehouse_id = $request->warehouse_id;
            $RestockItem->tem_ref_id = $ProductRestock->id;
            $RestockItem->ref_resp = json_encode($request->all());
            $RestockItem->save();
        }


        $ProductRestock->scanned_items = $ProductRestock->scanned_items + 1;
        $ProductRestock->save();

        return response()->json([
            'error' => false,
            'message' => 'Success',
        ]);
    }

    public function get_added_items(Request $request){
        $result = RestockItem::where('user_id',$request->user_id)->where('warehouse_id',$request->warehouse_id)->where('tranfered',0)->get();
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $result
        ]);
    }

    public function editItem(Request $request){
        $data = $request->all();
        $groupBy = _group_by($data,'tem_ref_id');
        foreach ($data as $val) {
            $detail = RestockItem::where('id', $val['id'])->first();
            if ($detail) {
                $detail->quantity = $val['quantity'];
                $detail->save();
            } 
        }

        if($groupBy){
            foreach($groupBy as $keyGRP => $row_GRP){
                $qty = 0;
                if($row_GRP){
                    foreach($row_GRP as $ROWGRP){
                        $qty = $qty + $ROWGRP['quantity'];
                    }
                }

                $ProductRestock  = ProductRestock::find($keyGRP);
                $ProductRestock->scanned_items = $qty;
                $ProductRestock->save();
            }
        }
        return response(['errors' => false, 'message' => 'Data updated in restock items.'], 200);
    }

    public function CompleteRestock(Request $request){
        $data = $request->all();
        foreach ($data as $val) {
            if($val['lot'] != ''){
                $MSLEObject = new MasterShelfLotAndExpiry;
                $lot_qty = $MSLEObject->GetMasterShelfLotAndExp([
                    'ETIN' => $val['ETIN'],
                    'address' => $val['from_location'],
                    'lot' => $val['lot']
                ]);
                if(!$lot_qty){
                    return response(["error" => true, 'message' => 'Something went wrong with ETIN and lot' . $val['ETIN']], 404);
                }

                if($val['quantity'] > $lot_qty['qty']){
                    return response(["error" => true, 'message' => 'Lot # ' . $val['lot'] .' does not have enough qty'], 404);
                }
            }

        }

        $groupBy = _group_by($data,'tem_ref_id');
        if($groupBy){
            foreach($groupBy as $keyGRP => $row_GRP){
                $qty = 0;
                if($row_GRP){
                    foreach($row_GRP as $ROWGRP){
                        $qty = $qty + $ROWGRP['quantity'];
                    }
                }

                $ProductRestock  = ProductRestock::find($keyGRP);
                if(!$ProductRestock){
                    return response(["error" => true, 'message' => 'Something went wrong'], 404); 
                }

                
                if($ProductRestock->qty_to_restock < $qty){
                    return response(["error" => true, 'message' => 'Qty for ETIN: '.$ProductRestock->ETIN.' and Backstock location: '.$ProductRestock->backstock_location.' can not be greater than required Qty'], 404); 
                }

                $MasterShelfObject = new MasterShelf;
                $MSQTY = $MasterShelfObject->GetTheMasterShelfQty([
                    'ETIN' => $val['ETIN'],
                    'address' => $val['from_location']
                ]);
                if(!$MSQTY){
                    return response(["error" => true, 'message' => 'Something went wrong with the backstock location'], 404); 
                }

                if($MSQTY['cur_qty'] < $qty){
                    return response(["error" => true, 'message' => 'Backstock location: '.$ProductRestock->backstock_location.' does not have enough qty for ETIN: '.$ProductRestock->ETIN], 404); 
                }
                
            }
        }
        foreach ($data as $val) {
            $detail = RestockItem::where('id', $val['id'])->first();
            if ($detail) {
                $MasterShelfObject = new MasterShelf;
                $RestockQty = $MasterShelfObject->RestockQty($val);

                $ProductRestock  = ProductRestock::find($val['tem_ref_id']);
                if($ProductRestock){
                    $ProductRestock->qty_to_restock = $ProductRestock->qty_to_restock - $val['quantity'];
                    $ProductRestock->save();
                }

                if($ProductRestock->qty_to_restock == 0){
                    $ProductRestock->delete();
                }

                $detail->tranfered = 1;
                $detail->save();

            } 
        }
        return response(['errors' => false, 'message' => 'Data updated in restock items.'], 200);
    }

    
    public function deleteItem(Request $request,$id)
    {
        $RI = RestockItem::find($id);
        if($RI){
            $PR = ProductRestock::find($RI->tem_ref_id);
            if($PR){
                $PR->scanned_items = NULL;
                $PR->save();
            }
            $RI->delete();
        }
        
        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }

    public function GetPickLocationItemsReport(Request $request){
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        $warehouse_id = $request->warehouse_id;
        
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $result_obj  = ProductRestock::leftJoin('users',function($q){
            $q->on('users.id','=','product_restocks.user_id');
        })->select('product_restocks.*','users.name as user_name')->where('warehouse',$warehouse_id);
        if(isset($request->sortBy['id'])){
			if($request->sortBy['desc']){
				$sort = 'DESC';
			}else{
				$sort = 'ASC';
			}
			$result_obj->orderBy($request->sortBy['id'],$sort);
		}

        if(isset($request->text) && $request->text != ''){
			$search = $request->text;
			$result_obj->where(function($q) use($search){
				$q->where('users.name','LIKE','%'.$search.'%');
				$q->OrWhere('product_restocks.ETIN','LIKE','%'.$search.'%');
				$q->OrWhere('product_restocks.product_listing_name','LIKE','%'.$search.'%');
				$q->OrWhere('product_restocks.client_supplier','LIKE','%'.$search.'%');
                $q->OrWhere('product_restocks.upc','LIKE','%'.$search.'%');
                $q->OrWhere('product_restocks.pick_location','LIKE','%'.$search.'%');
                $q->OrWhere('product_restocks.backstock_location','LIKE','%'.$search.'%');
                $q->OrWhere('product_restocks.pallet_id','LIKE','%'.$search.'%');
                $q->OrWhere('product_restocks.priority_name','LIKE','%'.$search.'%');
			});
		}
        $all_obj = $result_obj;
        $all_result = $all_obj->get();
        $total_records = count($all_result);

        if($limit != -1){
            $result_obj->orderBy('priority','ASC');
            $result_obj->skip($offset)->take($limit);
            $products = $result_obj->get();
        }else{
            $products = $all_result;
        }
        


        
        $response = ["data" => $products,'total_records' => $total_records, 'error' => false, 'message' => 'Success'];
		return response($response, 200);
    }

    public function AssignProductsToRestock(Request $request){
        $selectedProducts = $request->selectedProducts;
        if($selectedProducts){
            foreach($selectedProducts as $RowSP){
                $ProductRestock = ProductRestock::find($RowSP);
                $ProductRestock->user_id = $request->user_id;
                $ProductRestock->save();
            }
        }
        $response = ['error' => false, 'message' => 'Success'];
		return response($response, 200);
    }

    public function GetItemsForRestock(Request $request){
        $user_id = $request->user_id;
        $warehouse_id = $request->warehouse_id;
        $result = ProductRestock::where('warehouse',$warehouse_id)->where('user_id',$user_id)->whereColumn('scanned_items', '!=', 'qty_to_restock')->orderBy('priority','ASC')->get();
        if($result){
            foreach($result as $row){
                $lot_exp = MasterShelfLotAndExpiry::where('ETIN',$row->ETIN)->where('address',$row->backstock_location)->orderBy('exp_date','ASC')->get();
                $row->POEL = $lot_exp;
            }
        }
        $response = ['error' => false, 'message' => 'Success','data' => $result];
		return response($response, 200);
    }



}
 