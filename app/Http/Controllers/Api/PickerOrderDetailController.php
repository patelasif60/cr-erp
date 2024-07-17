<?php

namespace App\Http\Controllers\Api;

use DB;
use DNS1D;
use App\User;
use App\HotRoute;
use App\MasterShelf;
use App\PickedLotAndExp;
use App\OrderDetail;
use App\OrderSummary;
use App\MasterProduct;
use App\UpsZipZoneByWH;
use App\OrderPickAndPack;
use Illuminate\Http\Request;
use App\MasterProductKitComponents;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\NotificationRepository;
use App\Http\Resources\OrderDetail\OrderDetailResource;
use App\Http\Resources\OrderSummary\OrderSummaryResource;

class PickerOrderDetailController extends Controller
{
    public function __construct(NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->NotificationRepository = $NotificationRepository;
	}

    public function getPickSubOrders($pickerId)
    {
        $pickerRole = User::join('user_roles', 'users.role', '=', 'user_roles.id')
            ->where('users.id', $pickerId)->get(['user_roles.role', 'users.name']);

        if (!$pickerRole || !in_array($pickerRole[0]['role'],['WMS User','WMS Manager','Admin','Manager'])) {
            $response = ["error" => true, 'message' => 'User is not a picker'];
            return response($response, 400);
        }
        $orderDetail = OrderDetail::join('order_summary', 'order_summary.etailer_order_number', '=', 'order_details.order_number')->leftJoin('clients', 'clients.id', '=', 'order_summary.client_id')->where('picker_id',$pickerId)->whereIn('order_details.status',[2,10])->whereNotNull('sub_order_number')->orderBy('sub_order_number')->select('order_details.id','sub_order_number','order_source',DB::raw('DATE_FORMAT(order_summary.purchase_date,"%m-%d-%Y") as purchase_date'),'clients.company_name as customer_name')->groupBy('sub_order_number')->get(); 

        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $orderDetail];
        return response($response, 200);
    }
    public function getPickSubOrdersBatchwise($subOrderNumber)
    {
        $orderDetail = OrderDetail::join('master_product', 'master_product.ETIN', '=', 'order_details.ETIN')->where('sub_order_number',$subOrderNumber)->whereIn('order_details.status',[2,10])->orderBy('sub_order_number')->select('order_details.id','order_details.ETIN','product_listing_name','quantity_ordered','quantity_fulfilled')->with('product.kit_products','product.kit_products.component_product_details')->get();

        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $orderDetail];
        return response($response, 200);
    }
    public function pickSubOrdersBatchwiseStore(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        foreach($request->all() as $key => $val){
            $sub_order_number = $val['sub_order_number'];
            $checked = $val['checked'];
            if($checked){
                $picked_orders = $this->GetPickedItems($sub_order_number);
                if($picked_orders){
                    foreach($picked_orders as $row_picked_order){
                        $pro = MasterProduct::where('ETIN',$row_picked_order['ETIN'])->first();

                        $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                        ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                        ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 1)
                                        // ->where('cur_qty', '>=', $row_picked_order['pick_qty'])
                                        ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                        if(isset($ms) && ($ms->cur_qty >= $row_picked_order['pick_qty'])){

                        }elseif($pro->parent_ETIN != ''){
                            $parent_info = MasterProduct::where('ETIN',$pro->parent_ETIN)->first();
                            $parent_shelf = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                    ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                    ->where('cur_qty', '>', 0)
                                    ->where('master_shelf.ETIN', $pro->parent_ETIN)->where('master_shelf.location_type_id', 1)
                                    ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                            if (!$parent_shelf) {
                                $response = ["error" => true, 'message' => 'No Records found for Parent ETIN: '.$pro->parent_ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                return response($response, 400);
                            }
                            $unit_in_pack = (isset($parent_info->unit_in_pack) ? $parent_info->unit_in_pack : 0);
                            $Pro_cur_qty = isset($ms->cur_qty) ? $ms->cur_qty : 0;
                            $pack_qty = ($parent_shelf->cur_qty * $unit_in_pack) + $Pro_cur_qty;
                            if($pack_qty < $row_picked_order['pick_qty']){
                                $response = ["error" => true, 'message' => 'We unable to fulfil quantity for product: '.$row_picked_order['ETIN'].' --> '.$parent_shelf->ETIN.'  ('.$sub_order_number.')', "data" => $row_picked_order];
                                return response($response, 400);
                            }

                            $pack_qty = ($parent_shelf->cur_qty * $unit_in_pack) + $Pro_cur_qty;
                            $deducted_qty = $pack_qty - $row_picked_order['pick_qty'];
                            $new_parnet_pack = floor($deducted_qty/$unit_in_pack);
                            if($new_parnet_pack < 0){
                                $response = ["error" => true, 'message' => 'We unable to fulfil quantity for parent product: '.$parent_shelf->ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                return response($response, 400);
                            }
                            $new_child_qty = $deducted_qty%$unit_in_pack;
                            if((isset($ms->id)) && ($new_child_qty > $ms->max_qty)){
                                $back_ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 2)
                                ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                                if(!$back_ms){
                                    $response = ["error" => true, 'message' => 'Max Qty violation error (No Back stock location found for the product '.$row_picked_order['ETIN'].')'];
                                    return response($response, 400);
                                }else{
                                    if(($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)) < 0){
                                        $response = ["error" => true, 'message' => 'We unable to fulfil quantity for child product: '.$back_ms->ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                        return response($response, 400);
                                    }

                                    if($ms->max_qty < 0){
                                        $response = ["error" => true, 'message' => 'We unable to fulfil quantity for child product: '.$ms->ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                        return response($response, 400);
                                    }
                                }

                            }else if(isset($ms->id)){
                                if($new_child_qty < 0){
                                    $response = ["error" => true, 'message' => 'We unable to fulfil quantity for child product: '.$ms->ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                    return response($response, 400);
                                }
        
                            }else{
                                if($new_child_qty < 0){
                                    $response = ["error" => true, 'message' => 'We unable to fulfil quantity for child product: '.$ms->ETIN.' ('.$sub_order_number.')', "data" => $row_picked_order];
                                    return response($response, 400);
                                }
                            }

                        }else{
                            $response = ["error" => true, 'message' => 'Could not find enough qty for the '.$row_picked_order['ETIN'].' ('.$sub_order_number.')', "data" => $row_picked_order];
                            return response($response, 400);
                        }

                    }
                }
                
            }
        }

        
        foreach($request->all() as $key => $val){
            $sub_order_number = $val['sub_order_number'];
            $checked = $val['checked'];
            $order = OrderDetail::where('sub_order_number',$sub_order_number)->first();
            if($checked){
                $orderDetails = OrderDetail::where('sub_order_number',$sub_order_number)->first();
                $orderSummary = $orderDetails->orderSummary;
                //$transitDay = $this->getTransitDay($orderSummary->ship_to_state, $orderSummary->ship_to_zip, $orderDetails->warehouse);
                $transitDay = $orderDetails->transit_days;
                $transitDay == -1 ? 0 : $transitDay;
                
                OrderDetail::where('sub_order_number',$sub_order_number)->where('status',2)->update(['status'=>3, 'transit_days' => $transitDay]);
                OrderDetail::where('sub_order_number',$sub_order_number)->where('status',10)->update(['status'=>11, 'transit_days' => $transitDay]);
                $order = OrderDetail::where('sub_order_number',$sub_order_number)->first();
                $this->NotificationRepository->SendOrderNotification([
                    'subject' => "Order Status: Picked",
                    'body' => 'Sub Order No: '.$order->sub_order_number .' has been Picked',
                    'user_id' => $order->picker_id,
                    'order_number' => $order->order_number
                ]); 
                UpdateOrderHistory([
                    'order_number' => $order->order_number,
                    'sub_order_number' => $order->sub_order_number,
                    'detail' => 'Sub order Number #: '.$order->sub_order_number.' has been Picked',
                    'title' => 'Sub Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order)
                ]);
                UserLogs([
					'user_id' => $user_id,
					'action' => 'Click',
					'task' => 'Pick',
					'details' => 'Sub order # '.$sub_order_number.' has been picked',
					'type' => 'CWMS',
                    'sub_order_number' => $sub_order_number,
                    'etailer_order_number' => $orderSummary->etailer_order_number,
                    'channel_order_number' => $orderSummary->channel_order_number,
                    'client_order_number' => $orderSummary->sa_order_number
				]);

                $this->UpdateOrderSummeryStatus($sub_order_number,$user_id);
                $this->changeQty($sub_order_number,$user_id);
            }else{
                OrderDetail::where('sub_order_number',$sub_order_number)->whereIn('status',[1,2,3,4,6])->update(['status'=>1,'picker_id'=>null]);
                OrderDetail::where('sub_order_number',$sub_order_number)->whereIn('status',[9,10,11,12,13])->update(['status'=>9,'picker_id'=>null]);
                UpdateOrderHistory([
                    'order_number' => $order->order_number,
                    'sub_order_number' => $order->sub_order_number,
                    'detail' => 'Sub order Number #: '.$order->sub_order_number.' Status moved back to Ready to pick and picker has been removed',
                    'title' => 'Sub Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order)
                ]);
            }
        }
        $response = ["error" => false, 'message' => 'Data updated successfully'];
        return response($response, 200);
    }


    
    public function pickSubOrderFullfillStore(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        foreach($request->all() as $key=>$val){
            $orderDetail = OrderDetail::find($val['id']);
            if($orderDetail->quantity_ordered < $val['quantity_fulfilled']){
                $response = ["error" => true, 'message' => 'Quantity fulfilled is greter than quantity ordered'];
                return response($response, 406);
            }
        }
        $OS = NULL;
        foreach($request->all() as $key=>$val){
            $orderDetail = OrderDetail::find($val['id']);
            $orderDetail->quantity_fulfilled =   $val['quantity_fulfilled'];
            $orderDetail->save();
            
            UpdateOrderHistory([
                'order_number' => $orderDetail->order_number,
                'sub_order_number' => $orderDetail->sub_order_number,
                'detail' => 'Sub order Number #: '.$orderDetail->sub_order_number.' Fullfilled Qty Updated for ETIN '.$orderDetail->ETIN,
                'title' => 'Sub Order Qty Updated',
                'user_id' => $user_id,
                'reference' => 'API',
                'extras' => json_encode($orderDetail)
            ]);
            
        }
        $orderSummary = $orderDetail->orderSummary;
        UserLogs([
            'user_id' => $user_id,
            'action' => 'Click',
            'task' => 'Pick',
            'details' => 'Sub order #'.$orderDetail->sub_order_number.' item qty updated',
            'type' => 'CWMS',
            'sub_order_number' => $orderDetail->sub_order_number,
            'etailer_order_number' => $orderSummary->etailer_order_number,
            'channel_order_number' => $orderSummary->channel_order_number,
            'client_order_number' => $orderSummary->sa_order_number
        ]);
        $response = ["error" => false, 'message' => 'Data updated successfully'];
        return response($response, 200);
    }
    public function printPickerSubOrdersOLD($pickerId){
         $orderDetail = OrderDetail::where('picker_id',$pickerId)->whereIn('status',[1,2,9,10])->groupBy('sub_order_number')->get();
        if (count($orderDetail) > 0) {
            $data = OrderDetailResource::collection($orderDetail);
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $data];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        } 
    }

    public function printPickerSubOrders($pickerId){
         $orderDetail = OrderDetail::where('picker_id',$pickerId)->whereIn('status',[1,2,9,10])->groupBy('sub_order_number')->get();
        
         $result = [];
         if($orderDetail){
            foreach($orderDetail as $key => $row){
                $product = [];
                $all_kit_products = [];
                $row_orders = OrderDetail::where('sub_order_number',$row->sub_order_number)->get();
                if($row_orders){
                    foreach($row_orders as $row_sub_pro){
                        $pro = $row_sub_pro->product;
                        $kit_products = $pro->kit_products;
                        $kits = [];
                        if(count($kit_products) > 0){
                            foreach($kit_products as $row_kit_pro){
                                $kit_pro_details = $row_kit_pro->component_product_details;
                                $shelf_address = $pro->masterShelf->first();
                                if($shelf_address){
                                    $shelf_address = $shelf_address->address;
                                }else{
                                    $shelf_address = '';
                                }

                                $kit = [
                                    'ETIN' => $kit_pro_details->ETIN,
                                    'upc' => $kit_pro_details->upc,
                                    'product_listing_name' => $kit_pro_details->product_listing_name,
                                    'shelf_address' => $shelf_address,
                                    'quantity_fulfilled' => $row_sub_pro->quantity_fulfilled * $row_kit_pro->qty,
                                    'quantity_ordered' => $row_sub_pro->quantity_fulfilled * $row_kit_pro->qty,
                                    'parent_ETIN' => $row_sub_pro->ETIN,
                                    'kit_products' => [],
                                    'parent_upc' => $row_sub_pro->upc,
                                    'quantity_fulfilled' => $row_sub_pro->quantity_fulfilled,
                                    'kit_qty' => $row_kit_pro->qty
                                ];
                                $all_kit_products[] = $kit;
                                $kits[] = $kit;
                                $product[] = $kit;

                            }
                        }else{
                            
                            $product[] = [
                                'ETIN' => $pro->ETIN,
                                'upc' => $pro->upc,
                                'product_listing_name' => $pro->product_listing_name,
                                'shelf_address' => $pro->masterShelf->first() ? $pro->masterShelf->first()->address : '',
                                'quantity_ordered' => $row_sub_pro->quantity_ordered,
                                'quantity_fulfilled' => $row_sub_pro->quantity_fulfilled,
                                'kit_products' => $kits
                            ];
                        }

                        
                            
                        
                        
                        
                    }
                }

                $result[$key]['barcodeString'] = DNS1D::getBarcodePNG($row->sub_order_number, 'C128',2,83,array(1,1,0), true);
                $result[$key]['id'] = $row->id;
                $result[$key]['summery_detail'] = new OrderSummaryResource($row->orderSummary);
                $result[$key]['sub_order_number'] = $row->sub_order_number;
                $result[$key]['transit_days'] = $row->transit_days;
                $result[$key]['product'] = $product;
                $result[$key]['kit_products'] = $all_kit_products;
                $result[$key]['is_hot_route'] = $row->hot_route == 1 ? 'Yes' : 'No';
            }
         }
        if (count($orderDetail) > 0) {
            //$data = OrderDetailResource::collection($orderDetail);
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $result];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        } 
    }

    public function printSubOrders($sub_order_number){
    //     $orderDetail = OrderDetail::where('sub_order_number',$sub_order_number)->groupBy('sub_order_number')->get();
    //    //  ->whereIn('status',[1,2])
    //     $result = [];
    //     if($orderDetail){
    //        foreach($orderDetail as $key => $row){
    //            $product = [];
    //            $row_orders = OrderPickAndPack::where('sub_order_number',$row->sub_order_number)->get();
    //            if($row_orders){
    //                foreach($row_orders as $row_sub_pro){
    //                    $pro = $row_sub_pro->product;
    //                    $picked_lot_and_exp = PickedLotAndExp::where('sub_order',$row->sub_order_number)->where('Main_ETIN',$pro->ETIN)->whereNull('for_the_log')->orderBy('exp','ASC')->get();
    //                    $picked_lot_and_exp->transform(function ($record) {
    //                         $record->date_column = \Carbon\Carbon::createFromFormat('Y-m-d', $record->exp);
    //                         return $record;
    //                     });
    //                     $picked_lot_and_exp = $picked_lot_and_exp->sortBy('date_column');
    //                    $product[] = [
    //                        'ETIN' => $pro->ETIN,
    //                        'upc' => $pro->upc,
    //                        'product_listing_name' => $pro->product_listing_name,
    //                        'shelf_address' => $pro->masterShelf->first() ? $pro->masterShelf->first()->address : '',
    //                        'quantity_ordered' => $row_sub_pro->quantity_ordered,
    //                        'quantity_fulfilled' => $row_sub_pro->pick_qty,
    //                        'parent_ETIN' => $row_sub_pro->parent_ETIN,
    //                        'picked_lot_and_exp' => $picked_lot_and_exp
    //                    ];
    //                }
    //            }

               
    //             usort($product, function ($a, $b) {
    //                 // Extract the shelf addresses from the array elements
    //                 $shelfAddressA = $a['shelf_address'];
    //                 $shelfAddressB = $b['shelf_address'];
                
    //                 // Compare the shelf addresses
    //                 return strcmp($shelfAddressA, $shelfAddressB);
    //             });
               
    //            $result[$key]['barcodeString'] = DNS1D::getBarcodePNG($row->sub_order_number, 'C128',2,83,array(1,1,0), true);
    //            $result[$key]['id'] = $row->id;
    //            $result[$key]['summery_detail'] = new OrderSummaryResource($row->orderSummary);
    //            $result[$key]['sub_order_number'] = $row->sub_order_number;
    //            $result[$key]['product'] = $product;                
    //            $result[$key]['is_hot_route'] = $row->hot_route == 1 ? 'Yes' : 'No';
    //        }

    //        usort($result, function ($a, $b) {
    //             // Extract the shelf addresses from the first product in each element
    //             $shelfAddressA = $a['product'][0]['shelf_address'];
    //             $shelfAddressB = $b['product'][0]['shelf_address'];
        
    //             // Compare the shelf addresses
    //             return strcmp($shelfAddressA, $shelfAddressB);
    //         });
    //     }

    
    
    $orderDetail = OrderDetail::where('sub_order_number', $sub_order_number)->groupBy('sub_order_number')->get();
$result = [];

if ($orderDetail) {
    foreach ($orderDetail as $key => $row) {
        $product = [];
        $row_orders = OrderPickAndPack::where('sub_order_number', $row->sub_order_number)->get();
        
        if ($row_orders) {
            foreach ($row_orders as $row_sub_pro) {
                $pro = $row_sub_pro->product;
                $picked_lot_and_exp = PickedLotAndExp::where('sub_order', $row->sub_order_number)
                    ->where('Main_ETIN', $pro->ETIN)
                    ->whereNull('for_the_log')
                    ->orderBy('exp', 'ASC')
                    ->get();
                
                $picked_lot_and_exp->transform(function ($record) {
                    $record->date_column = \Carbon\Carbon::createFromFormat('Y-m-d', $record->exp);
                    return $record;
                });
                
                $picked_lot_and_exp = $picked_lot_and_exp->sortBy('date_column');
                
                $product[] = [
                    'ETIN' => $pro->ETIN,
                    'upc' => $pro->upc,
                    'product_listing_name' => $pro->product_listing_name,
                    'shelf_address' => $pro->masterShelf->first() ? $pro->masterShelf->first()->address : '',
                    'quantity_ordered' => $row_sub_pro->quantity_ordered,
                    'quantity_fulfilled' => $row_sub_pro->pick_qty,
                    'parent_ETIN' => $row_sub_pro->parent_ETIN,
                    'picked_lot_and_exp' => $picked_lot_and_exp
                ];
            }
        }

        usort($product, function ($a, $b) {
            // Extract the parts of shelf addresses
            $partsA = explode(':', $a['shelf_address']);
            $partsB = explode(':', $b['shelf_address']);

            // Compare the parts of shelf addresses
            $count = min(count($partsA), count($partsB));
            for ($i = 0; $i < $count; $i++) {
                $partA = $partsA[$i];
                $partB = $partsB[$i];

                // Compare the parts as numbers if they are numeric,
                // otherwise, compare them as strings
                if (is_numeric($partA) && is_numeric($partB)) {
                    if ($partA != $partB) {
                        return $partA - $partB;
                    }
                } else {
                    if ($partA != $partB) {
                        return strcmp($partA, $partB);
                    }
                }
            }

            // If all the compared parts are the same, compare the lengths of the addresses
            return count($partsA) - count($partsB);
        });

        $result[$key]['barcodeString'] = DNS1D::getBarcodePNG($row->sub_order_number, 'C128', 2, 83, array(1, 1, 0), true);
        $result[$key]['id'] = $row->id;
        $result[$key]['summery_detail'] = new OrderSummaryResource($row->orderSummary);
        $result[$key]['sub_order_number'] = $row->sub_order_number;
        $result[$key]['product'] = $product;
        $result[$key]['is_hot_route'] = $row->hot_route == 1 ? 'Yes' : 'No';
    }

    usort($result, function ($a, $b) {
        // Extract the parts of shelf addresses from the first product in each element
        $partsA = explode(':', $a['product'][0]['shelf_address']);
        $partsB = explode(':', $b['product'][0]['shelf_address']);

        // Compare the parts of shelf addresses
        $count = min(count($partsA), count($partsB));
        for ($i = 0; $i < $count; $i++) {
            $partA = $partsA[$i];
            $partB = $partsB[$i];

            // Compare the parts as numbers if they are numeric,
            // otherwise, compare them as strings
            if (is_numeric($partA) && is_numeric($partB)) {
                if ($partA != $partB) {
                    return $partA - $partB;
                }
            } else {
                if ($partA != $partB) {
                    return strcmp($partA, $partB);
                }
            }
        }

        // If all the compared parts are the same, compare the lengths of the addresses
        return count($partsA) - count($partsB);
    });
}

       if (count($orderDetail) > 0) {
           //$data = OrderDetailResource::collection($orderDetail);
           $response = ["error" => false, 'message' => 'Data found successfully',"data" => $result];
           return response($response, 200);
       }
       else{
           $response = ["error" => true, "message" => 'Data not found!'];
           return response($response, 400);
       } 
   }

    public function OLDprintSubOrders($sub_order_number){
         $orderDetail = OrderDetail::where('sub_order_number',$sub_order_number)->groupBy('sub_order_number')->get();
        //  ->whereIn('status',[1,2])
         $result = [];
         if($orderDetail){
            foreach($orderDetail as $key => $row){
                $product = [];
                $row_orders = OrderDetail::with('product','product.kit_products')->where('sub_order_number',$row->sub_order_number)->get();
                if($row_orders){
                    foreach($row_orders as $row_sub_pro){
                        $pro = $row_sub_pro->product;
                        $kit_products = $pro->kit_products;
                        $pro_address = $pro->masterShelf->first();
                        $product[] = [
                            'ETIN' => $pro->ETIN,
                            'upc' => $pro->upc,
                            'product_listing_name' => $pro->product_listing_name,
                            'shelf_address' => $pro_address ? $pro_address->address : '',
                            'quantity_ordered' => count($kit_products) > 0 ? '' : $row_sub_pro->quantity_ordered,
                            'quantity_fulfilled' => count($kit_products) > 0 ? '' : $row_sub_pro->quantity_fulfilled
                        ];
                        if(isset($kit_products)){
                            foreach($kit_products as $row_kit_pro){
                                $kit_pro = MasterProduct::where('ETIN',$row_kit_pro->components_ETIN)->first();
                                $kit_address = $kit_pro->masterShelf->first();
                                $product[] = [
                                    'ETIN' => $kit_pro->ETIN,
                                    'upc' => $kit_pro->upc,
                                    'product_listing_name' => $kit_pro->product_listing_name,
                                    'shelf_address' => $kit_address ? $kit_address->address : '',
                                    'quantity_ordered' => $row_sub_pro->quantity_ordered * $row_kit_pro->qty,
                                    'quantity_fulfilled' => $row_sub_pro->quantity_fulfilled * $row_kit_pro->qty
                                ];
                            }
                        }

                    }
                }

                $result[$key]['barcodeString'] = DNS1D::getBarcodePNG($row->sub_order_number, 'C128',2,83,array(1,1,0), true);
                $result[$key]['id'] = $row->id;
                $result[$key]['summery_detail'] = new OrderSummaryResource($row->orderSummary);
                $result[$key]['sub_order_number'] = $row->sub_order_number;
                $result[$key]['product'] = $product;                
                $result[$key]['is_hot_route'] = $row->hot_route == 1 ? 'Yes' : 'No';
            }
         }
        if (count($orderDetail) > 0) {
            //$data = OrderDetailResource::collection($orderDetail);
            $response = ["error" => false, 'message' => 'Data found successfully',"data" => $result];
            return response($response, 200);
        }
        else{
            $response = ["error" => true, "message" => 'Data not found!'];
            return response($response, 400);
        } 
    }

    public function printNonPickupOrders($sub_order_number){
        $orderDetail = OrderDetail::where('order_number',$sub_order_number)->whereNotNull('sub_order_number')->groupBy('sub_order_number')->get();
       //  ->whereIn('status',[1,2])
        $result = [];
        if($orderDetail){
           foreach($orderDetail as $key => $row){
               $product = [];
               $row_orders = OrderDetail::where('sub_order_number',$row->sub_order_number)->get();
               if($row_orders){
                   foreach($row_orders as $row_sub_pro){
                       $pro = $row_sub_pro->product;
                       $product[] = [
                           'ETIN' => $pro->ETIN,
                           'upc' => $pro->upc,
                           'product_listing_name' => $pro->product_listing_name,
                           'shelf_address' => $pro->masterShelf->first() ? $pro->masterShelf->first()->address : '',
                           'quantity_ordered' => $row_sub_pro->quantity_ordered,
                           'quantity_fulfilled' => $row_sub_pro->quantity_fulfilled
                       ];
                   }
               }

               $result[$key]['barcodeString'] = DNS1D::getBarcodePNG($row->sub_order_number, 'C128',2,83,array(1,1,0), true);
               $result[$key]['id'] = $row->id;
               $result[$key]['summery_detail'] = new OrderSummaryResource($row->orderSummary);
               $result[$key]['sub_order_number'] = $row->sub_order_number;
               $result[$key]['product'] = $product;
               $result[$key]['is_hot_route'] = $row->hot_route == 1 ? 'Yes' : 'No';
           }
        }
       if (count($orderDetail) > 0) {
           //$data = OrderDetailResource::collection($orderDetail);
           $response = ["error" => false, 'message' => 'Data found successfully',"data" => $result];
           return response($response, 200);
       }
       else{
           $response = ["error" => true, "message" => 'Data not found!'];
           return response($response, 400);
       } 
   }

    private function UpdateOrderSummeryStatus($sub_order_number,$user_id){
        $order_info = OrderDetail::select('order_number')->where('sub_order_number',$sub_order_number)->first();
        if($order_info){
            // $total_orders = OrderDetail::where('order_number',$order_info->order_number)->count();
            // $picked_orders = OrderDetail::where('order_number',$order_info->order_number)->where('status','>=',3)->count();
            // if($total_orders == $picked_orders){
                OrderSummary::where('etailer_order_number',$order_info->order_number)->update([
                    'order_status' => 4
                ]);
                UpdateOrderHistory([
                    'order_number' => $order_info->order_number,
                    'sub_order_number' => $order_info->sub_order_number,
                    'detail' => 'Order Number #: '.$order_info->order_number.' has been picked',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order_info)
                ]);
            // }
        }
    }

    private function changeQty($sub_order_number,$user_id) {
        
        $picked_orders = $this->GetPickedItems($sub_order_number);
        
        if($picked_orders){
            foreach($picked_orders as $row_picked_order){
                $pro = MasterProduct::where('ETIN',$row_picked_order['ETIN'])->first();
                $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                                ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                                // ->where('cur_qty', '>=', $row_picked_order['pick_qty'])
                                ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 1)
                                ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                
                Log::channel('Inventory')->info('Shelf Found: '.(isset($ms->id) ? $ms->id : ''));
                if(isset($ms) && ($ms->cur_qty >= $row_picked_order['pick_qty'])){
                    Log::channel('Inventory')->info('ETIN: '.$row_picked_order['ETIN'].' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($ms->cur_qty - $row_picked_order['pick_qty']).'');
                    MasterShelf::where('id', $ms->id)->update(['cur_qty' => ($ms->cur_qty - $row_picked_order['pick_qty'])]);
                    $PLAE = new MasterShelf;
                    $PLAE->PickExpANdLot([
                        'id' => $ms->id,
                        'qty' => $row_picked_order['pick_qty'],
                        'sub_order' => $sub_order_number,
                        'Main_ETIN' => $row_picked_order['ETIN']
                    ]);
                    InventoryAdjustmentLog([
                        'ETIN' => $row_picked_order['ETIN'],
                        'location' => $ms->address,
                        'starting_qty' => $ms->cur_qty,
                        'ending_qty' => ($ms->cur_qty - $row_picked_order['pick_qty']),
                        'total_change' => ($ms->cur_qty - $row_picked_order['pick_qty']),
                        'user' => $user_id,
                        'reference' => 'Pick Order',
                        'reference_value' => 'sub_order_number: '.$sub_order_number,
                        'reference_description' => 'Deducting Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                    ]);
                }elseif($pro->parent_ETIN != ''){
                    $parent_info = MasterProduct::where('ETIN',$pro->parent_ETIN)->first();
                    
                    $parent_shelf = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                            ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                            ->where('master_shelf.ETIN', $pro->parent_ETIN)->where('master_shelf.location_type_id', 1)
                            ->where('cur_qty', '>', 0)
                            ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                    $unit_in_pack = (isset($parent_info->unit_in_pack) ? $parent_info->unit_in_pack : 0);
                    $Pro_cur_qty = isset($ms->cur_qty) ? $ms->cur_qty : 0;
                    $pack_qty = ($parent_shelf->cur_qty * $unit_in_pack) + $Pro_cur_qty;
                    $deducted_qty = $pack_qty - $row_picked_order['pick_qty'];
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

                    MasterShelf::where('id', $parent_shelf->id)->update(['cur_qty' => $new_parnet_pack]);
                    $parentOldNew = $parent_shelf->cur_qty - $new_parnet_pack;
                    
                    $ids_and_qty = [];
                    $ids_and_qty[] = [
                        'id' => $parent_shelf->id,
                        'qty' => $parentOldNew,
                        'before' => $parent_shelf->cur_qty,
                        'after' => $new_parnet_pack,
                        'backstock' => 0,
                        'main' => 1,
                        'unit_in_pack' => $unit_in_pack
                    ];
                    InventoryAdjustmentLog([
                        'ETIN' => $parent_shelf->ETIN,
                        'location' => $parent_shelf->address,
                        'starting_qty' => $parent_shelf->cur_qty,
                        'ending_qty' => $new_parnet_pack,
                        'total_change' => ($new_parnet_pack),
                        'user' => $user_id,
                        'reference' => 'Pick Order',
                        'reference_value' => 'sub_order_number: '.$sub_order_number,
                        'reference_description' => 'Updating Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                    ]);

                    if((isset($ms->id)) && ($new_child_qty > $ms->max_qty)){
                        $back_ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                        ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                        ->where('master_shelf.ETIN', $row_picked_order['ETIN'])->where('master_shelf.location_type_id', 2)
                        ->where('warehouses.warehouses', $row_picked_order['warehouse'])->first(['master_shelf.*']);
                        if($back_ms){
                            Log::channel('Inventory')->info('ETIN: '.$back_ms->ETIN.' -- Location id: '.$back_ms->id.' --  Qty Before: '.$back_ms->cur_qty.' -- Qty After: '.($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)).'');    
                            Log::channel('Inventory')->info('ETIN: '.$ms->ETIN.' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($ms->max_qty).'');    
                            MasterShelf::where('id', $back_ms->id)->update(['cur_qty' => ($back_ms->cur_qty + ($new_child_qty - $ms->max_qty))]);
                            MasterShelf::where('id', $ms->id)->update(['cur_qty' => $ms->max_qty]);
                            $ids_and_qty[] = [
                                'id' => $back_ms->id,
                                'qty' => ($new_child_qty - $ms->max_qty),
                                'before' => $back_ms->cur_qty,
                                'after' => ($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)),
                                'backstock' => 1,
                                'main' => 0,
                                'unit_in_pack' => 0
                            ];
                            $ids_and_qty[] = [
                                'id' => $ms->id,
                                'qty' => $ms->max_qty,
                                'before' => $ms->cur_qty,
                                'after' => $ms->max_qty,
                                'backstock' => 0,
                                'main' => 0,
                                'unit_in_pack' => 0
                            ];
                            InventoryAdjustmentLog([
                                'ETIN' => $ms->ETIN,
                                'location' => $ms->address,
                                'starting_qty' => $ms->cur_qty,
                                'ending_qty' => $ms->max_qty,
                                'total_change' => ($ms->max_qty),
                                'user' => $user_id,
                                'reference' => 'Pick Order',
                                'reference_value' => 'sub_order_number: '.$sub_order_number,
                                'reference_description' => 'Updating Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                            ]);

                            InventoryAdjustmentLog([
                                'ETIN' => $back_ms->ETIN,
                                'location' => $back_ms->address,
                                'starting_qty' => $back_ms->cur_qty,
                                'ending_qty' => ($back_ms->cur_qty + ($new_child_qty - $ms->max_qty)),
                                'total_change' => (($back_ms->cur_qty + ($new_child_qty - $ms->max_qty))),
                                'user' => $user_id,
                                'reference' => 'Pick Order',
                                'reference_value' => 'sub_order_number: '.$sub_order_number,
                                'reference_description' => 'Updating Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                            ]);


                        }
                    }else if(isset($ms->id)){
                        Log::channel('Inventory')->info('ETIN: '.$ms->ETIN.' -- Location id: '.$ms->id.' --  Qty Before: '.$ms->cur_qty.' -- Qty After: '.($new_child_qty).'');
                        MasterShelf::where('id', $ms->id)->update(['cur_qty' => $new_child_qty]);
                        $ids_and_qty[] = [
                            'id' => $ms->id,
                            'qty' => $new_child_qty,
                            'before' => $ms->cur_qty,
                            'after' => $new_child_qty,
                            'backstock' => 0,
                            'main' => 0,
                            'unit_in_pack' => 0
                        ];
                        InventoryAdjustmentLog([
                            'ETIN' => $ms->ETIN,
                            'location' => $ms->address,
                            'starting_qty' => $ms->cur_qty,
                            'ending_qty' => ($new_child_qty),
                            'total_change' => ($new_child_qty),
                            'user' => $user_id,
                            'reference' => 'Pick Order',
                            'reference_value' => 'sub_order_number: '.$sub_order_number,
                            'reference_description' => 'Updating Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                        ]);

                    }else{
                        Log::channel('Inventory')->info('ETIN: '.$NewMS->ETIN.' -- Location id: '.$NewMS->id.' --  Qty Before: '.$NewMS->cur_qty.' -- Qty After: '.($new_child_qty).'');
                        MasterShelf::where('id', $NewMS->id)->update(['cur_qty' => $new_child_qty]);
                        $ids_and_qty[] = [
                            'id' => $NewMS->id,
                            'qty' => $new_child_qty,
                            'before' => $NewMS->cur_qty,
                            'after' => $new_child_qty,
                            'backstock' => 0,
                            'main' => 0,
                            'unit_in_pack' => 0
                        ];
                        InventoryAdjustmentLog([
                            'ETIN' => $NewMS->ETIN,
                            'location' => $NewMS->address,
                            'starting_qty' => $NewMS->cur_qty,
                            'ending_qty' => ($new_child_qty),
                            'total_change' => ($new_child_qty),
                            'user' => $user_id,
                            'reference' => 'Pick Order',
                            'reference_value' => 'sub_order_number: '.$sub_order_number,
                            'reference_description' => 'Updating Qty for ETIN while pickup order: pickSubOrdersBatchwiseStore->changeQty'
                        ]);
                    }
                    $PEALWP = new MasterShelf;
                    $PEALWP->PickExpAndLotWithParent($ids_and_qty,$sub_order_number,$row_picked_order['ETIN'],$row_picked_order['pick_qty']);
                    

                }else{
                    
                }

            }
        }
        
        if($picked_orders){
            foreach($picked_orders as $row_picked_up_item){
                $OrderPickAndPack = new OrderPickAndPack();
                $OrderPickAndPack->ETIN = isset($row_picked_up_item['ETIN']) ? $row_picked_up_item['ETIN'] : NULL;
                $OrderPickAndPack->parent_ETIN = isset($row_picked_up_item['parent_ETIN']) ? $row_picked_up_item['parent_ETIN'] : NULL;
                $OrderPickAndPack->sub_order_number = isset($row_picked_up_item['sub_order_number']) ? $row_picked_up_item['sub_order_number'] : NULL;
                $OrderPickAndPack->pick_qty = isset($row_picked_up_item['pick_qty']) ? $row_picked_up_item['pick_qty'] : NULL;
                $OrderPickAndPack->quantity_ordered = isset($row_picked_up_item['quantity_ordered']) ? $row_picked_up_item['quantity_ordered'] : NULL;
                $OrderPickAndPack->save();
            }
        }


        return "Success";
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


    private function GetPickedItems($sub_order_number){
        Log::channel('Inventory')->info('===========GetPickedItems Start ===================================');
        $orders = OrderDetail::where('sub_order_number',$sub_order_number)->whereIn('status',[2,3,10,11])->get();
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


}
