<?php

namespace App;
use DB;
use App\OrderSummary;
use App\OrderPackage;
use Illuminate\Support\Facades\Log;


use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';

    protected $fillable = ['picker_id'];

    public function checkFulfilledBy($summary, $orderDetail) {

        $mpt = $this->getMasterProduct($orderDetail->ETIN, $orderDetail->ETIN_flag);

        $fulfilledBy = '';
        if ($mpt != null) {

            $availability =  strtolower(EtailerAvailabilityName($mpt->etailer_availability));
            if ($availability == 'stocked' || $availability == 'special order' || $availability == 'catch all') {
                $fulfilledBy = 'e-tailer';
            } else if ($availability == 'dropshipped') {
                $fulfilledBy = $mpt->current_supplier;
            }
        }

        $orderDetail->fulfilled_by = $fulfilledBy;
        $orderDetail->save();
    }

    // Crate Sub-Order
    public function createSubOrders($summary, $orderDetail) {

        $mpt = $this->getMasterProduct($orderDetail->ETIN, $orderDetail->ETIN_flag);
        if ($mpt != null) {

            $temp = $mpt->product_temperature;
            $subOrderNumber = "";
            if ($temp && strlen($temp) > 0) {
                $temp = strtolower($temp);
                switch(strtolower($orderDetail->fulfilled_by)) {
                    case "e-tailer":
                        if (str_contains($temp, 'frozen')) {
                            $subOrderNumber = $summary->etailer_order_number . '.001';
                        } else if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.002';
                        } else if (str_contains($temp, 'refrigerated')) {
                            $subOrderNumber = $summary->etailer_order_number . '.003';
                        } 
                        break;
                    case "dot":
                        if (str_contains($temp, 'frozen')) {
                            $subOrderNumber = $summary->etailer_order_number . '.004';
                        } else if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.005';
                        } else if (str_contains($temp, 'refrigerated')) {
                            $subOrderNumber = $summary->etailer_order_number . '.006';
                        } 
                        break;
                    case "kehe":
                        if (str_contains($temp, 'dry')) {
                            $subOrderNumber = $summary->etailer_order_number . '.006';
                        } 
                        break;
                    default:
                        break;
                }
            }
        }

        $orderDetail->sub_order_number = $subOrderNumber;
        $orderDetail->save();
        // UpdateOrderHistory([
		// 	'order_number' => $orderDetail->order_number,
        //     'sub_order_number' => $subOrderNumber,
		// 	'detail' => 'Sub Order #: '.$subOrderNumber .' has been Created',
		// 	'title' => 'Sub Order # Created',
        //     'update' => true
		// ]);			
    }

    private function getMasterProduct($etin, $etinFlag) {
        
        $mpt = null;
        if ($etinFlag == 0) {
            $mpt = DB::table('master_product')->where('ETIN', $etin)->first();
        } else if ($etinFlag == 1) {
            $mpt = DB::table('master_product')->where('product_listing_ETIN', $etin)->first();
        } else if ($etinFlag == 2) {
            $mpt = DB::table('master_product')->where('alternate_ETINs', 'like', '%' . $etin . '%')->first();
        }

        return $mpt;
    }

    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
    public function orderSummary(){
        return $this->belongsTo(OrderSummary::class,'order_number','etailer_order_number');
    }
    public function orderPackage(){
        return OrderPackage::where('ETIN',$this->ETIN)->where('order_id',$this->sub_order_number)->select('tracking_number','package_num','shipping_label_creation_time','ship_date')->first(); 
    }
    
    public function warehouse_info(){
        return $this->belongsTo(WareHouse::class,'warehouse','warehouses');
    }

    public function carrier(){
        return $this->belongsTo(Carrier::class,'carrier_id','id');
    }

    public function carrier_account(){
        return $this->belongsTo(CarrierAccounts::class,'carrier_account_id','id');
    }

    public function carrier_service(){
        return $this->belongsTo(ShippingServiceType::class,'service_type_id','id');
    }

    public function UpdateOrderStatus($order_id, $status_id, $user_id = null){
        $order_detail = OrderDetail::find($order_id);
        if($order_detail){
            $sub_order_number = $order_detail->sub_order_number;
            $order_number = $order_detail->order_number;
            $current_status_id = $order_detail->status;

            $order_summery_detail = OrderSummary::where('etailer_order_number',$order_number)->first();
            if($order_summery_detail){
                if(in_array($status_id,[1,2,9,10])){
                    $OD = OrderDetail::where('sub_order_number',$sub_order_number)->first();
                    if($OD){
                        if(in_array($OD->status,[4,12,3,11])){
                            $rs = $this->changeQty($sub_order_number,$user_id);
                            if($rs == "Error"){
                                return "Error";
                            }
                        }
                        
                    }
                    
                    OrderDetail::where('sub_order_number',$sub_order_number)->update([
                        'picker_id' => NULL
                    ]);
                }

                if(in_array($status_id,[1,2,9,10])){
                    OrderPackage::where('order_id',$sub_order_number)->delete();
                    OrderPickAndPack::where('sub_order_number',$sub_order_number)->delete();
                }

                if(in_array($status_id,[3,11])){
                    OrderPackage::where('order_id',$sub_order_number)->delete();
                    OrderPickAndPack::where('sub_order_number',$sub_order_number)->update([
                        'pack_qty' => NULL
                    ]);
                    
                    $ETIN = [];
                    $picked_orders = $this->GetPickedItems($sub_order_number);
                    
                    if($picked_orders){
                        foreach($picked_orders as $row_picked_order){
                            $ETIN[] = $row_picked_order['ETIN'];
                        }
                    }

                    $PackedETIN = OrderPickAndPack::where('sub_order_number',$sub_order_number)->get()->pluck('ETIN')->toArray();
                    $ETIN_DIFF = array_diff($PackedETIN, $ETIN);  
                    if($ETIN_DIFF){
                        OrderPickAndPack::whereIN('ETIN',$ETIN_DIFF)->where('sub_order_number',$sub_order_number)->delete();
                    }
                    

                }




                OrderDetail::where('sub_order_number',$sub_order_number)->update([
                    'status' => $status_id
                ]);

                UpdateOrderHistory([
                    'sub_order_number' => $sub_order_number,
                    'order_number' => $order_number,
                    'title' => 'Update Sub order Status',
                    'detail' => "Sub order # $sub_order_number status has changed to ".OrderDetailStatusName($status_id),
                    'user_id' => $user_id,
                    'extras' => json_encode([
                        'order_detail' => $order_detail,
                        'status_id' => $status_id
                    ])
                ]);
                $status = null;

                if(in_array($status_id,[1,2,9,10])){
                    $status = 1;
                }

                if($status_id == 3 || $status_id == 11){
                    $status = 4;
                }

                if($status_id == 4 || $status_id == 12){
                    $status = 6;
                }
                
                OrderSummary::where('etailer_order_number',$order_number)->update([
                    'order_status' => $status
                ]);

                UpdateOrderHistory([
                    'sub_order_number' => $sub_order_number,
                    'order_number' => $order_number,
                    'title' => 'Order Status',
                    'detail' => "Order # $order_number status has changed to ".OrderSummeryStatusName($status),
                    'user_id' => $user_id,
                    'extras' => json_encode([
                        'order_detail' => $order_detail,
                        'status_id' => $status_id
                    ])
                ]);
            }
        }
    }

    

    private function changeQty($sub_order_number,$user_id) {
        
        $picked_orders = $this->GetPickedItems($sub_order_number);
        if($picked_orders){
            foreach($picked_orders as $row_picked_order){
                $pro = MasterProduct::where('ETIN',$row_picked_order['ETIN'])->first();
                $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 1)
                                ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                
                Log::channel('Inventory')->info('Shelf Found: '.(isset($ms->id) ? $ms->id : ''));
                if(isset($ms) && ($ms->cur_qty >= $row_picked_order['pick_qty'])){
                    if(($ms->cur_qty + $row_picked_order['pick_qty']) < 0 ){
                        return "Error";
                    }
                }elseif($pro->parent_ETIN != ''){
                    $parent_info = MasterProduct::where('ETIN',$pro->parent_ETIN)->first();
                    $parent_shelf = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                            ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                            ->where('master_shelf.ETIN', $pro->parent_ETIN)->where('master_shelf.location_type_id', 1)
                            ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                    $unit_in_pack = (isset($parent_info->unit_in_pack) ? $parent_info->unit_in_pack : 0);
                    $Pro_cur_qty = isset($ms->cur_qty) ? $ms->cur_qty : 0;
                    $pack_qty = ($parent_shelf->cur_qty * $unit_in_pack) + $Pro_cur_qty;
                    $deducted_qty = $pack_qty + $row_picked_order['pick_qty'];
                    $new_parnet_pack = floor($deducted_qty/$unit_in_pack);
                    $new_child_qty = $deducted_qty%$unit_in_pack;
                    if($new_parnet_pack < 0){
                        return "Error";
                    }
                    if((isset($ms->id)) && ($new_child_qty > $ms->max_qty)){
                        $back_ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                        ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                        ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 2)
                        ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                        if($back_ms){
                                
                            $back_ms_starting_qty = $back_ms->cur_qty;
                            if(($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)) < 0){
                                return "Error";
                            }

                            if($ms->max_qty < 0){
                                return "Error";
                            }

                        }
                    }else if(isset($ms->id)){
                        if($new_child_qty < 0){
                            return "Error";
                        }
                    }else{
                        if($new_child_qty < 0){
                            return "Error";
                        }
                    }

                }else{
                    
                }

            }
        }

        if($picked_orders){
            foreach($picked_orders as $row_picked_order){
                $pro = MasterProduct::where('ETIN',$row_picked_order['ETIN'])->first();
                $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 1)
                                ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                
                Log::channel('Inventory')->info('Shelf Found: '.(isset($ms->id) ? $ms->id : ''));
                if(isset($ms) && ($ms->cur_qty >= $row_picked_order['pick_qty'])){
                    Log::channel('Inventory')->info('ETIN: '.$row_picked_order['ETIN'].' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($ms->cur_qty + $row_picked_order['pick_qty']).'');
                    
                    InventoryAdjustmentLog([
                        'ETIN' => $ms->ETIN,
                        'location' => $ms->address,
                        'starting_qty' => $ms->cur_qty,
                        'ending_qty' => ($ms->cur_qty + $row_picked_order['pick_qty']),
                        'total_change' => '+'.$row_picked_order['pick_qty'],
                        'user' => $user_id,
                        'reference' => 'OrderDetail',
                        'reference_value' => 'sub_order_number: '.$sub_order_number,
                        'reference_description' => 'Adding Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                    ]);
                    MasterShelf::where('id', $ms->id)->update(['cur_qty' => ($ms->cur_qty + $row_picked_order['pick_qty'])]);
                }elseif($pro->parent_ETIN != ''){
                    $parent_info = MasterProduct::where('ETIN',$pro->parent_ETIN)->first();
                    $parent_shelf = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                            ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                            ->where('master_shelf.ETIN', $pro->parent_ETIN)->where('master_shelf.location_type_id', 1)
                            ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                    $unit_in_pack = (isset($parent_info->unit_in_pack) ? $parent_info->unit_in_pack : 0);
                    $Pro_cur_qty = isset($ms->cur_qty) ? $ms->cur_qty : 0;
                    $pack_qty = ($parent_shelf->cur_qty * $unit_in_pack) + $Pro_cur_qty;
                    $deducted_qty = $pack_qty + $row_picked_order['pick_qty'];
                    $new_parnet_pack = floor($deducted_qty/$unit_in_pack);
                    $new_child_qty = $deducted_qty%$unit_in_pack;
                    Log::channel('Inventory')->info('ETIN: '.$parent_shelf->ETIN.' -- Location id: '.$parent_shelf->id.' --  Qty Before: '.$parent_shelf->cur_qty.' -- Qty After: '.($new_parnet_pack).'');
                    if(!isset($ms->id)){
                        $NewMS = new MasterShelf();
                        $NewMS->aisle_id = $parent_shelf->aisle_id;
                        $NewMS->bay_id = $parent_shelf->bay_id;
                        $NewMS->shelf = $parent_shelf->shelf;
                        $NewMS->slot = $parent_shelf->slot;
                        $NewMS->ETIN = $row_picked_order['ETIN'];
                        $NewMS->max_qty = 100;
                        $NewMS->address = $parent_shelf->address;
                        $NewMS->location_type_id = $parent_shelf->location_type_id;
                        $NewMS->parent_id = $parent_shelf->parent_id;
                        $NewMS->product_temp_id = $parent_shelf->product_temp_id;
                        $NewMS->save();
                    }

                    InventoryAdjustmentLog([
                        'ETIN' => $parent_shelf->ETIN,
                        'location' => $parent_shelf->address,
                        'starting_qty' => $parent_shelf->cur_qty,
                        'ending_qty' => ($new_parnet_pack),
                        'total_change' => ($parent_shelf->cur_qty - $new_parnet_pack),
                        'user' => $user_id,
                        'reference' => 'OrderDetail',
                        'reference_value' => 'sub_order_number: '.$sub_order_number,
                        'reference_description' => 'Updating Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                    ]);

                    MasterShelf::where('id', $parent_shelf->id)->update(['cur_qty' => $new_parnet_pack]);
                    if((isset($ms->id)) && ($new_child_qty > $ms->max_qty)){
                        $back_ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                        ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                        ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 2)
                        ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                        if($back_ms){
                            Log::channel('Inventory')->info('ETIN: '.$back_ms->ETIN.' -- Location id: '.$back_ms->id.' --  Qty Before: '.$back_ms->cur_qty.' -- Qty After: '.($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)).'');    
                            Log::channel('Inventory')->info('ETIN: '.$ms->ETIN.' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($ms->max_qty).'');    
                            $back_ms_starting_qty = $back_ms->cur_qty;
                            InventoryAdjustmentLog([
                                'ETIN' => $back_ms->ETIN,
                                'location' => $back_ms->address,
                                'starting_qty' => $back_ms_starting_qty,
                                'ending_qty' => ($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)),
                                'total_change' => '+'.($new_child_qty - $ms->max_qty),
                                'user' => $user_id,
                                'reference' => 'OrderDetail',
                                'reference_value' => 'sub_order_number: '.$sub_order_number,
                                'reference_description' => 'Updating Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                            ]);

                            MasterShelf::where('id', $back_ms->id)->update(['cur_qty' => ($back_ms->cur_qty + ($new_child_qty - $ms->max_qty))]);
                            InventoryAdjustmentLog([
                                'ETIN' => $ms->ETIN,
                                'location' => $ms->address,
                                'starting_qty' => $ms->cur_qty,
                                'ending_qty' => $ms->max_qty,
                                'total_change' => $ms->max_qty,
                                'user' => $user_id,
                                'reference' => 'OrderDetail',
                                'reference_value' => 'sub_order_number: '.$sub_order_number,
                                'reference_description' => 'Updating Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                            ]);
                            MasterShelf::where('id', $ms->id)->update(['cur_qty' => $ms->max_qty]);
                        }
                    }else if(isset($ms->id)){
                        Log::channel('Inventory')->info('ETIN: '.$ms->ETIN.' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($new_child_qty).'');
                        InventoryAdjustmentLog([
                            'ETIN' => $ms->ETIN,
                            'location' => $ms->address,
                            'starting_qty' => $ms->cur_qty,
                            'ending_qty' => $new_child_qty,
                            'total_change' => ($ms->cur_qty - $new_child_qty),
                            'user' => $user_id,
                            'reference' => 'OrderDetail',
                            'reference_value' => 'sub_order_number: '.$sub_order_number,
                            'reference_description' => 'Updating Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                        ]);
                        MasterShelf::where('id', $ms->id)->update(['cur_qty' => $new_child_qty]);
                    }else{
                        Log::channel('Inventory')->info('ETIN: '.$NewMS->ETIN.' -- Location id: '.$NewMS->id.' --  Qty Before: '.$NewMS->cur_qty.' -- Qty After: '.($new_child_qty).'');
                        InventoryAdjustmentLog([
                            'ETIN' => $NewMS->ETIN,
                            'location' => $NewMS->address,
                            'starting_qty' => $NewMS->cur_qty,
                            'ending_qty' => $new_child_qty,
                            'total_change' => ($NewMS->cur_qty - $new_child_qty),
                            'user' => $user_id,
                            'reference' => 'OrderDetail',
                            'reference_value' => 'sub_order_number: '.$sub_order_number,
                            'reference_description' => 'Updating Qty for ETIN while pickup order: UpdateOrderStatus->changeQty'
                        ]);

                        MasterShelf::where('id', $NewMS->id)->update(['cur_qty' => $new_child_qty]);
                    }

                }else{
                    
                }

            }
        }
        
        


        return "Success";
    }

    

    private function GetPickedItems($sub_order_number){
        Log::channel('Inventory')->info('===========GetPickedItems Start ===================================');
        $orders = OrderDetail::where('sub_order_number',$sub_order_number)->get();
        $picked_up_items = [];
        if($orders){
            foreach ($orders as $order) {
                Log::channel('Inventory')->info('------------------------');
                Log::channel('Inventory')->info('Product: '.$order->ETIN);
                $mpt = MasterProduct::where('ETIN', $order->ETIN)->first();
                if (!$mpt) {
                    Log::channel('WMS')->info('No records found in MPT for ETIN: ' . $order->ETIN);
                    continue;     
                }
                if (str_contains(strtolower($mpt->item_form_description), 'kit')) {
                    $kit_com = MasterProductKitComponents::select('master_product_kit_components.*')
                    ->where('master_product_kit_components.ETIN',$mpt->ETIN)->get();
                    if($kit_com){
                        foreach($kit_com as $key => $row_kit_components){
                            $mpt = $row_kit_components->component_product_details;
                            $quantity_fulfilled = $order->quantity_fulfilled *  $row_kit_components->qty;
                            $picked_up_items[] = [
                                'ETIN' => $row_kit_components->components_ETIN,
                                'parent_ETIN' => $row_kit_components->ETIN,
                                'sub_order_number' => $sub_order_number,
                                'pick_qty' => $quantity_fulfilled,
                                'quantity_ordered' => $order->quantity_ordered,
                                'warehouse' => $order->warehouse
                            ];

                            Log::channel('Inventory')->info('*****');
                            Log::channel('Inventory')->info('Kit Component ETIN: '.$row_kit_components->components_ETIN);
                            Log::channel('Inventory')->info('Sub-order Number: '.$sub_order_number);
                            Log::channel('Inventory')->info('Pick Qty: '.$quantity_fulfilled);

                        }
                    }
                }else{
                    $picked_up_items[] = [
                        'ETIN' => $order->ETIN,
                        'sub_order_number' => $sub_order_number,
                        'pick_qty' => $order->quantity_fulfilled,
                        'quantity_ordered' => $order->quantity_ordered,
                        'warehouse' => $order->warehouse
                    ];

                    Log::channel('Inventory')->info('ETIN: '.$order->ETIN);
                    Log::channel('Inventory')->info('Sub-order Number: '.$sub_order_number);
                    Log::channel('Inventory')->info('Pick Qty: '.$order->quantity_fulfilled);
                    
                }
            }
        }

        $groupped_items = _group_by($picked_up_items, 'ETIN');
        
        $new_picked_up_items = [];
        if($groupped_items){
            foreach($groupped_items as $row_GI){
                $pick_qty = 0;
                $quantity_ordered = 0;
                $parent_ETIN = [];
                $ETIN = '';
                $warehouse = '';
                if($row_GI){
                    foreach($row_GI as $row_GI_ETIN){
                        $pick_qty += $row_GI_ETIN['pick_qty'];
                        $quantity_ordered += $row_GI_ETIN['quantity_ordered'];
                        if(isset($row_GI_ETIN['parent_ETIN']) && $row_GI_ETIN['parent_ETIN'] != ''){
                            $parent_ETIN[] = $row_GI_ETIN['parent_ETIN'];
                        }
                        $ETIN = $row_GI_ETIN['ETIN'];
                        $warehouse = $row_GI_ETIN['warehouse'];
                    }
                }
                
                $new_picked_up_items[] = [
                    'ETIN' => $ETIN,
                    'parent_ETIN' => $parent_ETIN ? implode(',',$parent_ETIN) : NULL,
                    'sub_order_number' => $sub_order_number,
                    'pick_qty' => $pick_qty,
                    'quantity_ordered' => $quantity_ordered,
                    'warehouse' => $warehouse
                ];

            }
        }
        Log::channel('Inventory')->info('================GetPickedItems END==============================');
        return $new_picked_up_items;
        
    }

    public function GetAvailableQty($etin,$warehouse){
        $Qty = OrderDetail::whereIn('order_details.status',[1,2,9,10])->where('order_details.ETIN',$etin)->where('order_details.warehouse',$warehouse)->whereIn('order_status',[2,23])->leftJoin('order_summary',function($join){
            $join->on('order_summary.etailer_order_number','=','order_details.order_number');
        })->sum('quantity_ordered');

        return $Qty;
    }
    public function OrderDetailsStatus()
    {
        return $this->belongsTo(OrderDetailsStatus::class,'status','id');
    }


}
