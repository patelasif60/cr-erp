<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\User;
use App\Client;
use App\HotRoute;
use App\WareHouse;
use App\IceSubChart;
use App\OrderDetail;
use App\OrderPackage;
use App\OrderSummary;
use App\MasterProduct;
use App\UpsZipZoneByWH;
use App\OrderPickAndPack;
use App\PackagingMaterials;
use Illuminate\Http\Request;
use App\CustomClientOuterBox;
use App\MaterialWarehouseTdCount;
use Illuminate\Support\Facades\DB;
use App\MasterProductKitComponents;
use App\PackagingcomponentsSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\NotificationRepository;
use App\OrderPack;
use App\PickedLotAndExp;
use Carbon\Carbon;


class PackController extends Controller
{
    public function __construct(NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->NotificationRepository = $NotificationRepository;
	}

    public function create_package(Request $request,$sub_order_id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $full_order_id = explode(".", $sub_order_id);
        $order_id = $full_order_id[0];
        if(!isset($full_order_id[1])){
            return response(
                [
                    "error" => true, 
                    'message' => 'Barcode Format is not corrent'
                ], 400
            );
        }

        $orderSummary = OrderSummary::where('etailer_order_number', $order_id)->first();
        if(!$orderSummary){
            return response(
                [
                    "error" => true, 
                    'message' => 'Order not found'
                ], 400
            );
        }

        $orderDetails = OrderDetail::where('sub_order_number', $sub_order_id)->whereIN('status',[3,11])->first();
        if(!$orderDetails){
            return response(
                [
                    "error" => true, 
                    'message' => 'SubOrder not found'
                ], 400
            );
        }
        if(!in_array($orderDetails->status, [3,11])){
            return response(
                [
                    "error" => true, 
                    'message' => 'Either Sub-order not picked or already packed/shipped'
                ], 400
            );
        }

        $orderPackage = OrderPackage::where('order_id', $sub_order_id)->orderBy('id', 'desc')->first();
        if($orderPackage){
            $packageNumber = $orderPackage->package_num + 1;
        }
        else{
            $packageNumber = 1;
        }

        $response['sub_order_number'] = $sub_order_id;
        $response['name'] = $orderSummary->ship_to_name;
        $response['order_source'] = $orderSummary->order_source;
        $response['destination'] = $orderSummary->ship_to_state;
        $response['zip'] = $orderSummary->ship_to_zip;
        $response['city'] = $orderSummary->ship_to_city;
        $response['address'] = $orderSummary->ship_to_address1;
        $response['package_number'] = $packageNumber;
        $response['transit_days'] = isset($orderDetails->transit_days) ? $orderDetails->transit_days : 0;
        
        $items_needs_to_pack = OrderPickAndPack::whereNull('pack_qty')->where('sub_order_number',$sub_order_id)->whereNotNull('parent_ETIN')->get()->toArray();
        $items = [];
        if($items_needs_to_pack){
            foreach($items_needs_to_pack as $row_item_pack){
                $parent_ETIN = explode(',',$row_item_pack['parent_ETIN']);
                if($parent_ETIN){
                    foreach($parent_ETIN as $row_parent_etin){
                        $items[] = $row_parent_etin;
                    }
                }
            }
        }
        $response['kit_items'] = array_unique($items);

        $p_ids = [];
        $o_ds = OrderDetail::where('sub_order_number', $sub_order_id)->get();
        foreach ($o_ds as $od) {
            array_push($p_ids, $od->product->id);
        }

        $names = [];
        $pm = PackagingMaterials::where('clients_assigned', $orderSummary->client_id)
            ->where('material_type_id', 1)->get();
        if (!isset($pm) || count($pm) <= 0) {
            $response['box'] = 'ANY';
        } else {

            $names = $this->get_package_names($pm, $orderSummary, $o_ds);

            if (count($names) <= 0) {
                $pms_no_client = PackagingMaterials::where(function($query) {
                    $query->whereNull('clients_assigned')
                    ->orWhere('clients_assigned', '');
                })->where('material_type_id', 1)->get();
                if (isset($pms_no_client) && count($pms_no_client) > 0) {
                    foreach($pms_no_client as $pmnc) {
                        if(!in_array($pmnc->product_description, $names)) {
                            array_push($names, $pmnc->product_description);
                        }
                    }
                }
            }

            $response['box'] = count($names) > 0 ? implode(', ', $names) : 'ANY';               
        }

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Pack',
            'details' => $sub_order_id.' has been scanned to pack',
            'type' => 'CWMS',
            'sub_order_number' => $sub_order_id,
            'etailer_order_number' => $orderSummary->etailer_order_number,
            'channel_order_number' => $orderSummary->channel_order_number,
            'client_order_number' => $orderSummary->sa_order_number
        ]);

        return response(
            [
                "error" => false, 
                'data' => $response
            ], 200
        );
    }

    private function get_package_names($pm, $orderSummary, $o_ds) {

        $names = [];
        foreach($pm as $p) {
            if (isset($p->channel_ids) && $p->channel_ids != '' && $orderSummary->channel_id 
                && $orderSummary->channel_id != '' 
                && !in_array($orderSummary->channel_id, explode(',', $p->channel_ids))) {
                continue;
            }
            
            if (!$this->isItemPresent($o_ds, $p)) {
                continue;
            }
            // dump($p->id);
            // dump($this->isQuantityMatchingForTransitDay($o_ds, $p));
            if (!$this->isQuantityMatchingForTransitDay($o_ds, $p)) {
                continue;
            }

            if(!in_array($p->product_description, $names)) {
                array_push($names, $p->product_description);
            }
        }

        return $names;
    }

    private function isQuantityMatchingForTransitDay($orders, $scanned_package) {
        
        $wh = $orders[0]->warehouse_info->id;        
        $td = $orders[0]->transit_days;
        // dump('***');
        // dump($wh);
        // dump($td);
        // dump($scanned_package->id);
        // dump('***');

        $mwtc = MaterialWarehouseTdCount::where('material_id', $scanned_package->id)
            ->where('wh_id', $wh)->where('transit_days', $td)->first();

        if (!isset($mwtc) || !isset($mwtc->count)) { return true; }

        
        $total_count = 0;

        foreach($orders as $o) { 

            $item_form_desc = $o->product->item_form_description;
            $item_form_desc = strtolower($item_form_desc);

            if ($item_form_desc == 'each') {
                $total_count += $o->quantity_fulfilled;
            } else if ($item_form_desc == 'pack' || $item_form_desc == 'case' || $item_form_desc == 'case as each') {
                $total_count += ($o->quantity_fulfilled * $o->product->unit_in_pack);
            } else if ($item_form_desc == 'kit') {
                $kit_comps = MasterProductKitComponents::leftJoin('master_product', function($join){
                    $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
                })->where('master_product_kit_components.ETIN', $o->product->ETIN)->get();
                if (isset($kit_comps) && count($kit_comps) > 0) {
                    foreach($kit_comps as $comp) {
                        $item_form_desc = $comp->component_product_details->item_form_description;
                        $item_form_desc = strtolower($item_form_desc);

                        if ($item_form_desc == 'each') {
                            $total_count += $o->quantity_fulfilled;
                        } else if ($item_form_desc == 'pack' || $item_form_desc == 'case' || $item_form_desc == 'case as each') {
                            $total_count += ($o->quantity_fulfilled * $o->product->unit_in_pack);
                        }
                    }
                }
            }
        }
        
        return $total_count <= $mwtc->count;
    }

    private function isItemPresent($orders, $scanned_package) {

        $p_ids = [];
        foreach ($orders as $od) {
            array_push($p_ids, $od->product->id);
        }

        $found = true;
        if (isset($scanned_package->product_ids) && $scanned_package->product_ids != '') {
            $pk_pids = explode(',', $scanned_package->product_ids);
            foreach($p_ids as $p_id) { 
                if (!in_array($p_id, $pk_pids)) {
                    $found = false;
                    break; // Break if one product in order is not in package selection
                }
            }
        }
        
        return $found;
    }

    public function scan_package(Request $request,$scan_package, $sub_order_id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $full_order_id = explode(".", $sub_order_id);
        $order_id = $full_order_id[0];
        

        $orderSummary = OrderSummary::where('etailer_order_number', $order_id)->first();
        if(!$orderSummary){
            return response(
                [
                    "error" => true, 
                    'message' => 'Order not found'
                ], 400
            );
        }

        $package_break = explode("-", $sub_order_id);
        $packageNumber = $package_break[1];
        $subOrderNum = $package_break[0];

        $od = OrderDetail::where('sub_order_number', $subOrderNum)->first();
        
        if (!isset($od)) {
            return response(
                [
                    "error" => true, 
                    'message' => 'Invalid Sub-Order Number'
                ], 400
            );
        }

        $packagingMaterials = PackagingMaterials::where('scannable_barcode', $scan_package)->first();

        if(!$packagingMaterials){
            return response(
                [
                    "error" => true, 
                    'message' => 'Box/Package not found'
                ], 400
            );
        } 
        
        if($packagingMaterials->material_type_id != 1){
            return response(
                [
                    "error" => true, 
                    'message' => 'Box/Package is not an Outer Box.'
                ], 400
            );
        }

        if($packagingMaterials->has_barcode == 0){
            return response(
                [
                    "error" => true, 
                    'message' => 'Barcode is not scannable'
                ], 400
            );
        }

        $orders = OrderDetail::where('sub_order_number', $subOrderNum)->get();

        
        $pm = PackagingMaterials::where('clients_assigned', $orderSummary->client_id)
            ->where('material_type_id', 1)->get();
        
        $names = [];
        
        if (isset($pm) && count($pm) > 0) {
            $names = $this->get_package_names($pm, $orderSummary, $orders);
        }

        // dump($packagingMaterials->product_description);
        // dd($names);

        if (count($names) > 0 && !in_array($packagingMaterials->product_description, $names)) {
            return response(
                [
                    "error" => true, 
                    'message' => 'Box/Package is not valid for this Order due to criteria mismatch.'
                ], 400
            );
        }

        // If client is attached to the package
        if (isset($packagingMaterials->clients_assigned) && $packagingMaterials->clients_assigned != '') {  
            
            if ($packagingMaterials->clients_assigned != $orderSummary->client_id) {
                return response(
                    [
                        "error" => true, 
                        'message' => 'Box/Package is not valid for this Order due to client mismatch'
                    ], 400
                );
            }
            
            if (isset($packagingMaterials->channel_ids) && isset($packagingMaterials->channel_ids) 
                && $packagingMaterials->channel_ids != '' && $orderSummary->channel_id && $orderSummary->channel_id != ''
                && !in_array($orderSummary->channel_id, explode(',', $packagingMaterials->channel_ids))) {
                return response(
                    [
                        "error" => true, 
                        'message' => 'Box/Package is not valid for this Order due to channel mismatch.'
                    ], 400
                );
            }

            if (!$this->isItemPresent($orders, $packagingMaterials)) {
                return response(
                    [
                        "error" => true, 
                        'message' => 'Box/Package is not valid for this Order due to item mismatch.'
                    ], 400
                );
            }            
        }        

        // dd($this->isQuantityMatchingForTransitDay($orders, $packagingMaterials));
        
        // if (!$this->isQuantityMatchingForTransitDay($orders, $packagingMaterials)) {
        //     return response(
        //         [
        //             "error" => true, 
        //             'message' => 'Box/Package is not valid for this Order due to quantity mismatch.'
        //         ], 400
        //     );
        // }
        
        $ice_block = 0;
        $ice_pallet = 0;
        $ETINS = [];
        $TEMPS = [];
        // $Child_Component = PackagingcomponentsSetting::with('PackagingMaterials','ProductTemperature')->where('parent_packaging_material_id',$packagingMaterials->id)->get();
        
        if($request->all()){
            foreach($request->all() as $row_pro){
                $ETINS[] = $row_pro['ETIN'];
                $TEMPS[] = $row_pro['product_temperature'];
            }
        }
        // $OrderInfo = OrderDetail::where('sub_order_number',$subOrderNum)->whereIN('ETIN',$ETINS)->first();
        $OPP = OrderPickAndPack::where('sub_order_number',$subOrderNum)->whereIN('ETIN',$ETINS)->first();
        if($OPP){
            $OrderInfo = $OPP->sub_order;
            if($OrderInfo){
                if(in_array('Frozen',$TEMPS)){
                    $wah = $OrderInfo->warehouse;
                    //$transitDay = $this->getTransitDay($orderSummary->ship_to_state, $orderSummary->ship_to_zip, $wah);
                    $transitDay = $OrderInfo->transit_days;
                    $wh = WareHouse::where('warehouses', $wah)->first();
                    
                    $get_ice_chart_template = DB::table('ice_chart_template_warehouse')->where('warehouse_id',$wh->id)->first();
                    if($get_ice_chart_template){
                        $ice_chart_id = $get_ice_chart_template->ice_chart_template_id;
                        $ice_chart_product = IceSubChart::where('ice_chart_template_id',$ice_chart_id)->where('packaging_materials_id',$packagingMaterials->id)->first();
                        if($ice_chart_product){
                            $ice_row = $ice_chart_product->toArray();
                            if($transitDay == 1){
                                $ice_block = $ice_row['1day_block'];
                                $ice_pallet = $ice_row['1day_pellet'];
                            }

                            if($transitDay == 2){
                                $ice_block = $ice_row['2day_block'];
                                $ice_pallet = $ice_row['2day_pellet'];
                            }

                            if($transitDay == 3){
                                $ice_block = $ice_row['3day_block'];
                                $ice_pallet = $ice_row['3day_pellet'];
                            }

                            if($transitDay == 4){
                                $ice_block = $ice_row['4day_block'];
                                $ice_pallet = $ice_row['4day_pellet'];
                            }
                        }
                        
                    }

                }
                
            }
        }
        $response['sub_order_number'] = $subOrderNum;
        $response['name'] = $orderSummary->ship_to_name;
        $response['order_source'] = $orderSummary->order_source;
        $response['destination'] = $orderSummary->ship_to_state;
        $response['zip'] = $orderSummary->ship_to_zip;
        $response['city'] = $orderSummary->ship_to_city;
        $response['address'] = $orderSummary->ship_to_address1;
        $response['package_number'] = $packageNumber;
        $response['packagingMaterial_id'] = $packagingMaterials->id;
        $response['packagingMaterial_product_description'] = $packagingMaterials->product_description;
        $response['ice_block'] = $ice_block;
        $response['ice_pallet'] = $ice_pallet;
        // $response['Child_Component'] = $Child_Component;
        $response['gift_message'] = $orderSummary->gift_message;
        $response['is_hot_route'] = $od->hot_route == 1 ? 'Yes' : 'No';
        
        $Packed_ETIN = [];
        $packed_items = OrderPickAndPack::where('sub_order_number',$subOrderNum)->whereNotNUll('pack_qty')->get();
        if($packed_items){
            foreach($packed_items as $rowPackedItem){
                $Packed_ETIN[] = $rowPackedItem->ETIN;
                if($rowPackedItem->parent_ETIN != ''){
                    $Packed_ETIN[] = $rowPackedItem->parent_ETIN;
                }
            }
        }

        $Packed_ETIN = array_unique($Packed_ETIN);
        $OrderItems = OrderDetail::with('product','product.kit_products','product.kit_products.component_product_details')->where('sub_order_number',$subOrderNum)->whereIN('ETIN',$Packed_ETIN)->get();
        $Kit_items = [];
        if($OrderItems){
            foreach($OrderItems as $row_items){
                $product = $row_items->product;
                $Kit_items[] = [
                    'product_listing_name' => $product->product_listing_name,
                    'quantity_ordered' => $row_items->quantity_ordered
                ];
                if($row_items->quantity_ordered > 0){
                    if(isset($product->kit_products)){
                        foreach($product->kit_products as $row_kit_pro){
                            // if(in_array($row_kit_pro->components_ETIN,$Packed_ETIN)){
                                $Kit_items[] = [
                                    'product_listing_name' => '&nbsp;&nbsp;&nbsp;  --->'.$row_kit_pro->component_product_details->product_listing_name,
                                    'quantity_ordered' => $row_items->quantity_ordered * $row_kit_pro->qty
                                ];  
                            // }
                        }      
                    }
                }
            }
        }

        
        $PM = [];
        $ETINS = [];
        $input = $request->all();
        if($input){
            foreach($input as $row_input){
                $masterProduct = MasterProduct::where('ETIN',$row_input['ETIN'])->first();
                if($masterProduct){
                    $unit_in_pack = $masterProduct->unit_in_pack;
                    $item_form_description = strtolower($masterProduct->item_form_description);
                    $qty  = $row_input['qty'];
                    if ($item_form_description == 'pack' || $item_form_description == 'case' || $item_form_description == 'case as each') {
                        $qty = $qty * $unit_in_pack;
                    }
                    $PM[] = [
                        'ETIN' => $row_input['ETIN'],
                        'qty' => $qty,
                        'unit_description' => $masterProduct->unit_description
                    ];
                }
                // $ETINS[] = $row_input['ETIN'];
            }
        }
        $OD = OrderDetail::where('sub_order_number',$subOrderNum)->first();
        

        $output = array();
        foreach ($PM as $values) {
            $key = $values['unit_description'];
            $output[$key][] = $values;
        }

        $new_out_put = [];
        if($output){
            foreach($output as $key_out => $row_out){
                $total_count = 0;
                if($row_out){
                    foreach($row_out as $new_row_out){
                        $total_count+= $new_row_out['qty'];
                    }
                }
                $new_out_put[] = [
                    'unit_description' => $key_out,
                    'total_count' => $total_count
                ];
            }
        }

        DeveloperLog([
            'reference' => 'Item Grouping',
            'ref_request' => json_encode($PM),
            'ref_response' => json_encode($new_out_put)
        ]);        
        $RequiredComponentObject = new PackagingMaterials();
        $RequiredComponent = $RequiredComponentObject->GetTheRequiredPackagingComponent($new_out_put,$OD);


        $response['OrderItems'] = $Kit_items;
        $response['PackagingItems'] = $RequiredComponent;

        
        $OrderPack = OrderPack::where('sub_order_number',$subOrderNum)->whereNull('transfer')->get();
        $response['OrderPack'] = $OrderPack;
        
        return response(
            [
                "error" => false, 
                'data' => $response
            ], 200
        );
    }

    public function add_products(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $sub_order_id = $request->sub_order_id;
        $upc = $request->upc;
        $scannable_barcode = $request->scannable_barcode;
        $scannedBox = $request->scannedBox;
        $package_components = $request->package_components;
        $masterProduct = MasterProduct::with('kit_products','kit_products.component_product_details')->where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if(!$masterProduct){
            return response(
                [
                    "error" => true, 
                    'message' => 'Product not found'
                ], 400
            );
        }

        $is_item_picked = OrderPickAndPack::where('sub_order_number',$sub_order_id)->where('ETIN',$masterProduct->ETIN)->first();
        if(!$is_item_picked){
            return response(
                [
                    "error" => true, 
                    'message' => 'Item not found on order'
                ], 400
            );
        }

        if($is_item_picked->pick_qty == $is_item_picked->pack_qty){
            return response(
                [
                    "error" => true, 
                    'message' => 'Item has been packed'
                ], 400
            );
        }



        if($is_item_picked->pick_qty == ''){
            return response(
                [
                    "error" => true, 
                    'message' => 'Item not picked'
                ], 400
            );
        }

        $OD = OrderDetail::where('sub_order_number',$sub_order_id)->first();
        if(!$OD){
            return response(
                [
                    "error" => true, 
                    'message' => 'No Sub order found'
                ], 400
            );
        }

        


        $response['ETIN'] = $masterProduct->ETIN;
        $response['prod_desc'] = $masterProduct->product_listing_name;
        $response['product_temperature'] = $masterProduct->product_temperature;
        $response['quantity_ordered'] = $is_item_picked->quantity_ordered;
        $response['quantity_fulfilled'] = ($is_item_picked->pick_qty - $is_item_picked->pack_qty);
        $response['qty'] = 1;
        $response['kit_products'] = $masterProduct->kit_products;
        $response['parent_ETIN'] = $is_item_picked->parent_ETIN;

        $picked_lot_and_exp = PickedLotAndExp::where('sub_order',$sub_order_id)->where('Main_ETIN',$masterProduct->ETIN)->whereNull('for_the_log')->orderBy('exp','ASC')->get();
        if(count($picked_lot_and_exp) > 0){
            $picked_lot_and_exp->transform(function ($record) {
                $record->date_column ='';
                if($record->exp){
                    $record->date_column = Carbon::createFromFormat('Y-m-d', $record->exp);    
                }
                return $record;
            });
            $picked_lot_and_exp = $picked_lot_and_exp->sortBy('date_column');
        }
        

        $OP = OrderPack::where('ETIN',$masterProduct->ETIN)->where('sub_order_number',$sub_order_id)->whereNull('transfer')->first();
        if($OP){
           if(count($picked_lot_and_exp) == 0){
            $OP->qty = $OP->qty + 1;
            $OP->save();
           } 
        }else{
            $OP = new OrderPack;
            $OP->ETIN = $masterProduct->ETIN;
            $OP->sub_order_number = $sub_order_id;
            $OP->prod_desc = $masterProduct->product_listing_name;
            $OP->product_temperature = $masterProduct->product_temperature;
            $OP->qty = count($picked_lot_and_exp) > 0 ? 0 : 1;
            $OP->parent_ETIN = $is_item_picked->parent_ETIN;
            $OP->quantity_fulfilled = count($picked_lot_and_exp) > 0 ? 0 : ($is_item_picked->pick_qty - $is_item_picked->pack_qty);
            $OP->save();
        }

        $response['ROW'] = $OP;
        $orderSummary = $OD->orderSummary;
        $response['POEL'] = $picked_lot_and_exp;
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Pack',
            'details' => 'ETIN '.$masterProduct->ETIN.' has been scanned for sub order# '.$sub_order_id.'',
            'type' => 'CWMS',
            'sub_order_number' => $sub_order_id,
            'etailer_order_number' => $orderSummary->etailer_order_number,
            'channel_order_number' => $orderSummary->channel_order_number,
            'client_order_number' => $orderSummary->sa_order_number
        ]);

        // $response['PackagingComponent'] = isset($package_components[$product_found]) ? $package_components[$product_found] : NULL;


        return response(
            [
                "error" => false, 
                'data' => $response,
                'message' => 'Success'
            ], 200
        );

    }

    public function SelectLot(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);

        $id = $request->id;
        $POEL = $request->POEL;
        $ETIN = $request->ETIN;
        $lot = $request->lot;
        $exp_date = $request->exp_date;
        $sub_order_number = $request->sub_order_number;

        $row = OrderPack::where('ETIN',$ETIN)->where('sub_order_number',$sub_order_number)->where('lot',$lot)->where('exp',$exp_date)->whereNull('transfer')->first();
        if($row){
            if(($row->qty + 1) > $POEL['qty']){
                return response()->json([
                    'error' => true,
                    'message' => 'Qty can not be greater than required qty'
                ],400);
            }
            $row->qty = $row->qty + 1;
            $row->quantity_fulfilled = $POEL['qty'] - $POEL['pack_qty'];
            $row->save();
            UserLogs([
                'user_id' => $user_id,
                'action' => 'Select',
                'task' => 'Pack',
                'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  sub order number '.$sub_order_number,
                'type' => 'CWMS',
                'sub_order_number' => $sub_order_number
            ]);
            
        }else{
            $row = OrderPack::where('ETIN',$ETIN)->where('sub_order_number',$sub_order_number)->whereNull('lot')->whereNull('exp')->whereNull('transfer')->first();
            if($row){
                if(($row->qty + 1) > $POEL['qty']){
                    return response()->json([
                        'error' => true,
                        'message' => 'Qty can not be greater than required qty'
                    ],400);
                }

                $row->lot = $lot;
                $row->exp = $exp_date;
                $row->qty = $row->qty + 1;
                $row->quantity_fulfilled = $POEL['qty'] - $POEL['pack_qty'];
                $row->save();
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'Pack',
                    'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  sub order number '.$sub_order_number,
                    'type' => 'CWMS',
                    'sub_order_number' => $sub_order_number
                ]);
            }else{
                $masterProduct = MasterProduct::with('kit_products','kit_products.component_product_details')->where(function($q) use($ETIN){
                    $q->where('upc', $ETIN);
                    $q->orWhere('gtin',$ETIN);
                    $q->orWhere('ETIN',$ETIN);
                })->first();
                $is_item_picked = OrderPickAndPack::where('sub_order_number',$sub_order_number)->where('ETIN',$masterProduct->ETIN)->first();
                $OP = new OrderPack;
                $OP->ETIN = $ETIN;
                $OP->sub_order_number = $sub_order_number;
                $OP->lot = $lot;
                $OP->exp = $exp_date;
                $OP->qty = 1;
                $OP->quantity_fulfilled = $POEL['qty'] - $POEL['pack_qty'];
                $OP->prod_desc = $masterProduct->product_listing_name;
                $OP->product_temperature = $masterProduct->product_temperature;
                $OP->parent_ETIN = $is_item_picked->parent_ETIN;
                $OP->save(); 
                UserLogs([
                    'user_id' => $user_id,
                    'action' => 'Select',
                    'task' => 'Pack',
                    'details' => 'Lot '.$lot.' has been selected for Item '.$ETIN.'  &  sub order number '.$sub_order_number,
                    'type' => 'CWMS',
                    'sub_order_number' => $sub_order_number
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
        $sub_order_number = $request->sub_order_number;

        $row = OrderPack::find($request->id);
        $ETIN = $row->ETIN;
        $sub_order_number = $row->sub_order_number;

        $pt = OrderPack::where('ETIN',$ETIN)->where('sub_order_number',$sub_order_number)->where('lot',$lot)->where('exp',$exp_date)->whereNull('transfer')->where('id','!=',$request->id)->first();
        if($pt){
            return response()->json([
                'error' => true,
                'message' => 'Lot is already selected'
            ],400);
        }

        $POEL = PickedLotAndExp::where('sub_order',$sub_order_number)->where('Main_ETIN',$ETIN)->where('lot',$lot)->where('exp',$exp_date)->whereNull('for_the_log')->first();
        $quantity_fulfilled = 0;
        if($POEL){
            $quantity_fulfilled = $POEL->qty - $POEL->pack_qty;
        }

        if($row->qty > $quantity_fulfilled){
            return response()->json([
                'error' => true,
                'message' => 'Qty can not be greater than required qty'
            ],400);
        }
        
        $row->lot = $lot;
        $row->exp = $exp_date;
        $row->quantity_fulfilled = $quantity_fulfilled;
        $row->save();
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Select',
            'task' => 'Pack',
            'details' => 'Lot '.$lot.' has been has been Changed  '.$ETIN.'  &  bol '.$sub_order_number,
            'type' => 'CWMS',
            'sub_order_number' => $sub_order_number
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Success'
        ],200);
        
    }

    public function deletePackingItem($id){
        $row = OrderPack::find($id);   
        if($row){
            $row->delete();
        }
        return response(
            [
                "error" => false, 
                'message' => 'Success'
            ], 200
        );
    }

    public function getOrderPackItemInfo($id){
        $row = OrderPack::find($id);
        $picked_lot_and_exp = PickedLotAndExp::where('sub_order',$row->sub_order_number)->where('Main_ETIN',$row->ETIN)->whereNull('for_the_log')->orderBy('exp','ASC')->get();
        $picked_lot_and_exp->transform(function ($record) {
            $record->date_column = Carbon::createFromFormat('Y-m-d', $record->exp);
            return $record;
        });
        $POEL = $picked_lot_and_exp->sortBy('date_column');
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'POEL' => $POEL,
            'ROW' => $row
        ]);
    }

    public function save_package(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $req = $request->all();
        $validator = $request->validate([
            'sub_order_number' => 'required',
            'package_number' => 'required',
            'packagingMaterial_id' => 'required',
            'products' => 'required'
        ]);

        $products = $request->products;
        
        $component_ETINS = [];
        $added_products = [];
        // dd($products);

        
        if($products){
            
            foreach($products as $row){
                if($row['qty'] > $row['quantity_fulfilled']){
                    return response(
                        [
                            "error" => true, 
                            'message' => 'You have scanned to many qty of '.$row['ETIN']
                        ], 400
                    );
                }

                if($row['parent_ETIN'] != ''){
                    $OrderPickAndPack = OrderPickAndPack::where('sub_order_number', $req['sub_order_number'])->whereRaw('FIND_IN_SET("'.$row['parent_ETIN'].'", parent_ETIN)')->get();
                    if($OrderPickAndPack){
                        foreach($OrderPickAndPack as $row_order_and_pack){
                            $component_ETINS[] = $row_order_and_pack->ETIN;
                        }
                    }
                }

                $added_products[] = $row['ETIN'];
            }
        }

        $added_products = array_unique($added_products);
        $component_ETINS = array_unique($component_ETINS);


        $containsAllNeeded = 0 == count(array_diff($component_ETINS, $added_products));
        if($containsAllNeeded == false){
            return response(
                [
                    "error" => true, 
                    'message' => 'Please add all the items of the kit'
                ], 400
            );
        }

        
        if($products){
            foreach($products as $row){
                $OrderPickAndPack = OrderPickAndPack::where('sub_order_number', $req['sub_order_number'])->where('ETIN', $row['ETIN'])->first();
                $orderDetails = OrderDetail::where('sub_order_number', $req['sub_order_number'])->where(function($q) use($OrderPickAndPack){
                    $q->where('ETIN',$OrderPickAndPack->ETIN);
                    $q->orWhere('ETIN',$OrderPickAndPack->parent_ETIN);
                })->first();
                $orderSummary = $orderDetails->orderSummary;
                //$transitDay = $this->getTransitDay($orderSummary->ship_to_state, $orderSummary->ship_to_zip, $orderDetails->warehouse);
                $transitDay = $orderDetails->transit_days;
                
                
                $OrderPickAndPack->pack_qty = $OrderPickAndPack->pack_qty + $row['qty'];
                $OrderPickAndPack->package_number = $req['package_number'];
                $OrderPickAndPack->save();

                if($row['lot'] !== ''){
                    $POEL = PickedLotAndExp::where('sub_order',$req['sub_order_number'])->where('Main_ETIN',$row['ETIN'])->where('lot',$row['lot'])->where('exp',$row['exp'])->whereNull('for_the_log')->first();
                    if($POEL){
                        $POEL->pack_qty = $POEL->pack_qty + $row['qty'];
                        $POEL->save();
                    }
                }
                
                $OP = OrderPack::find($row['id']);
                if($OP){
                    $OP->transfer = 1;
                    $OP->save();
                }
                $OD = OrderDetail::where('sub_order_number', $req['sub_order_number'])->where(function($q) use($OrderPickAndPack){
                    $q->where('ETIN',$OrderPickAndPack->ETIN);
                    $q->orWhere('ETIN',$OrderPickAndPack->parent_ETIN);
                })->get();
                if($OD){
                    foreach($OD as $row_OD){
                        if($OrderPickAndPack->pick_qty == $OrderPickAndPack->pack_qty){
                            if($row_OD->status == 3){
                                $row_OD->status = 4;
                            }elseif($row_OD->status == 11){
                                $row_OD->status = 12;
                            }
                        }
                        $row_OD->transit_days = $transitDay == -1 ? 0 : $transitDay;
                        $row_OD->save();
                        
                    }
                }
                


                $this->NotificationRepository->SendOrderNotification([
                    'subject' => "Order Status: Packed",
                    'body' => 'Sub Order No: '.$orderDetails->sub_order_number .' with Package No: '.$req['package_number'].' has been Packed',
                    'user_id' => $orderDetails->picker_id,
                    'order_number' => $orderDetails->order_number,
                    'transit_days' => $orderDetails->transit_days
                ]); 

                UpdateOrderHistory([
                    'order_number' => $orderDetails->order_number,
                    'sub_order_number' => $orderDetails->sub_order_number,
                    'detail' => 'Sub Order #: '.$orderDetails->sub_order_number .' with Package #: '.$req['package_number'].' and ETIN '.$row['ETIN'].' has been Packed',
                    'title' => 'Sub Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($orderDetails)
                ]);
                $orderPackage = $this->addPackage($row['ETIN'], $orderDetails, $req['package_number'], $req['packagingMaterial_id'],$req['ice_block'],$req['ice_pallet'],$row['qty']);

            }
            $this->changeOrderSummaryStatus($req['sub_order_number'],$user_id);
        }

        $GetAllPickedItems = OrderPickAndPack::where('sub_order_number', $req['sub_order_number'])->where(function($q) {
            $q->whereColumn('pick_qty','<>', 'pack_qty');
            $q->orWhereNull('pack_qty');
        })->get();

        
        // $qry = str_replace(array('%', '?'), array('%%', '%s'), $GetAllPickedItems->toSql());
        // $qry = vsprintf($qry, $GetAllPickedItems->getBindings());
        // dd($qry);
        $response['sub_order_number'] = $req['sub_order_number'];
        $response['package_number'] = $req['package_number'];
        $ODCount = OrderDetail::where('sub_order_number', $req['sub_order_number'])->whereIn('status',[3,11])->count();
        $response['ItemLeft'] = $ODCount;
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Pack',
            'details' => 'Sub order# '.$orderDetails->sub_order_number.' has been packed',
            'type' => 'CWMS',
            'sub_order_number' => $orderDetails->sub_order_number,
            'etailer_order_number' => $orderSummary->etailer_order_number,
            'channel_order_number' => $orderSummary->channel_order_number,
            'client_order_number' => $orderSummary->sa_order_number
        ]);

        return response(
            [
                "error" => false, 
                'data' => $response,
                'message' => 'Items packed successfully.',
            ], 200
        );

    }

    public function save_package_child_component(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $input = $request->all();
        if($input){
            foreach($input as $row){
                $pt = PackagingcomponentsSetting::find($row['id']);
                $pt->qty = $row['qty'];
                $pt->save();
            }
        }

        UserLogs([
            'user_id' => $user_id,
            'action' => 'Scan',
            'task' => 'Ship',
            'details' => 'Update Packaging Component Qty',
            'type' => 'CWMS'
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Success'
        ]);
    
    }
    

    private function changeOrderSummaryStatus($subOrderNumber,$user_id) {
        
        $order_info = OrderDetail::select('order_number')->where('sub_order_number',$subOrderNumber)->first();
        if($order_info){
            $total_orders = OrderDetail::where('order_number',$order_info->order_number)->count();

            $packed_orders = OrderDetail::where('order_number',$order_info->order_number)->whereIn('status',[4,12])->count();

            if($total_orders == $packed_orders){
                OrderSummary::where('etailer_order_number',$order_info->order_number)->update([
                    'order_status' => 6
                ]);
                UpdateOrderHistory([
                    'order_number' => $order_info->order_number,
                    'sub_order_number' => $order_info->sub_order_number,
                    'detail' => 'Order #: '.$order_info->order_number .' has been Packed',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order_info)
                ]);
            }else{
                OrderSummary::where('etailer_order_number',$order_info->order_number)->update([
                    'order_status' => 5
                ]);
                UpdateOrderHistory([
                    'order_number' => $order_info->order_number,
                    'sub_order_number' => $order_info->sub_order_number,
                    'detail' => 'Order #: '.$order_info->order_number .' has been Partially Packed',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order_info)
                ]);
            }
            
            
        }

    }

    private function addPackage($ETIN, $orderDetails, $package_number, $packagingMaterial_id,$ice_block,$ice_pallet,$qty) {

        $check_package = OrderPackage::where('package_num',$package_number)->where('order_id',$orderDetails->sub_order_number)->where('ETIN',$ETIN)->first();
        if($check_package){
            $orderPackage = OrderPackage::find($check_package->id);
        }else{
            $orderPackage = new OrderPackage();
        }
        
        $orderPackage->package_num = $package_number;

        $wh = WareHouse::where('warehouses', $orderDetails->warehouse)->first();
        if (!$wh) {
            return response(
                [
                    'error' => true,
                    'message' => 'Invalid Warehouse: ' . $orderDetails->warehouse
                ], 404);
        }
        $orderPackage->warehouse_id = $wh->id;
        $orderPackage->order_id = $orderDetails->sub_order_number;
        $orderPackage->ETIN = $ETIN;
        $orderPackage->Order_ETIN = $orderDetails->ETIN;
        $orderPackage->shipped_qty = $orderPackage->shipped_qty + $qty;
        $orderPackage->box_used = $packagingMaterial_id; //Using Box for default
        $orderPackage->packer_name = null;
        $orderPackage->dry_ice_block_Lb = $ice_block;
        $orderPackage->dry_ice_pallet_Lb = $ice_pallet;
        $orderPackage->save();

        return $orderPackage;
    }

    private function getTransitDay($state, $zip, $wh) {
        
        $zip3 = substr($zip, 0, 3);
        $record = UpsZipZoneByWH::where('state', $state)->where('zip_3', $zip3)->first();
        if ($record) {
            if ($wh == 'WI') {
                return $record->transit_days_WI;
            } else if ($wh == 'PA') {
                return $record->transit_days_PA;
            } else if ($wh == 'NV') {
                return $record->transit_days_NV;
            } else {
                return $record->transit_days_OKC;
            } 
        }

        return -1;
    }


    public function PackSlip(Request $request, $sub_order_id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $full_order_id = explode(".", $sub_order_id);
        $order_id = $full_order_id[0];

        $orderSummary = OrderSummary::where('etailer_order_number', $order_id)->first();
        if(!$orderSummary){
            return response(
                [
                    "error" => true, 
                    'message' => 'Order not found'
                ], 400
            );
        }

        $package_break = explode("-", $sub_order_id);
        $packageNumber = $package_break[1];
        $subOrderNum = $package_break[0];
        $od = OrderDetail::where('sub_order_number', $subOrderNum)->first();
        if (!isset($od)) {
            return response(
                [
                    "error" => true, 
                    'message' => 'Invalid Sub-Order Number'
                ], 400
            );
        }

        

        
        $OrderItemsPrint = OrderPackage::with('product','product.kit_products','product.kit_products.component_product_details')->where('order_id',$subOrderNum)->where('package_num',$packageNumber)->get();
        $Kit_itemsPrint = [];
        if($OrderItemsPrint){
            foreach($OrderItemsPrint as $rowOIP){
                $product = $rowOIP->product;
                $Kit_itemsPrint[] = [
                    'product_listing_name' => $product->product_listing_name,
                    'quantity_ordered' => $rowOIP->shipped_qty
                ];
                if($rowOIP->shipped_qty > 0){
                    if(isset($product->kit_products)){
                        foreach($product->kit_products as $row_kit_pro){
                            // if(in_array($row_kit_pro->components_ETIN,$Packed_ETIN)){
                                $Kit_itemsPrint[] = [
                                    'product_listing_name' => '&nbsp;&nbsp;&nbsp;  --->'.$row_kit_pro->component_product_details->product_listing_name,
                                    'quantity_ordered' => $rowOIP->shipped_qty * $row_kit_pro->qty
                                ];  
                            // }
                        }      
                    }
                }
            }
        }

        $OIPF = $OrderItemsPrint->first();
        $packagingMaterials = PackagingMaterials::find($OIPF->box_used);

        $response['sub_order_number'] = $subOrderNum;
        $response['name'] = $orderSummary->ship_to_name;
        $response['order_source'] = $orderSummary->order_source;
        $response['destination'] = $orderSummary->ship_to_state;
        $response['zip'] = $orderSummary->ship_to_zip;
        $response['city'] = $orderSummary->ship_to_city;
        $response['address'] = $orderSummary->ship_to_address1;
        $response['package_number'] = $packageNumber;
        $response['packagingMaterial_id'] = $packagingMaterials->id;
        $response['packagingMaterial_product_description'] = $packagingMaterials->product_description;
        $response['ice_block'] = $OIPF->dry_ice_block_Lb;
        $response['ice_pallet'] = $OIPF->dry_ice_pallet_Lb;
        $response['gift_message'] = $orderSummary->gift_message;
        $response['is_hot_route'] = $od->hot_route == 1 ? 'Yes' : 'No';
        $response['subOrderDetails'] = $od;
        $response['OrderItems'] = $Kit_itemsPrint;
        return response(
            [
                "error" => false, 
                'data' => $response
            ], 200
        );

    }

    public function GetPackageStatus($sub_order,$packageNumber){
        $status = 0;
        $getInfo = OrderPackage::select('ETIN')->where('package_num',$packageNumber)->where('order_id',$sub_order)->first();
        if($getInfo){
            $OrderDetail = OrderDetail::select('status')->where('ETIN',$getInfo->ETIN)->where('sub_order_number',$sub_order)->first();
            if($OrderDetail){
                $status = $OrderDetail->status;
            }
        }

        return response(
            [
                "error" => false, 
                'data' => $status
            ], 200
        );

    }
}