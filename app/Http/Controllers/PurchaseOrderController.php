<?php

namespace App\Http\Controllers;

use App\Client;
use App\Exports\PurchaseOrderExport;
use DataTables; 
use App\Supplier;
use App\MasterShelf;
use App\PurchaseOrderExpAndLot;
use App\PurchasingDetail;
use App\PurchasingSummary;
use App\ReceivingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseOrderController extends Controller
{
    
    public function create($id = null, $type = null) {
        $row = null;
        $c_row = null;
        if ($type === 'supplier') $row = Supplier::find($id);
        else if ($type === 'client') $c_row = Client::find($id);
        $warehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        return view('purchase_order.create',compact('row','warehouses','c_row'));
    }

    public function edit($id = null, $sumId = null, $type = null) {
        $row = null;
        $c_row = null;
        if ($type === 'supplier') $row = Supplier::find($id);
        else if ($type === 'client') $c_row = Client::find($id);
        $ps = PurchasingSummary::find($sumId);
        $warehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        return view('purchase_order.edit',compact('row','warehouses','ps', 'c_row'));
    }

    public function editAsnBol($id = null, $sumId = null, $type = null) {
        $row = null;
        $c_row = null;
        if ($type === 'supplier') $row = Supplier::find($id);
        else if ($type === 'client') $c_row = Client::find($id);
        $ps = PurchasingSummary::find($sumId);
        $purchase_details = PurchasingDetail::where('po', $ps->order)->first();
        $warehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        return view('purchase_order.editasnbol',compact('row','warehouses','ps', 'c_row','purchase_details'));
    }

    public function PurchaseOrderProducts(Request $request) {

            $responses = [];

            if ($request->warehouse_id) {
                
                $type = !isset($request->supplier) ? 'client' : 'supplier';
                $supplier = isset($request->supplier) ? $request->supplier : $request->client;
                $supplier = htmlspecialchars_decode($supplier, ENT_QUOTES);

                $data = DB::table('master_product')->where('is_approve', 1)->whereNull('parent_ETIN')
                    ->where('client_supplier_id', $supplier)->where('supplier_type', $type)->get();
                if ($data && count($data) > 0) {
                    
                    foreach ($data as $datum) {

                        if (isset($datum->item_form_description) && str_contains(strtolower($datum->item_form_description), 'kit')) {
                            continue;
                        }

                        $itemInStock = DB::table('master_shelf')
                            ->join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                            ->where('master_aisle.warehouse_id', $request->warehouse_id)
                            ->whereIn('master_shelf.location_type_id', [1, 2])
                            ->where('master_shelf.ETIN', $datum->ETIN)->select('master_shelf.*')->get();

                        $onHandQty = 0;
                        if ($itemInStock && count($itemInStock) > 0) {                            
                            foreach($itemInStock as $its) {
                                $onHandQty += $its->cur_qty;
                            }
                        }

                        $rds = PurchasingDetail::where('warehouse_id', $request->warehouse_id)
                                        ->where('etin', $datum->ETIN)
                                        ->whereNull('asn_bol_shipped_qty')
                                        ->get();

                        $onOrderQty = 0;
                        if ($rds && count($rds) > 0) {
                            foreach($rds as $rs) {
                                $onOrderQty += $rs->qty_ordered;
                            }
                        }

                        $suggestedOrderQty = $datum->week_worth_qty - ($onHandQty + $onOrderQty);
                        $suggestedOrderQty = $suggestedOrderQty < 0 ? 0 : $suggestedOrderQty;
                        
                        $response['ETIN'] = $datum->ETIN;
                        $response['supplier_product_number'] = $datum->supplier_product_number;
                        $response['product_listing_name'] = $datum->product_listing_name;
                        $response['status'] = $datum->status;
                        $response['on_hand_qty'] = $onHandQty;
                        $response['on_order_qty'] = $onOrderQty;
                        $response['week_worth_qty'] = $datum->week_worth_qty;
                        $response['min_order_qty'] = $datum->min_order_qty;
                        $response['suggested_order_qty'] = $suggestedOrderQty;
                        $response['order_qty'] = $suggestedOrderQty;
                        $response['weight'] = $datum->weight;
                        $response['product_availability'] = isset($data->supplier_status) && $data->supplier_status === '1' ? 'Yes' : 'No';
                        $response['lead_time'] = $datum->lead_time;
                        if (str_contains(strtolower($datum->product_temperature), 'dry')) {
                            $response['temp'] = 'dry';
                        } else if (str_contains(strtolower($datum->product_temperature), 'frozen')) {
                            $response['temp'] = 'frozen';
                        } else if (str_contains(strtolower($datum->product_temperature), 'refrigerated')
                                    || str_contains(strtolower($datum->product_temperature), 'beverages')) {
                            $response['temp'] = 'refrigerated';
                        } else {
                            $response['temp'] = 'na';
                        }

                        array_push($responses, $response);
                    }
                }
            }

            // return response($data);

            return Datatables::of($responses)->addIndexColumn()->make(true);

        
    }

    public function SavedPurchaseOrderProducts(Request $request) {

        $responses = [];
        if ($request->warehouse_id && $request->po) {
            
            $pd = PurchasingDetail::where('po', $request->po)->get();
            if ($pd) {

                foreach($pd as $datum) {

                    $type = !isset($request->supplier) ? 'client' : 'supplier';
                    $supplier = isset($request->supplier) ? $request->supplier : $request->client;
                    $supplier = htmlspecialchars_decode($supplier, ENT_QUOTES);

                    $data = DB::table('master_product')->where('is_approve', 1)->whereNull('parent_ETIN')
                    ->where('client_supplier_id', $supplier)->where('supplier_type', $type)
                    ->where('ETIN', $datum->etin)->first();

                    $itemInStock = DB::table('master_shelf')
                                    ->join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                    ->where('master_aisle.warehouse_id', $request->warehouse_id)
                                    ->where('master_shelf.ETIN', $datum->etin)->select('master_shelf.*')->get();

                        $onHandQty = 0;
                        if ($itemInStock && count($itemInStock) > 0) {                            
                            foreach($itemInStock as $its) {
                                $onHandQty += $its->cur_qty;
                            }
                        }

                        $rds = ReceivingDetail::where('warehouse_id', $request->warehouse_id)->where('etin', $datum->ETIN)->get();

                        $onOrderQty = 0;
                        if ($rds && count($rds) > 0) {
                            foreach($rds as $rs) {
                                $onOrderQty += $rs->qty_remaining;
                            }
                        }

                        $suggestedOrderQty = $data ? $data->week_worth_qty - ($onHandQty + $onOrderQty) : "-";
                        $suggestedOrderQty = $suggestedOrderQty < 0 ? 0 : $suggestedOrderQty;

                        $response['ETIN'] = $datum->etin;
                        $response['supplier_product_number'] =  $data ? $data->supplier_product_number : "-";
                        $response['product_listing_name'] =  $data ? $data->product_listing_name : "-";
                        $response['status'] =  $data ? $data->status : "-";
                        $response['on_hand_qty'] = $onHandQty;
                        $response['on_order_qty'] = $onOrderQty;
                        $response['week_worth_qty'] =  $data ? $data->week_worth_qty : "-";
                        $response['min_order_qty'] =  $data ? $data->min_order_qty : "-";
                        $response['suggested_order_qty'] = $suggestedOrderQty;
                        $response['order_qty'] = $datum->qty_ordered;
                        $response['weight'] = $data ? $data->weight : '-';
                        $response['product_availability'] = isset($data->supplier_status) && $data->supplier_status === '1' ? 'Yes' : 'No';
                        $response['lead_time'] = $data ? $data->lead_time : '-';
                        $response['temp'] = 'na';
                        if($data){
                            if (str_contains(strtolower($data->product_temperature), 'dry')) {
                                $response['temp'] = 'dry';
                            } else if (str_contains(strtolower($data->product_temperature), 'frozen')) {
                                $response['temp'] = 'frozen';
                            } else if (str_contains(strtolower($data->product_temperature), 'refrigerated')
                                        || str_contains(strtolower($data->product_temperature), 'beverages')) {
                                $response['temp'] = 'refrigerated';
                            } else {
                                $response['temp'] = 'na';
                            }
                        }
                        

                        array_push($responses, $response);
                }
            }
        }

        return Datatables::of($responses)->addIndexColumn()->make(true);
    }

    public function SavedPurchaseOrderProductsForAsn(Request $request) {

        $responses = [];
        if ($request->warehouse_id && $request->po) {
            
            $pd = null;
            
            if (isset($request->supplier_id)) {
                $pd = PurchasingDetail::where('po', $request->po)
                    ->where('supplier_id', $request->supplier_id)
                    ->where('warehouse_id', $request->warehouse_id)->get();
            } else {
                $pd = PurchasingDetail::where('po', $request->po)
                    ->where('client_id', $request->client_id)
                    ->where('warehouse_id', $request->warehouse_id)->get();
            }
            if ($pd) {
                $type = !isset($request->supplier_id) ? 'client' : 'supplier';
                // $supplier = isset($request->supplier_id) ? $request->supplier_name : $request->client_name;
                $supplier = isset($request->supplier_id) ? $request->supplier_id : $request->client_id;
                $supplier = htmlspecialchars_decode($supplier, ENT_QUOTES);
                foreach($pd as $datum) {

                    $data = DB::table('master_product')->where('is_approve', 1)->whereNull('parent_ETIN')
                                    ->where('client_supplier_id', $supplier)->where('supplier_type', $type)
                                    ->where('ETIN', $datum->etin)->first();

                    $response['ETIN'] = $datum->etin;
                    $response['supplier_product_number'] = $data ? $data->supplier_product_number : "-";
                    $response['product_listing_name'] = $data ? $data->product_listing_name : "-";
                    $response['order_qty'] = $datum->qty_ordered;
                    $response['asn_bol_shipped_qty'] = $datum->asn_bol_shipped_qty;
                    $response['bol'] = $datum->bol_number;
                    $response['asn_qty'] = $datum->asn_qty;
                    $response['asn'] = $datum->asn;
                    $response['weight'] = $data->weight;
                    if (str_contains(strtolower($data->product_temperature), 'dry')) {
                        $response['temp'] = 'dry';
                    } else if (str_contains(strtolower($data->product_temperature), 'frozen')) {
                        $response['temp'] = 'frozen';
                    } else if (str_contains(strtolower($data->product_temperature), 'refrigerated')
                                || str_contains(strtolower($data->product_temperature), 'beverages')) {
                        $response['temp'] = 'refrigerated';
                    } else {
                        $response['temp'] = 'na';
                    }

                    array_push($responses, $response);
                }
            }
        }

        return Datatables::of($responses)->addIndexColumn()->make(true);
    }

    // Not Needed check and delete
    public function getPurchaseSummary($supplierId) {

        $ps = DB::table('purchasing_summaries')
            ->join('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')
            ->where('purchasing_summaries.supplier_id', $supplierId)->get();
        $responses = [];
    	if ($ps && count($ps) > 0) {
    		foreach($ps as $p) {
                array_push($responses, [
                    'warehouse' => $p->warehouses,
                    'order' => $p->order,
                    'order_date' => $p->purchasing_asn_date,
                    'po_status' => $p->po_status
                ]);
            }
    	}

        return Datatables::of($responses)->addIndexColumn()->make(true);
    }

    public function saveDraftPo(Request $request) {
        $request->validate([
            'warehouse_id' => 'required',
            // 'order_date' => 'required',
            'delivery_date' => 'nullable|after_or_equal:order_date'
        ]);
        $res = $this->saveFunction($request, "Pending");
        if ($res) {
            return response()->json([
                'error' => $res
            ], 422);
        } else {
            return response()->json([
                'error' => 0,
                'msg' => 'PO Saved As Draft',
                'url' => $request->supplier_id 
                            ? url('suppliers/' . $request->supplier_id . '/edit')
                            : url('clients/' . $request->client_id . '/edit')
            ]);
        }    
    }

    public function submitPurchaseOrder(Request $request) {

        $request->validate([
            'warehouse_id' => 'required',
            'order_date' => 'required',
            'items' => 'required',
            'delivery_date' => 'required|after_or_equal:order_date'
        ]);

        $item_added = 0;
        $items = json_decode($request->items, true);
        
        if(count($items) > 0){
            foreach($items as $row_item){
                if($row_item['order_qty'] > 0){
                    $item_added = 1;
                }
            }
        }

        if($item_added == 0){
            return response(['errors_item' => ['Please select an item']], 422);
        }
        
        $res = $this->saveFunction($request, "Submitted", true);
        if ($res) {
            return response()->json([
                'error' => $res
            ], 422);
        } else {
            return response()->json([
                'error' => 0,
                'msg' => 'PO Submitted',
                'url' => $request->supplier_id 
                            ? url('suppliers/' . $request->supplier_id . '/edit')
                            : url('clients/' . $request->client_id . '/edit')
            ]);
        } 
    }

    public function saveFunction(Request $request, $status, $isSubmit = false) {

        // $validator = Validator::make($request->all(), [
        //     'warehouse_id' => 'required',
        //     'order_date' => 'required',
        //     'items' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response($validator->errors()->all(), 422);
        // }

        if (!(isset($request->supplier_id) || isset($request->client_id))) {
            return response(['error' => ['Supplier and Client both cannot be blank.']], 422);
        }

        if ($request->order_date > $request->delivery_date) {
            return response(['error' => ['Order date cannot be greator than delivery date']], 422);
        }
        
        $ps = null;
        if ($request->summary_id) {
            $ps = PurchasingSummary::where('id', $request->summary_id)->first();
        }


        // Get max PO
        $orderId = DB::table('purchasing_summaries')->max('order');
        if (!$orderId || $orderId <= 0) {
            $orderId = 1000;
        } else {
            $orderId = $orderId + 1;
        }

        $array = json_decode($request->items, true);
        $item_added = 1;
        if(count($array) > 0){
            foreach($array as $row_item){
                if($row_item['order_qty'] > 0){
                    $item_added = 1;
                }
            }
        }

        if($item_added == 0){
            return response(['errors_item' => ['Please select an item']], 422);
        }

        if ($ps && $ps->id) {
            $ps->supplier_id = $request->supplier_id;
            $ps->client_id = $request->client_id;
            $ps->warehouse_id = $request->warehouse_id;
            $ps->purchasing_asn_date = $request->order_date;
            $ps->delivery_inbound_fees = $request->delivery_fees;
            $ps->freight_shipping_charge = $request->freight_fees;
            $ps->surcharge_1 = $request->surcharge;
            $ps->po_status = $status;
            $ps->delivery_date = $request->delivery_date;
            $ps->save();
            $orderId = $ps->order;
        } else {
            $ps = PurchasingSummary::create([
                'supplier_id' => $request->supplier_id,
                'client_id' => $request->client_id,
                'warehouse_id' => $request->warehouse_id,
                'purchasing_asn_date' => $request->order_date,
                'order' => $orderId,
                'delivery_inbound_fees' => $request->delivery_fees,
                'freight_shipping_charge' => $request->freight_fees,
                'surcharge_1' => $request->surcharge,
                'po_status' => $status,
                'delivery_date' => $request->delivery_date
            ]);
        }
        
        PurchasingDetail::where('po', $ps->order)->delete();

        $data = [];
        $modArray = [];
        foreach ($array as $obj) {
            if ($isSubmit && $obj['order_qty'] <= 0) continue;
            array_push($data, [
                'supplier_id' => $request->supplier_id,
                'client_id' => $request->client_id,
                'po' => $orderId,
                'etin' => $obj['etin'], 
                'qty_ordered' => $obj['order_qty'],
                'asn_bol_shipped_qty' => null,
                'warehouse_id' => $request->warehouse_id
            ]);
            array_push($modArray, $obj);          
        }
        PurchasingDetail::insert($data);
        
        if ($isSubmit) {
            $file = 'Purchase_Order_' . $orderId . '_' .date('d.m.Y').'.csv';
            $file_with_fol = 'purchase_orders/' . $file;
            
            if (file_exists($file_with_fol)) {
                unlink($file_with_fol);
            }            

            Excel::store(new PurchaseOrderExport($modArray), $file_with_fol, 'real_public');
            
            $ps->report_path = $file_with_fol;
            $ps->save();
        }
    }

    public function saveAsnBol(Request $request) {

        $validator = Validator::make($request->all(), [
            'summary_id' => 'required',
            'items' => 'required',
            'po' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors()->all()], 422);
        }

        if (!(isset($request->supplier_id) || isset($request->client_id))) {
            return response(['error' => 'Supplier and Client both cannot be blank.'], 422);
        }

        $array = json_decode($request->items, true);
        // return response($array);

        foreach ($array as $row) {
            
            $pd = PurchasingDetail::where('etin', $row['etin'])->where('po', $request->po)->first();
            $pd->asn_bol_shipped_qty = ($row['asn_bol_shipped_qty'] == 'NULL' || $row['asn_bol_shipped_qty'] == 'null') ? null : $row['asn_bol_shipped_qty'];
            $pd->bol_number = $row['bol'];
            $pd->asn_qty = ($row['asn_qty'] == 'NULL' || $row['asn_qty'] == 'null') ? null : $row['asn_qty'];
            $pd->asn = $row['asn'];
            $pd->save();
            if($pd->asn != '' && $pd->asn_qty != '' && $pd->asn_bol_shipped_qty != '' && $pd->bol_number != ''){
                $pd->status = 'Ready';
                $pd->save();
            }
            
            PurchaseOrderExpAndLot::where('pd_id',$pd->id)->delete();
        }

        PurchasingSummary::where('id', $request->summary_id)->update(['po_status' => 'Ready to Receive']);

        return response()->json([
            'error' => 0,
            'msg' => 'ASN/BOL Submitted Successfully',
            'url' => isset($request->supplier_id) 
                            ? url('suppliers/' . $request->supplier_id . '/edit') 
                            : url('clients/' . $request->client_id . '/edit')
        ]);
    }

    public function get_lot_and_exp($order){
        $result = PurchasingDetail::where('po',$order)->get();
        $status = null;
        $Presult = PurchasingDetail::where('po',$order)->first();
        if($Presult){
            // $ps = PurchasingSummary::where('order', $Presult->po)->first();
            $status = $Presult->status;
        }
        if($status === NULL){
            $status = '';
        }
        var_dump($status);
        
        return view('clients.lots_and_exp',compact('result','order','status'));
    }

    public function SaveLotAndExp(Request $request,$id){
        // dd($request->all());
        $input = $request->all();
        $exp_lots = $input['EL'];
        if($exp_lots){
            foreach($exp_lots as $key => $row_lots){
                $exp_date = $row_lots['exp_date'];
                $lot = $row_lots['lot'];
                $qty = $row_lots['qty'];
                $ETIN = $row_lots['ETIN'][0];
                $asn_bol_shipped_qty = $row_lots['asn_bol_shipped_qty'][0];
                $total_qty = 0;
                if($exp_date && $exp_date[0] != ''){
                    foreach($exp_date as $key_exp => $row_exp){
                        if($row_exp != '' || $row_exp != null){
                                $total_qty = $total_qty + $qty[$key_exp];
                        }
                    }
                    if($total_qty != $asn_bol_shipped_qty){
                        return response([
                            'errors' => [
                                'pro_'.$key => 'Total Added Qty has to be same as asn bol qty'
                            ]
                            ],404);
                    }
                }

                

            }
        }
        if($exp_lots){
            foreach($exp_lots as $key => $row_lots){
                PurchaseOrderExpAndLot::where('pd_id',$key)->delete();
                $exp_date = $row_lots['exp_date'];
                $lot = $row_lots['lot'];
                $qty = $row_lots['qty'];
                $ETIN = $row_lots['ETIN'][0];
                $bol_number = $row_lots['bol_number'][0];
                

                if($exp_date){
                    foreach($exp_date as $key_exp => $row_exp){
                        if($row_exp != '' || $row_exp != null){
                            $PurchaseOrderExpAndLot = new PurchaseOrderExpAndLot;
                            $PurchaseOrderExpAndLot->po = $id;
                            $PurchaseOrderExpAndLot->pd_id = $key;
                            $PurchaseOrderExpAndLot->ETIN = $ETIN;
                            $PurchaseOrderExpAndLot->exp_date = $row_exp;
                            $PurchaseOrderExpAndLot->lot = $lot[$key_exp];
                            $PurchaseOrderExpAndLot->qty = $qty[$key_exp];
                            $PurchaseOrderExpAndLot->bol_number = $bol_number;
                            $PurchaseOrderExpAndLot->save();
                        }
                        
                    }
                }
            }
        }

        return response([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }
}
