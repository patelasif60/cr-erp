<?php

namespace App\Http\Controllers\Api;

use App\MasterProduct;
use App\PurchasingDetail;
use App\ReceivingDetail;
use App\BackStockPallet;
use App\PutAway;
use App\MasterShelf;
use App\PurchaseOrderExpAndLot;
use App\Http\Controllers\Controller;
use App\PurchasingSummary;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivingDetailController extends Controller
{

    
    /*
        Method: checkDiscrepency
        Description: Check mismatch in the received products
    */
    public function checkDiscrepency($bol_number) {    
        $details = ReceivingDetail::where('bol_number', $bol_number)->get();
        if (!$details) {
            return response(["error" => true, 'message' => 'No details found for BOl Number: ' . $bol_number], 400);
        }

        $responses = [];
        $etins = [];
        $po = ''; 
        $i = 0;
        
        foreach($details as $datum) {
            if ($po == '') {
                $po = $datum->po;
            }
            array_push($etins, $datum->etin);
            $masterProduct = MasterProduct::where('ETIN', $datum->etin)->whereNull('parent_ETIN')->first();
            $ordered = $datum->qty_ordered;
            $damaged = $datum->qty_damaged;
            $received = $datum->qty_received;
            $missing = $ordered - ($received);

            $add = 0;
            if($received > $ordered){
                $response['detail'] = 'Over';
                $add = 1;
            }elseif($damaged > 0){
                $response['detail'] = 'Damaged';
                $add = 1;
            }elseif($received < $ordered){
                $response['detail'] = 'Short';
                $add = 1;
            }elseif($received == 0){
                $response['detail'] = 'Missing';
                $add = 1;
            }
            $response['bol_number'] = $bol_number;
            $response['upc'] = $masterProduct->upc;
            $response['ETIN'] = $masterProduct->ETIN;
            $response['gtin'] = $masterProduct->gtin;
            $response['expected'] = $ordered;
            $response['received'] = $received;
            $response['damaged'] = $damaged;
            $response['name'] = $masterProduct->product_listing_name;
            $response['id'] = $datum->id;
            $response['damaged_sattaled'] = $datum->damaged_sattaled;
            $response['damaged_sattaled_location'] = $datum->damaged_sattaled_location;
            $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$masterProduct->ETIN)->get();
            $response['POEL'] = $POEL;
            if($add == 1){
                $responses[] = $response;
            }
            
        }
        
        $pds = PurchasingDetail::where('bol_number', $bol_number)->where('asn_bol_shipped_qty','>',0)->get();
        if ($pds) {
            foreach ($pds as $pd) {
                if (!in_array($pd->etin, $etins)) {
                    $masterProduct = MasterProduct::where('ETIN', $pd->etin)->whereNull('parent_ETIN')->first();
                    $response['upc'] = $masterProduct ? $masterProduct->upc : NULL;
                    $response['detail'] = 'Missing';
                    $response['expected'] = $pd->qty_ordered;
                    $response['received'] = 0;
                    $response['damaged'] = 0;
                    $response['name'] = $masterProduct ? $masterProduct['full_product_desc'] : NULL;
                    $response['damaged_sattaled'] = NULL;
                    $response['damaged_sattaled_location'] = NULL;
                    $responses[] = $response; 
                }
            }
        }

        return response(["error" => false, 'data' => $responses, 'message' => 'Data Found'], 200);
    }

    /*
        Method: saveReceiving
        Description: Save the received products
    */
    public function saveReceiving(Request $request, $bol_number) {    
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $data = $request->all();
        $validator = Validator::make($data, [
            'upc' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        $bol_info = PurchasingDetail::where('bol_number',$bol_number)->first();
        if (!$bol_info) {
            return response(["error" => true, 'message' => 'No Bol number Found: ' . $bol_number], 400);
        }

        $upc = $data['upc'];
        if ($upc) {
            
            $masterProduct = MasterProduct::where(function($q) use($upc){
                $q->where('upc', $upc);
                $q->orWhere('gtin',$upc);
                $q->orWhere('ETIN',$upc);
            })->first();
            if (!$masterProduct) {
                return response(["error" => true, 'message' => 'Put to side for MGR review'], 404);
            } else if (!$masterProduct->ETIN) {
                return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $upc], 404);
            }

            if($masterProduct->parent_ETIN != ''){
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
                } else if(count($parent_ETIN) == 1){
                    $masterProduct = MasterProduct::where('ETIN',$parent_ETIN[0])->first();
                }  
            }

            $detail = PurchasingDetail::where('etin', $masterProduct->ETIN)->where('bol_number', $bol_number)->first();
            if(!$detail){
                return response(["error" => true, 'message' => 'Product not found in this purchase order'], 404);
            }
            $rd = ReceivingDetail::where('etin', $masterProduct->ETIN)->where('bol_number', $bol_number)->first();
            if ($rd) {
                $rd->qty_received = $rd->qty_received + 1;
                $rd->qty_remaining = $rd->qty_remaining + 1;
                $rd->recount = 0;
                if($rd->qty_received > $detail->asn_bol_shipped_qty){
                    return response(["error" => true, 'message' => 'You have scanned Too many of '.$masterProduct->ETIN], 404);
                }
                $rd->save();
            } else {
                if($detail->asn_bol_shipped_qty <= 0){
                    return response(["error" => true, 'message' => 'ASN/BOL QTY should be greater than Zero'], 404);
                }
                $rcv = ReceivingDetail::create([
                    'bol_number' => $bol_number,
                    'etin' => $masterProduct->ETIN,
                    'qty_ordered' => $detail && $detail->asn_bol_shipped_qty ? $detail->asn_bol_shipped_qty : 0,
                    'qty_received' => 1,
                    'qty_damaged' => 0,
                    'qty_missing' => 0,
                    'qty_remaining' => 1,
                    'warehouse_id' => $bol_info->warehouse_id
                ]);
            }

            
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Scan',
                'task' => 'Receiving',
                'details' => 'Item '.$masterProduct->ETIN.' scanned for bol '.$bol_number,
                'type' => 'CWMS',
                'bol_number' => $bol_number
            ]);
        } else {
            return response(["error" => true, 'message' => 'ETIN is mandatory'], 400);
        }

        return response(['error' => false, 'message' => 'Data saved in Receiving Details.'], 200);
    }

    /*
        Method: updateReceiving
        Description: Update the quantities products
    */
    public function updateReceiving(Request $request) {    
        $data = $request->all();
        $validator = Validator::make($data, [
            '*.id' => 'required',
            '*.qty_received' => 'nullable|required',
            '*.qty_damaged' => 'required'
        ],[
            '*.qty_received.required' => 'Sellable QTY is Required',
            '*.qty_damaged.required' => 'Damaged QTY is required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }


        

        foreach ($data as $datum) {
            $detail = ReceivingDetail::where('id', $datum['id'])->first();
            if ($detail) {
                $detail->qty_received = $datum['qty_received'];
                $detail->qty_remaining = ($datum['qty_received'] + $datum['qty_damaged']);
                $detail->qty_damaged = $datum['qty_damaged'];
                if ($detail->qty_ordered > ($detail->qty_received + $detail->qty_damaged)) {
                    $detail->qty_missing = $detail->qty_ordered - ($detail->qty_received + $detail->qty_damaged); 
                } else {
                    $detail->qty_missing = 0;
                }
                $detail->recount = 0;
                $detail->save();
            } else {
                return response(['error' => true, 'message' => 'Invalid Id: ' . $datum['id']], 400);
            }
        }
        return response(['error' => false, 'message' => 'Data updated in Receiving Details.'], 200);
    }

    /*
        Method: deleteReceivingDetailsBySummaryId
        Description: Delete the Received Details
    */
    public function deleteReceivingDetailsBySummaryId($summaryId) {
        $deleted = ReceivingDetail::where('summary_id', $summaryId)->delete();
        return response(["error" => false, 'message' => 'Deleted. Total Deleted'], 200);
    }

    /*
        Method: getAllReceiving
        Description: Return all received products
    */
    public function getAllReceiving() {
        $result = ReceivingDetail::get();
    	if (count($result->toArray()) > 0) {
    		return response(["data" => $result, 'message' => 'Data found successfully', 'status' => 200, 'error' => false]);
    	}
    	else{
    		return response(["message" => 'Data not found!', 'status' => 400, 'error' => true]);
    	}
    }

    /*
        Method: getAllReceivingBySupplierId
        Description: Return all received products filtered by supplier
    */
    public function getAllReceivingBySupplierId($supplierId) {
        $result = ReceivingDetail::where('supplier_id', $supplierId)->get();
    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200, 'error' => false];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400, 'error' => true];
			return response($response, 400);
    	}
    }

    /*
        Method: getAllReceivingBySupplierIdAndPO
        Description: Return all received products filtered by supplier and PO
    */
    public function getAllReceivingByPurchaseSummary($bol_number) {
        $results = ReceivingDetail::where('bol_number', $bol_number)->orderBy('updated_at','DESC')->get();
    	if (count($results->toArray()) > 0) {
            
            $responses = [];
            foreach ($results as $result) {
                
                $mp = MasterProduct::where('ETIN', $result->etin)->whereNull('parent_ETIN')->first();
                $response['id'] = $result->id;
                $response['supplier_id'] = $result->supplier_id;
                $response['po'] = $result->po;
                $response['etin'] = $result->etin;
                $response['qty_ordered'] = $result->qty_ordered;
                $response['qty_received'] = $result->qty_received;
                $response['qty_damaged'] = $result->qty_damaged;
                $response['qty_missing'] = $result->qty_missing;
                $response['summary_id'] = $result->summary_id;
                $response['upc'] = $mp->upc;
                $response['product_name'] = $mp->product_listing_name;
                $response['created_at'] = $mp->created_at;
                $response['updated_at'] = $mp->updated_at;
                $response['recount'] = $result->recount;
                $response['upc_scanable'] = $mp->upc_scanable;
                $response['gtin_scanable'] = $mp->gtin_scanable;
                $response['unit_upc_scanable'] = $mp->unit_upc_scanable;
                $response['unit_gtin_scanable'] = $mp->unit_gtin_scanable;
                
                array_push($responses, $response);
            }
    		$response = ['error' => false, "data" => $responses, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ['error' => true, "message" => 'Data not found!', 'status' => 400];
			return response($response, 400);
    	}
    }

    /*
        Method: deleteReceivingDetailsById
        Description: Delete the Received Details
    */
    public function deleteReceivingDetailsById(Request $request,$id) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $deleted = ReceivingDetail::where('id', $id)->first();
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Receiving',
            'details' => 'Item '.$deleted->etin.' delted from bol '.$deleted->bol_number,
            'type' => 'CWMS',
            'bol_number' => $deleted->bol_number
        ]);
        $deleted->delete();
        return response(["error" => false, 'message' => 'Deleted. Total Deleted'], 200);
    }

    public function updaterecount($id){
        $RD = ReceivingDetail::find($id);
        $RD->recount = 1;
        $RD->qty_received = 0;
        $RD->qty_damaged = 0;
        $RD->qty_missing = 0;
        $RD->qty_remaining = $RD->qty_ordered;
        $RD->save();
        return response(["error" => false, 'message' => 'Success'], 200);
    }

    public function completeReceivingDetails(Request $request,$bol_number) {
            $header = $request->header('Authorization');
            $user_id = ExtractToken($header);
            $pds = PurchasingDetail::where('bol_number', $bol_number)->get();
            if (!$pds || count($pds) <= 0) {
                return response(["error" => true, 'message' => 'No Purchase detail found for BOL number: ' . $bol_number], 404);
            }

            $rds = ReceivingDetail::where('bol_number', $bol_number)->select('*')->get();
            
            if($rds){
                foreach($rds as $row_data){
                    $total_qty =    $row_data->qty_received + $row_data->qty_damaged;
                    if($total_qty > $row_data->qty_ordered){
                        return response(['error' => true, 'message' => 'Qty can not be greater than ordered qty for: '.$row_data->etin], 404);
                    }
                }
            }
            // return response($rds);
            if (!$rds || count($rds) <= 0) {
                return response(["error" => true, 'message' => 'No Receiving detail found for BOL Number: ' . $bol_number], 404);
            }

            foreach ($pds as $pd) {
                $found = false;
                foreach($rds as $etin) {
                    if ($etin->etin == $pd->etin) {
                        // if($etin->qty_damaged > 0){
                        //     AddProductToQuorantineLocation($etin->etin,$etin->qty_damaged,$etin->warehouse_id);
                        // }
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    ReceivingDetail::create([
                        'etin' => $pd->etin,
                        'qty_ordered' => $pd->asn_bol_shipped_qty,
                        'qty_received' => 0,
                        'qty_damaged' => 0,
                        'qty_missing' => $pd->asn_bol_shipped_qty,
                        'qty_remaining' => $pd->asn_bol_shipped_qty,
                        'warehouse_id' => $pd->warehouse_id,
                        'bol_number' => $pd->bol_number
                    ]);
                }

                $pd->status = "Put Away";
                $pd->save();
                
                PurchasingSummary::where('order',$pd->po)->update([
                    'po_status' => 'Received'
                ]);

                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Click',
                    'task' => 'Receiving',
                    'details' => $pd->bol_number.' has been received',
                    'type' => 'CWMS',
                    'bol_number' => $pd->bol_number
                ]);
            }
            
            
            return response(["error" => false, 'message' => 'Receiving Completed.'], 200);
        
    }

    public function getallreceivedItems($bol_number){
        $pds = PurchasingDetail::where('bol_number', $bol_number)->first();
        if(!$pds){
            return response(["error" => true, 'message' => 'No Purchase detail found for BOL number: ' . $bol_number], 400);
        }

        
        $BackStock = BackStockPallet::leftJoin('backstock_pallet_items',function($join){
            $join->on('backstock_pallet_items.backstock_pallet_id','=','backstock_pallet.id');
        })->leftJoin('master_product',function($join){
            $join->on('master_product.ETIN','=','backstock_pallet_items.ETIN');
        })->select('master_product.upc','master_product.product_listing_name as product_name','backstock_pallet_items.quantity as qty','backstock_pallet.address as location','master_product.gtin_scanable','master_product.unit_gtin_scanable','master_product.unit_upc_scanable','master_product.unit_gtin_scanable')->where('bol_number', $bol_number)->whereNotNull('address')->get();
    
        $PutAway = PutAway::leftJoin('master_product',function($join){
            $join->on('master_product.ETIN','=','put_away.etin');
        })->select('master_product.upc','master_product.product_listing_name as product_name','put_away.quantity as qty','put_away.location','master_product.gtin_scanable','master_product.gtin_scanable','master_product.unit_upc_scanable','master_product.unit_gtin_scanable','put_away.lot')->where('bol_number', $bol_number)->where('transfered',1)->orderBy('put_away.id','DESC')->get();
        
        $received = ReceivingDetail::where('bol_number', $bol_number)->leftJoin('master_product',function($join){
            $join->on('master_product.ETIN','=','receiving_details.ETIN');
        })->select('receiving_details.*','master_product.upc','master_product.product_listing_name as product_name','master_product.gtin_scanable','master_product.unit_gtin_scanable','master_product.unit_upc_scanable','master_product.unit_gtin_scanable')->get();

        return response(["error" => false, 'message' => 'Success', 'BackStock' => $BackStock, 'PutAway' => $PutAway,'received' => $received], 200);
    }

    public function SattleDamagedQty(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $products = $request->all();
        
        $grouped_qty = _group_by($products,'ETIN');

        if($grouped_qty){
            foreach($grouped_qty as $row_g_q){
                $total_added_qty = 0;
                $first_pro = $row_g_q[0];
                foreach($row_g_q as $row_pro){
                    $total_added_qty = $total_added_qty + $row_pro['added_qty'];
                }

                if($total_added_qty != $first_pro['damaged']){
                    return response(["error" => true, 'message' => 'Please add Correct Damaged Qty for ETIN: '.$first_pro['ETIN']], 400);
                }
            }
        }

        foreach($products as $row_pro){
            if($row_pro['location'] == ''){
                return response(["error" => true, 'message' => 'Location can not be empty'], 400);
            }
            $quorantine_location = MasterShelf::where('address',$row_pro['location'])->where('location_type_id',7)->first();
            if(!$quorantine_location){
                return response(["error" => true, 'message' => 'Location '.$row_pro['location'].' is not Quarantine location'], 400);
            }

            if($row_pro['lot'] != '' && $row_pro['POEL']){
                foreach($row_pro['POEL'] as $rowPOEL){
                    if($row_pro['lot'] === $rowPOEL['lot'] && $row_pro['added_qty'] > $rowPOEL['qty']){
                        return response(["error" => true, 'message' => 'You can not add more than '.$rowPOEL['qty'].' on the lot #:'.$rowPOEL['lot']], 400);
                    }
                }
            }
        }
        foreach($products as $row_pro){
            $MasterShelf = new MasterShelf;
            $MasterShelf->AddProductToQuorantineLocation($row_pro['ETIN'],$row_pro['added_qty'],$row_pro['location'],$user_id,$row_pro);
            $RD = ReceivingDetail::find($row_pro['id']);
            $expected_qty = $RD->qty_received + $RD->qty_damaged;
            $RD->damaged_sattaled = $RD->damaged_sattaled + $row_pro['added_qty'];
            $RD->damaged_sattaled_location = $row_pro['location'];
            $RD->qty_remaining = $RD->qty_remaining - $row_pro['added_qty'];
            $RD->save();

            

            $pt_away = PutAway::create([
                'user_id' => $user_id,
                'etin' => $row_pro['ETIN'],
                'location' => $row_pro['location'],
                'bol_number' => $RD->bol_number,
                'expected_qty' => ($expected_qty),
                'quantity' => $row_pro['added_qty'],
                'lot' => $row_pro['lot'],
                'exp_date' => $row_pro['exp_date'],
                'transfered' => 1
            ]);

        }
        return response(["error" => false, 'message' => 'Success'], 200);
    }
}
