<?php

namespace App\Http\Controllers\Api;

use App\AisleMaster;
use App\PutAway;
use App\MasterShelf;
use App\MasterShelfLotAndExpiry;
use App\MasterProduct;
use App\ReceivingDetail;
use App\BackStockPallet;
use App\BackStockPalletItem;
use App\PurchasingSummary;
use App\PurchaseOrderExpAndLot;
use App\PurchasingDetail;
use Illuminate\Http\Request;
use App\ExpirationLotManagement;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PutAwayController extends Controller
{
    
    /*
        Method: savePutAway
        Description: Save put away and return Location details
    */
    public function recordPutAway(Request $request, $bol_number) {
        $header = $request->header('Authorization');
        $user_id = ExtractToken($header);

        $validator = Validator::make($request->all(), [
            'upc' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }
        $upc = $request->upc;
        $masterProduct = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if (!$masterProduct) {
            return response(["error" => true, 'message' => 'Put to Side for Manager Review'], 400);
        } else if (!$masterProduct->ETIN) {
            return response(["error" => true, 'message' => 'No ETIN found for UPC: ' . $request->upc], 400);
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
            } else if(count($parent_ETIN) == 1){
                $masterProduct = MasterProduct::where('ETIN',$parent_ETIN[0])->first();
            }       
        }

        $detail = ReceivingDetail::where('etin', $masterProduct->ETIN)->where('bol_number', $bol_number)->first();
        if (!$detail) {
            if(isset($child_product->parent_ETIN) && $child_product->parent_ETIN != ''){
                $masterProduct = MasterProduct::with('kit_products','kit_products.component_product_details')->where(function($q) use($upc){
                    $q->where('upc', $upc);
                    $q->orWhere('gtin',$upc);
                    $q->orWhere('ETIN',$upc);
                })->first();
            
                $detail = ReceivingDetail::where('etin', $masterProduct->ETIN)->where('bol_number', $bol_number)->first();
                if (!$detail) {
                    return response(["error" => true, 'message' => 'Receiving detail not found UPC: ' . $request->upc], 400);    
                }
            }else{
                return response(["error" => true, 'message' => 'Receiving detail not found UPC: ' . $request->upc], 400);
            }
            
        }

        if (($detail->qty_received + $detail->qty_damaged) <= 0) {
            return response(["error" => true, 'message' => 'Item has not received: ' . $request->upc], 400);
        }


        $location = DB::table('master_shelf')
        ->join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
        ->leftjoin('location_type', function($join){
            $join->on('location_type.id','=','master_shelf.location_type_id');
        })
        ->select('master_shelf.*','location_type.id as type_id','location_type.type')
        ->where('master_aisle.warehouse_id', $detail->warehouse_id)
        ->where('master_shelf.location_type_id','!=',7)
        ->where('master_shelf.ETIN', $detail->etin)->get();
        
        if (count($location) == 0) {
            return response(["error" => true, 'message' => 'No location found for ETIN: ' . $detail->etin], 400);
        }
        $res = [];
        foreach($location as $row_location){
            $MSLAEObj = new MasterShelfLotAndExpiry ();
            $MSLAE = $MSLAEObj->GetMasterShelfLotAndExpOfETINAndAddress([
                'ETIN' => $masterProduct->ETIN,
                'address' => $row_location->address
            ]);
            $res[] = [
                'ETIN' => $masterProduct->ETIN,
                'upc' => $masterProduct->upc,
                'location' => $row_location->address,
                'current_qty' => $row_location->cur_qty,
                'max_qty' => $row_location->max_qty,
                'product_name' => $masterProduct->full_product_desc,
                'upc_scanable' => $masterProduct->upc_scanable,
                'gtin_scanable' => $masterProduct->gtin_scanable,
                'unit_upc_scanable' => $masterProduct->unit_upc_scanable,
                'unit_gtin_scanable' => $masterProduct->unit_gtin_scanable,
                'type' => $row_location->type,
                'type_id' => $row_location->type_id,
                'MSLAE' => $MSLAE
            ];
        }


        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Put Away',
            'details' => 'First Screen Scan Item '.$masterProduct->ETIN.' for bol '.$bol_number,
            'type' => 'CWMS',
            'bol_number' => $bol_number
        ]);
        return response(["error" => false, 'data' => $res, 'message' => 'Put Away Recorded successfully '. $detail->etin], 200);
    }

    /*
        Method: getAllPutAwayBySummaryId
        Description: Get All Put Away by Summary Id
    */
    public function getAllPutAwayBySummaryId($bol_number,$user_id) {

        // ->where('user_id',$user_id)
        $results = PutAway::with('product')->where('bol_number', $bol_number)->get();
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $results
        ]);
    }
    
    /*
        Method: checkPutAwayDiscrepency
        Description: Check discrepency for Put Away
    */
    public function checkPutAwayDiscrepency($bol_number) {
        $recvDetails = ReceivingDetail::where('bol_number', $bol_number)->get();
        DeveloperLog([
            'reference' => 'checkPutAwayDiscrepency - Receiving Details',
            'ref_request' => json_encode([
                'bol_number' => $bol_number
            ]),
            'ref_response' => json_encode($recvDetails)
        ]);
        $responses = [];
        if($recvDetails){
            foreach($recvDetails as $row_dec){
                $damaged = $row_dec->qty_damaged;
                // $received = $row_dec->qty_received + $damaged;
                $received = $row_dec->qty_remaining;
                $product  = $row_dec->product;
                $put_away_qty = PutAway::where('etin', $row_dec->etin)->where('bol_number',$bol_number)->whereNull('transfered')->sum('quantity');
                DeveloperLog([
                    'reference' => 'checkPutAwayDiscrepency - Put Away Qty',
                    'ref_request' => json_encode([
                        'bol_number' => $bol_number,
                        'etin' => $row_dec->etin
                    ]),
                    'ref_response' => json_encode($put_away_qty)
                ]);
                $ordered = 0;
                $response = [];
                $add = 0;
                if($put_away_qty == 0){
                    $response['detail'] = 'Missing';
                    $add = 1;
                    if($received == 0){
                        $add = 0;
                    }
                    $put_away_qty = 0;   
                }else{
                    if($put_away_qty > $received){
                        $response['detail'] = 'Over';
                        $add = 1;
                    }elseif($put_away_qty < $received){
                        $response['detail'] = 'Short';
                        $add = 1;
                    }
                }

                $response['name'] = isset($product) ? $product->product_listing_name: NULL;
                $response['upc'] = isset($product) ? $row_dec->product->upc: NULL;
                $response['expected_location'] = '';//$location->address;
                $response['expected'] = abs($received - $put_away_qty);
                $response['received'] = $received;
                if($add == 1){
                    $responses[] = $response;
                }
                
            }
            DeveloperLog([
                'reference' => 'checkPutAwayDiscrepency - Descripency',
                'ref_request' => json_encode([
                    'bol_number' => $bol_number
                ]),
                'ref_response' => json_encode($responses)
            ]);
            $response = ["data" => $responses, 'error' => false, 'message' => 'Discrepency check complete'];
			return response($response, 200);
        }else {
            $response = ["message" => 'Data Receiving for Bol number: '. $bol_number .' not found!', 
            'status' => 400, 'error' => true];
			return response($response, 400);
        }

    }


    /*
        Method: savePutAwayDiscrepency
        Description: Save Put Away
    */
    public function savePutAway(Request $request,$bol_number) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        Log::channel('Inventory')->info('==============================================');
        Log::channel('Inventory')->info('BOL number: '.$bol_number);

        $PutAwayItems = PutAway::select('*',DB::raw('SUM(quantity) as newSum'))->where('bol_number',$bol_number)->whereNull('transfered')->groupBy('etin')->get();
        if($PutAwayItems){
            foreach($PutAwayItems as $row_put){
                $GetReceiving = ReceivingDetail::where('etin', $row_put->etin)->where('bol_number', $bol_number)->first();
                if(!$GetReceiving){
                    return response(["error" => true, 'message' => 'ETIN: ' . $row_put->etin.' Not received yet'], 400);
                }

                if($row_put->newSum > $GetReceiving->qty_remaining){
                    return response(["error" => true, 'message' => 'ETIN: ' . $row_put->etin.' is over required qty'], 400);
                }

                
                if($row_put->lot != ''){
                    $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$row_put->etin)->where('lot',$row_put->lot)->orderBy('exp_date','ASC')->first();
                    
                    if(!$POEL){
                        return response(['error' => true, 'message' => 'Could find lot# '.$row_put->lot.' and etin: ' . $row_put->etin. ' combination in purchase order '], 400);
                    }

                    if(($row_put->quantity) > $POEL->qty){
                        return response(['error' => true, 'message' => $row_put->etin.' does not have enough qty for the lot#  '.$row_put->lot.' to put away'], 400);
                    }
                }
            }
        }

        $PutAwayItemsForLot = PutAway::where('bol_number',$bol_number)->whereNull('transfered')->get();
        if($PutAwayItemsForLot){
            foreach($PutAwayItemsForLot as $rowPAIL){

                $ms = MasterShelf::where('address', $rowPAIL->location)->where('ETIN', $rowPAIL->etin)->first();
                if (!$ms) {
                    return response(['error' => true, 'message' => 'Address not found for ETIN: ' . $rowPAIL->etin], 400);        
                }

                $cur_qty = $ms->cur_qty + $row_put->quantity;
                $max_qty = $ms->max_qty;
                if(($cur_qty > $max_qty) && $ms->location_type_id != 2){
                    return response(['error' => true, 'message' => 'Max Qty Violation for ETIN: ' . $row_put->etin], 400);
                }

                if($rowPAIL->lot != ''){
                    // $total_put_away_qty = PutAway::where('bol_number',$bol_number)->where('etin',$rowPAIL->etin)->where('lot',$rowPAIL->lot)->sum('quantity');
                    $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$rowPAIL->etin)->where('lot',$rowPAIL->lot)->orderBy('exp_date','ASC')->first();
                    $GRLot = new PurchaseOrderExpAndLot();
                    $GetReceivedLot = $GRLot->GetReceivedLot([
                        'ETIN' => $rowPAIL->etin,
                        'lot' => $rowPAIL->lot,
                        'bol_number' => $bol_number
                    ]);
                    if(!$POEL){
                        return response(['error' => true, 'message' => 'Could find lot and etin combination in purchase order ' . $rowPAIL->etin], 400);
                    }

                    if(($rowPAIL->quantity + $GetReceivedLot) > $POEL->qty){
                        return response(['error' => true, 'message' => $rowPAIL->etin.' does not have enough qty for the lot#  '.$rowPAIL->lot.' to put away'], 400);
                    }

                }
            }
        }

        $responses = [];
        

        $PutAwayItems = PutAway::where('bol_number',$bol_number)->whereNull('transfered')->get();
        $putAway = $PutAwayItems;
        if (count($putAway->toArray()) > 0) {
            foreach($putAway as $pa) {

                Log::channel('Inventory')->info('Location: '.$pa->location.' ETIN: '.$pa->etin);
                $MasterShelfFPA = new MasterShelf();
                $MasterShelfFPA->FinnishPutAway($pa,$user_id,$bol_number);
                Log::channel('Inventory')->info('After Current Qty: '.$ms->cur_qty);
                $GetReceiving = ReceivingDetail::where('etin', $pa->etin)->where('bol_number', $bol_number)->first();
                if($GetReceiving){
                    $RCRemainig = $GetReceiving->qty_remaining;
                    $GetReceiving->qty_remaining = $RCRemainig - $pa->quantity;
                    $GetReceiving->save();
                    if($GetReceiving->qty_remaining == 0){
                        PurchasingDetail::where('bol_number',$bol_number)->where('etin',$pa->etin)->update([
                            'status' => 'Received',
                            'reference' => 'Putaway',
                            'reference_date' => date('Y-m-d H:i:s')
                        ]);
                    }
                    
                }
                $pa->transfered = 1;
                $pa->save();

                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Click',
                    'task' => 'Put Away',
                    'details' => 'Item '.$pa->etin.' Has been put away '.$bol_number,
                    'type' => 'CWMS',
                    'bol_number' => $bol_number
                ]);
            }
            
            $Total_receiving_remainnig = ReceivingDetail::where('bol_number', $bol_number)->where('qty_remaining','!=',0)->count();
            $redirect = 0;
            if($Total_receiving_remainnig == 0){
                
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Click',
                    'task' => 'Put Away',
                    'details' => 'Bol '.$bol_number.' Has been Received Completely',
                    'type' => 'CWMS',
                    'bol_number' => $bol_number
                ]);
                $redirect = 1;
            }
            $response = ["message" => 'Data Put Away Successfully', 'error' => false, 'redirect' => $redirect];
            return response($response, 200);
        } else {
            $response = ["message" => 'No data found for Bol: '. $bol_number, 
            'status' => 400, 'error' => true];
			return response($response, 400);
        }
    }

    /*
        Method: deletePutAwayById
        Description: Delete the Put Away by Id
    */
    public function deletePutAwayById(Request $request, $id) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $deleted = PutAway::where('id', $id)->first();
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Put Away',
            'details' => 'Item '.$deleted->etin.' delted from bol '.$deleted->bol_number,
            'type' => 'CWMS',
            'bol_number' => $deleted->bol_number
        ]);

        $deleted->delete();
        return response(["error" => false, 'message' => 'Deleted'], 200);
    }

    /*
        Method: deletePutAwayBySummaryId
        Description: Delete the Put Away by Id
    */
    public function deletePutAwayBySummaryId($id) {
        $deleted = PutAway::where('summary_id', $id)->delete();
        return response(["error" => false, 'message' => 'Deleted. Total Deleted: ' . $deleted], 200);
    }

    /**
     * Method: SCanUpcWithLocation
     * Description: Validate Upc of the provided Bol and UPc and insert it into UPC
     */

    public function SCanUpcWithLocation(Request $request,$bol_number){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $upc = $request->upc;
        $location = $request->location;

        $PD = PurchasingDetail::where('bol_number',$bol_number)->first();
        if(!$PD){
            return response()->json([
                'error' => true,
                'message' => 'BOL # not found'
            ],400);
        }

        $exp_lot = '';
        if($PD->client_id != ''){
            $exp_lot = GetClientExpLotSetting($PD->client_id);
        }

        if($PD->supplier_id != ''){
            $exp_lot = GetSupplierExpLotSetting($PD->supplier_id);
        }



        $master_pro = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if(!$master_pro){
            return response()->json([
                'error' => true,
                'message' => 'UPC not found'
            ],400);
        }
        $child_product = NULL;
        if($master_pro->parent_ETIN != ''){
            $child_product = $master_pro;
            $parents = [];
            $parent_ETIN = explode(',',$master_pro->parent_ETIN);
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
                $master_pro = MasterProduct::where('ETIN',$parent_ETIN[0])->first();
            }       
        }

        $check_in_receiving = ReceivingDetail::where('bol_number',$bol_number)->where('etin',$master_pro->ETIN)->first();
        if(!$check_in_receiving){
            if(isset($child_product->parent_ETIN) && $child_product->parent_ETIN != ''){
                $master_pro = MasterProduct::where(function($q) use($upc){
                    $q->where('upc', $upc);
                    $q->orWhere('gtin',$upc);
                    $q->orWhere('ETIN',$upc);
                })->first();
                $check_in_receiving = ReceivingDetail::where('bol_number',$bol_number)->where('etin',$master_pro->ETIN)->first();
                if(!$check_in_receiving){
                    return response()->json([
                        'error' => true,
                        'message' => 'UPC does not exist in this BOL Number'
                    ],400);    
                }
            }else{
                return response()->json([
                    'error' => true,
                    'message' => 'UPC does not exist in this BOL Number'
                ],400);
            }
        }


        if (($check_in_receiving->qty_received + $check_in_receiving->qty_damaged) <= 0) {
            return response(["error" => true, 'message' => 'Item has not been received: ' . $upc], 400);
        }


        $check_if_master_shelf_exist = MasterShelf::where('address',$location)->where('ETIN',$master_pro->ETIN)->first();
        if(!$check_if_master_shelf_exist){
            return response()->json([
                'error' => true,
                'message' => 'UPC not assigned to this address'
            ],400);
        }

        $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$master_pro->ETIN)->orderBy('exp_date','ASC')->get();
        $newPoEL = [];
        if($POEL){
            foreach($POEL as $rowPOEL){
                $GRLot = new PurchaseOrderExpAndLot();
                $GetReceivedLot = $GRLot->GetReceivedLot([
                    'ETIN' => $rowPOEL->ETIN,
                    'lot' => $rowPOEL->lot,
                    'bol_number' => $bol_number
                ]);
                if($GetReceivedLot < $rowPOEL->qty){
                    $newPoEL[] = $rowPOEL;
                }
            }
        }
        $POEL = $newPoEL;
        
        $expected_qty = $check_in_receiving->qty_received + $check_in_receiving->qty_damaged;
        $total_put_away_qty = PutAway::where('etin',$master_pro->ETIN)->where('bol_number',$bol_number)->sum('quantity');
        if($total_put_away_qty > $expected_qty){
            return response(["error" => true, 'message' => 'You have scanned Too many of '.$master_pro->ETIN], 404);
        }
        // ->where('user_id',$request->user_id)
        $pt_away = PutAway::where('etin',$master_pro->ETIN)->where('bol_number',$bol_number)->where('location',$location)->whereNull('transfered')->first();
        if($pt_away){
            if(count($POEL) == 0){
                $pt_away->quantity = $pt_away->quantity + 1;
                $pt_away->save();
            }
            
        }else{
            $pt_away = PutAway::create([
                'user_id' => $request->user_id,
                'etin' => $master_pro->ETIN,
                'location' => $location,
                'bol_number' => $bol_number,
                'expected_qty' => ($expected_qty),
                'quantity' => count($POEL) == 0 ? 1 : NULL
            ]);
        }

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Put Away',
            'details' => 'Item '.$master_pro->ETIN.' Scanned for bol '.$bol_number,
            'type' => 'CWMS',
            'bol_number' => $bol_number
        ]);

        
        if(count($POEL) > 0 && $exp_lot == 'YES'){
            return response()->json([
                'error' => false,
                'message' => 'Success',
                'POEL' => $POEL,
                'ROW' => $pt_away
            ]);
        }




        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);

        

    }

    public function SelectLot(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $ETIN = $request->ETIN;
        $location = $request->location;
        $lot = $request->lot;
        $exp_date = $request->exp_date;
        $bol_number = $request->bol_number;

        $pt_away = PutAway::where('etin',$ETIN)->where('bol_number',$bol_number)->where('location',$location)->where('lot',$lot)->where('exp_date',$exp_date)->whereNull('transfered')->first();
        $POEL = PurchaseOrderExpAndLot::where('bol_number',$bol_number)->where('ETIN',$ETIN)->where('lot',$lot)->where('exp_date',$exp_date)->orderBy('exp_date','ASC')->first();
        if($pt_away){
            if(($pt_away->quantity + 1) > $POEL->qty){
                return response()->json([
                    'error' => true,
                    'message' => 'Qty can not be greater than required qty'
                ],400);
            }
            $pt_away->quantity = $pt_away->quantity + 1;
            $pt_away->save();
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Select',
                'task' => 'Put Away',
                'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  bol '.$bol_number,
                'type' => 'CWMS',
                'bol_number' => $bol_number
            ]);
            
        }else{
            $pt_away = PutAway::where('etin',$ETIN)->where('bol_number',$bol_number)->where('location',$location)->whereNull('lot')->whereNull('exp_date')->whereNull('transfered')->first();
            if($pt_away){
                if(($pt_away->quantity + 1) > $POEL->qty){
                    return response()->json([
                        'error' => true,
                        'message' => 'Qty can not be greater than required qty'
                    ],400);
                }

                $pt_away->lot = $lot;
                $pt_away->exp_date = $exp_date;
                $pt_away->quantity = $pt_away->quantity + 1;
                $pt_away->save();
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'Put Away',
                    'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  bol '.$bol_number,
                    'type' => 'CWMS',
                    'bol_number' => $bol_number
                ]);
            }else{
                $check_in_receiving = ReceivingDetail::where('bol_number',$bol_number)->where('etin',$ETIN)->first();
                $expected_qty = $check_in_receiving->qty_received + $check_in_receiving->qty_damaged;
                PutAway::create([
                    'user_id' => $user_id,
                    'etin' => $ETIN,
                    'exp_date' => $exp_date,
                    'lot' => $lot,
                    'location' => $location,
                    'quantity' => 1,
                    'bol_number' => $bol_number,
                    'expected_qty' => ($expected_qty)
                ]); 
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'Put Away',
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
        $location = $request->location;
        $lot = $request->lot;
        $exp_date = $request->exp_date;
        $bol_number = $request->bol_number;

        $pt_away = PutAway::find($request->id);
        $ETIN = $pt_away->etin;
        $bol_number = $pt_away->bol_number;

        $pt = PutAway::where('etin',$ETIN)->where('bol_number',$bol_number)->where('location',$location)->where('lot',$lot)->where('exp_date',$exp_date)->whereNull('transfered')->where('id','!=',$request->id)->first();
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
            'task' => 'Put Away',
            'details' => 'Lot '.$lot.' has been has been Changed  '.$ETIN.'  &  bol '.$bol_number,
            'type' => 'CWMS',
            'bol_number' => $bol_number
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Success'
        ],200);
        
    }

    public function GetPutAway($id){
        $pt_away = PutAway::find($id);
        $POEL = PurchaseOrderExpAndLot::where('bol_number',$pt_away->bol_number)->where('ETIN',$pt_away->etin)->orderBy('exp_date','ASC')->get();
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
            'ROW' => $pt_away
        ]);
    }

    public function UpdatePutQty(Request $request){
        $input = $request->all();
      
        $validator = Validator::make($input, [
            '*.quantity' => 'required',
        ],[
            '*.quantity.required' => 'QTY is Required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }
        if($input){
            foreach($input as $row){
                $pt = PutAway::find($row['id']);
                $pt->quantity = $row['quantity'];
                $pt->save();
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }

    public function PutAwayToReady(Request $request,$bol_number){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $PTAWAY = PutAway::where('bol_number',$bol_number)->get();
        if($PTAWAY){
            foreach($PTAWAY as $ROWPTAWAY){
                $transfered = $ROWPTAWAY->transfered;
                if($transfered == 1){
                    $ms = MasterShelf::where('address', $ROWPTAWAY->location)->where('ETIN', $ROWPTAWAY->etin)->first();
                    if($ms){
                        InventoryAdjustmentLog([
                            'ETIN' => $ms->ETIN,
                            'location' => $ms->address,
                            'starting_qty' => $ms->cur_qty,
                            'ending_qty' => ($ms->cur_qty - $ROWPTAWAY->quantity),
                            'total_change' => '-'.$ROWPTAWAY->quantity,
                            'user' => $user_id,
                            'reference' => 'Put Away to ready',
                            'reference_value' => 'bol_number: '.$bol_number,
                            'reference_description' => 'Deducting Qty for ETIN while Putaway to ready: PutAwayToReady'
                        ]);
                        $ms->cur_qty = $ms->cur_qty - $ROWPTAWAY->quantity;
                        $ms->save();
                        
                    }
                }

                $ROWPTAWAY->delete();
            }
        }

        $bsp = BackStockPallet::where('bol_number', $bol_number)->first();
        if($bsp){
            $bsp_items = BackStockPalletItem::where('backstock_pallet_id', $bsp->id)->get();
            if($bsp_items){
                foreach($bsp_items as $row_bsp_items){
                    $bs_ms = MasterShelf::where('address', $row_bsp_items->location)->where('ETIN', $row_bsp_items->ETIN)->first();
                    if($bs_ms){
                        InventoryAdjustmentLog([
                            'ETIN' => $bs_ms->ETIN,
                            'location' => $bs_ms->address,
                            'starting_qty' => $bs_ms->cur_qty,
                            'ending_qty' => ($bs_ms->cur_qty - $row_bsp_items->quantity),
                            'total_change' => '-'.$row_bsp_items->quantity,
                            'user' => $user_id,
                            'reference' => 'Put Away to ready',
                            'reference_value' => 'bol_number: '.$bol_number,
                            'reference_description' => 'Deducting Qty for ETIN while Putaway to ready: PutAwayToReady'
                        ]);

                        $bs_ms->cur_qty = $bs_ms->cur_qty - $row_bsp_items->quantity;
                        $bs_ms->save();
                    }

                    $row_bsp_items->delete();
                }
            }

            $bsp->delete();
        }
        ReceivingDetail::where('bol_number',$bol_number)->delete();
        PurchasingDetail::where('bol_number',$bol_number)->update([
            'status' => 'Ready'
        ]);

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Change',
            'task' => 'Put Away',
            'details' => 'Bol# '.$bol_number.' has been has been reset to Ready State',
            'type' => 'CWMS',
            'bol_number' => $bol_number
        ]);

        


              
        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    }


}
