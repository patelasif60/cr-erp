<?php

namespace App\Http\Controllers\Api;

use App\Client;
use App\OrderDetail;
use App\OrderSummary;
use App\OrderPackage;
use App\PickerOrderMap;
use App\PalletHeaderNonPersonOrder;
use App\PalletDetailsNonPersonOrder;
use App\MasterShelf;
use App\OrderDetailsStatus;
use App\Http\Resources\OrderPallets\OrderPalletsResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use DB;

class OrderManagementController extends Controller
{

    public function getAllWarehouseOrders(Request $request) {
        $toReturn = [];
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $select_array = [
                'sub_order_number' => 'order_details.sub_order_number',
                'fulfilled_by' => 'order_details.fulfilled_by',
                'qty_ordered' => 'order_details.quantity_ordered',
                'ETIN' => 'order_details.ETIN',
                'qty_fulfilled' => 'order_details.quantity_fulfilled',
                'picker' => 'order_details.picker_id',
                'order_date' => 'order_summary.purchase_date',
                'order_source' => 'order_summary.order_source',
                'destination' => 'order_summary.ship_to_state',
                'channel_delivery_date' => 'order_summary.channel_estimated_delivery_date',
                'ship_by' => 'order_summary.ship_by_date',
                'order_number' => 'order_details.sub_order_number',
                'company_name' => 'clients.company_name',
                'picker_name' => 'users.name',
                'id' => 'order_details.id',
                'order_status' => 'order_details_status.status'
        ];

        $orders_obj = OrderDetail::join('order_summary', 'order_details.order_number', '=', 'order_summary.etailer_order_number')
            ->join('order_details_status', 'order_details_status.id', '=', 'order_details.status')
            ->leftJoin('users', 'users.id', '=', 'order_details.picker_id')
            ->leftJoin('clients', 'clients.id', '=', 'order_summary.client_id')
            ->whereIn('order_summary.order_type_id',[1,2])
            ->whereIn('order_summary.order_status', ['2', '3','4','5','6','18', '23'])
            ->whereNotNull('sub_order_number')
            ->where('order_details.warehouse', $request->wareHouse)
            // ->whereNull('order_details.status')
            ->whereNotNull('order_details.sub_order_number')
            ->whereIn('order_details.status', ['1', '2', '3', '4','9','10','11','12'])
            ->groupBy('sub_order_number')
            ->select([
                'order_details.sub_order_number', 
                'order_details.fulfilled_by', 
                'order_details.quantity_ordered as qty_ordered', 
                'order_details.ETIN', 
                'order_details.quantity_fulfilled as qty_fulfilled', 
                'order_details.picker_id as picker', 
                DB::raw('DATE_FORMAT(order_summary.purchase_date,"%m-%d-%Y") as order_date'), 
                'order_summary.order_source as order_source', 
                'order_summary.ship_to_state as destination', 
                'order_summary.channel_estimated_delivery_date as channel_delivery_date', 
                'order_summary.ship_by_date as ship_by', 
                'order_details.sub_order_number as order_number', 
                'clients.company_name',
                'users.name as picker_name', 
                'order_details.id', 
                'order_details_status.status as order_status',
                'order_details_status.id as order_status_id'
            ]);

            if(isset($request->sortBy['id'])){
                if($request->sortBy['desc']){
                    $sort = 'DESC';
                    
                }else{
                    $sort = 'ASC';
                }
                $orders_obj->orderBy($select_array[$request->sortBy['id']],$sort);
            }else{
                $orders_obj->orderBy('order_summary.id','desc');
            }

            if(isset($request->text) && $request->text != ''){
                $search = $request->text;
                $orders_obj->where(function($q) use($search){
                    $q->where('order_details.sub_order_number','LIKE','%'.$search.'%');
                    $q->OrWhere('order_details.ETIN','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_by_date','LIKE','%'.$search.'%');
                    $q->OrWhere('clients.company_name','LIKE','%'.$search.'%');
                    $q->OrWhere('users.name','LIKE','%'.$search.'%');
                    //$q->OrWhere('order_summary_status.order_status_name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.purchase_date','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.order_source','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_state','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.channel_estimated_delivery_date','LIKE','%'.$search.'%');
                });
            }
            
            $all_orders_obj = $orders_obj;
            $all_orders = $all_orders_obj->get();
            $totalOrders = count($all_orders);
            $orders_obj->skip($offset)->take($limit);

            
            // $qry = str_replace(array('%', '?'), array('%%', '%s'), $orders_obj->toSql());
            // $qry = vsprintf($qry, $orders_obj->getBindings());
            // dd($qry);
            $orders = $orders_obj->get();

        
        
        
        $toReturn['totalOrders'] = $totalOrders;
        $toReturn['frozen'] = 0;
        $toReturn['refrigerated'] = 0;
        $toReturn['dry'] = 0;
        $osResponse = [];

        if ($totalOrders) {
            foreach ($all_orders as $order) {
                if ($order->sub_order_number && str_contains($order->sub_order_number, '.')) {
                    if (str_contains($order->sub_order_number, '.001') || str_contains($order->sub_order_number, '.004')) {
                        $toReturn['frozen'] = $toReturn['frozen'] + 1;
                    } else if (str_contains($order->sub_order_number, '.002') || str_contains($order->sub_order_number, '.005')) {
                        $toReturn['dry'] = $toReturn['dry'] + 1;
                    } else if (str_contains($order->sub_order_number, '.003')) {
                        $toReturn['refrigerated'] = $toReturn['refrigerated'] + 1;
                    } else if (str_contains($order->sub_order_number, '.006')) {
                        if ($order->fulfilled_by = 'dot') {
                            $toReturn['refrigerated'] = $toReturn['refrigerated'] + 1;
                        } else if ($order->fulfilled_by = 'kehe') {
                            $toReturn['dry'] = $toReturn['dry'] + 1;
                        }                    
                    }
                }
            }            
        }


        $toReturn['records'] = $orders;
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $toReturn];
        return response($response, 200);
    }


    public function getAllShippedOrders(Request $request) {
        $toReturn = [];
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $select_array = [
                'sub_order_number' => 'order_details.sub_order_number',
                'fulfilled_by' => 'order_details.fulfilled_by',
                'qty_ordered' => 'order_details.quantity_ordered',
                'ETIN' => 'order_details.ETIN',
                'qty_fulfilled' => 'order_details.quantity_fulfilled',
                'picker' => 'order_details.picker_id',
                'order_date' => 'order_summary.purchase_date',
                'order_source' => 'order_summary.order_source',
                'destination' => 'order_summary.ship_to_state',
                'channel_delivery_date' => 'order_summary.channel_estimated_delivery_date',
                'ship_by' => 'order_summary.ship_by_date',
                'order_number' => 'order_details.sub_order_number',
                'company_name' => 'clients.company_name',
                'picker_name' => 'users.name',
                'id' => 'order_details.id',
                'order_status' => 'order_details_status.status',
                'tracking_number' => 'order_packages.tracking_number'
        ];

        $orders_obj = OrderDetail::join('order_summary', 'order_details.order_number', '=', 'order_summary.etailer_order_number')
            ->join('order_details_status', 'order_details_status.id', '=', 'order_details.status')
            ->leftJoin('users', 'users.id', '=', 'order_details.picker_id')
            ->leftJoin('clients', 'clients.id', '=', 'order_summary.client_id')
            ->leftJoin('order_packages', 'order_packages.order_id', '=', 'order_details.sub_order_number')
            ->whereIn('order_summary.order_type_id',[1,2])
            // ->whereIn('order_summary.order_status', ['2', '3'])
            ->whereNotNull('sub_order_number')
            ->where('order_details.warehouse', $request->wareHouse)
            // ->whereNull('order_details.status')
            ->whereNotNull('order_details.sub_order_number')
            ->whereIn('order_details.status', [6,13])
            ->groupBy('order_details.sub_order_number')
            ->select([
                'order_details.sub_order_number', 
                'order_details.fulfilled_by', 
                'order_details.quantity_ordered as qty_ordered', 
                'order_details.ETIN', 
                'order_details.quantity_fulfilled as qty_fulfilled', 
                'order_details.picker_id as picker', 
                DB::raw('DATE_FORMAT(order_summary.purchase_date,"%m-%d-%Y") as order_date'), 
                DB::raw('GROUP_CONCAT(DISTINCT(order_packages.tracking_number) SEPARATOR ", ") as tracking_number'),
                'order_summary.order_source as order_source', 
                'order_summary.ship_to_state as destination', 
                'order_summary.channel_estimated_delivery_date as channel_delivery_date', 
                'order_summary.ship_by_date as ship_by', 
                'order_details.sub_order_number as order_number', 
                'clients.company_name',
                'users.name as picker_name', 
                'order_details.id', 
                'order_details_status.status as order_status',
                'order_details_status.id as order_status_id'
            ]);

            if(isset($request->sortBy['id'])){
                if($request->sortBy['desc']){
                    $sort = 'DESC';
                    
                }else{
                    $sort = 'ASC';
                }
                $orders_obj->orderBy($select_array[$request->sortBy['id']],$sort);
            }else{
                $orders_obj->orderBy('order_summary.id','desc');
            }

            if(isset($request->text) && $request->text != ''){
                $search = $request->text;
                $orders_obj->where(function($q) use($search){
                    $q->where('order_details.sub_order_number','LIKE','%'.$search.'%');
                    $q->OrWhere('order_details.ETIN','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_by_date','LIKE','%'.$search.'%');
                    $q->OrWhere('clients.company_name','LIKE','%'.$search.'%');
                    $q->OrWhere('users.name','LIKE','%'.$search.'%');
                    //$q->OrWhere('order_summary_status.order_status_name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.purchase_date','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.order_source','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_state','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.channel_estimated_delivery_date','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.customer_number','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.customer_name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.customer_email','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.customer_phone','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_address1','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_address2','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_address3','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_city','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_zip','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_country','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_phone','LIKE','%'.$search.'%');
                    $q->OrWhere('order_packages.tracking_number','LIKE','%'.$search.'%');
                    

                });
            }

            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();

            $orders_obj->whereBetween('order_packages.shipping_label_creation_time',[$start_date,$end_date]);
            
            $all_orders_obj = $orders_obj;
            $all_orders = $all_orders_obj->get();
            $totalOrders = count($all_orders);
            $orders_obj->skip($offset)->take($limit);

            
            // $qry = str_replace(array('%', '?'), array('%%', '%s'), $orders_obj->toSql());
            // $qry = vsprintf($qry, $orders_obj->getBindings());
            // dd($qry);
            $orders = $orders_obj->get();

        
        
        
        $toReturn['totalOrders'] = $totalOrders;
        $toReturn['frozen'] = 0;
        $toReturn['refrigerated'] = 0;
        $toReturn['dry'] = 0;
        $osResponse = [];

        if ($totalOrders) {
            foreach ($all_orders as $order) {
                if ($order->sub_order_number && str_contains($order->sub_order_number, '.')) {
                    if (str_contains($order->sub_order_number, '.001') || str_contains($order->sub_order_number, '.004')) {
                        $toReturn['frozen'] = $toReturn['frozen'] + 1;
                    } else if (str_contains($order->sub_order_number, '.002') || str_contains($order->sub_order_number, '.005')) {
                        $toReturn['dry'] = $toReturn['dry'] + 1;
                    } else if (str_contains($order->sub_order_number, '.003')) {
                        $toReturn['refrigerated'] = $toReturn['refrigerated'] + 1;
                    } else if (str_contains($order->sub_order_number, '.006')) {
                        if ($order->fulfilled_by = 'dot') {
                            $toReturn['refrigerated'] = $toReturn['refrigerated'] + 1;
                        } else if ($order->fulfilled_by = 'kehe') {
                            $toReturn['dry'] = $toReturn['dry'] + 1;
                        }                    
                    }
                }
            }            
        }


        $toReturn['records'] = $orders;
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $toReturn];
        return response($response, 200);
    }

    public function getNonPersonPickupOrders(Request $request) {
        $toReturn = [];
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $select_array = [
                'order_number' => 'order_summary.etailer_order_number',
                'order_date' => 'order_summary.purchase_date',
                'order_source' => 'order_summary.order_source',
                'destination' => 'order_summary.ship_to_state',
                'channel_delivery_date' => 'order_summary.channel_estimated_delivery_date',
                'ship_by' => 'order_summary.ship_by_date',
                'company_name' => 'clients.company_name',
                'picker_name' => 'users.name',
                'order_status' => 'order_summary_status.order_status_name',
                'bol_number' => 'order_summary.bol_number',
                'po_number' => 'order_summary.po_number'
        ];

        $orders_obj = OrderSummary::join('order_details', 'order_details.order_number', '=', 'order_summary.etailer_order_number')
            ->join('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
            ->leftJoin('users', 'users.id', '=', 'order_details.picker_id')
            ->leftJoin('clients', 'clients.id', '=', 'order_summary.client_id')
            ->whereIn('order_summary.order_type_id',[3])
            // ->whereIn('order_summary.order_status', [1,2,19])
            ->where('order_details.warehouse', $request->wareHouse)
            ->groupBy('etailer_order_number')
            ->select([
                DB::raw('DATE_FORMAT(order_summary.purchase_date,"%m-%d-%Y") as order_date'), 
                'order_summary.order_source as order_source', 
                'order_summary.ship_to_state as destination', 
                'order_summary.channel_estimated_delivery_date as channel_delivery_date', 
                'order_summary.ship_by_date as ship_by', 
                'order_summary.etailer_order_number as order_number', 
                'clients.company_name',
                'users.name as picker_name', 
                'order_summary_status.order_status_name as order_status',
                'order_summary.bol_number',
                'order_summary.po_number'

            ]);

            if(isset($request->sortBy['id'])){
                if($request->sortBy['desc']){
                    $sort = 'DESC';
                }else{
                    $sort = 'ASC';
                }
                $orders_obj->orderBy($select_array[$request->sortBy['id']],$sort);
            }

            if(isset($request->text) && $request->text != ''){
                $search = $request->text;
                $orders_obj->where(function($q) use($search){
                    $q->OrWhere('order_summary.ship_by_date','LIKE','%'.$search.'%');
                    $q->OrWhere('clients.company_name','LIKE','%'.$search.'%');
                    $q->OrWhere('users.name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary_status.order_status_name','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.purchase_date','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.order_source','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.ship_to_state','LIKE','%'.$search.'%');
                    $q->OrWhere('order_summary.channel_estimated_delivery_date','LIKE','%'.$search.'%');
                });
            }
            
            $all_orders_obj = $orders_obj;
            $all_orders = $all_orders_obj->get();
            $totalOrders = count($all_orders);
            $orders_obj->skip($offset)->take($limit);
            $orders = $orders_obj->get();

        
        
        
        $toReturn['totalOrders'] = $totalOrders;
        $toReturn['records'] = $orders;
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $toReturn];
        return response($response, 200);
    }

    public function PalletOrders($id){
        $result = PalletDetailsNonPersonOrder::leftJoin('order_summary',function($join){
            $join->on('order_summary.etailer_order_number','=','pallet_details_non_person_orders.order_numbers');
        })->join('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
        ->leftJoin('clients', 'clients.id', '=', 'order_summary.client_id')
        ->select([
            'order_summary.purchase_date as order_date', 
            'order_summary.order_source as order_source', 
            'order_summary.ship_to_state as destination', 
            'order_summary.channel_estimated_delivery_date as channel_delivery_date', 
            'order_summary.ship_by_date as ship_by', 
            'order_summary.etailer_order_number as order_number', 
            'clients.company_name',
            'order_summary_status.order_status_name as order_status'
        ])->where('pallet_header_non_person_orders_id',$id)->get();
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $result];
        return response($response, 200);
    }

    public function getOrderDetails($orderNumber) {

        $orderDetails = OrderDetail::where("order_number", $orderNumber)
            ->get(['id', 'ETIN', 'etailer_product_name', 'quantity_ordered', 'quantity_fulfilled']);

        if ($orderDetails && count($orderDetails) > 0) {
            $response = ["error" => false, 'message' => 'Data found successfully', "data" => $orderDetails];
            return response($response, 200);
        } else {
            $response = ["error" => true, 'message' => 'Data not found'];
            return response($response, 400);
        }
    }

    public function updateOrders(Request $request) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $data = $request->all();
        $validator = Validator::make($data, [
            '*.id' => 'required',
            '*.fullfilledValue' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        foreach ($data as $datum) {

            $od = OrderDetail::where('id', $datum['id'])->first();
            if ($od) {
                $od->quantity_fulfilled = $datum['fullfilledValue'];
                $od->save();
            }

            UpdateOrderHistory([
                'order_number' => $od->order_number,
                'sub_order_number' => $od->sub_order_number,
                'detail' => 'Sub Order #'.$od->sub_order_number.' fulfilled Qty updated',
                'title' => 'Sub Order Qty Update',
                'user_id' => $user_id,
                'reference' => 'API',
                'extras' => json_encode($od)
            ]);
        }
        $response = ["error" => false, 'message' => 'Data updated successfully'];
        return response($response, 200);
    }

    public function OrderItems($subOrderNumber)
    {
        $orderDetail = OrderDetail::join('master_product', 'master_product.ETIN', '=', 'order_details.ETIN')->where('sub_order_number',$subOrderNumber)->orderBy('sub_order_number')->select('order_details.id','order_details.ETIN','product_listing_name','quantity_ordered','quantity_fulfilled','transit_days','order_details.status','product_temperature')->get();

        $packages = OrderPackage::leftjoin('packaging_materials',function($join){
            $join->on('packaging_materials.id','=','order_packages.box_used');
        })->where('order_id',$subOrderNumber)->groupBy('package_num')->select('package_num','scannable_barcode as scan_package')->get();

        $status = 0;
        if(isset($orderDetail[0]->status)){
            $status = $orderDetail[0]->status;
        }
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $orderDetail,'packages' => $packages,'order_status' => $status];
        return response($response, 200);
    }

    public function NonPickupOrderItems($order_number)
    {
        $orderDetail = OrderDetail::join('master_product', 'master_product.ETIN', '=', 'order_details.ETIN')->where('order_number',$order_number)->orderBy('sub_order_number')->select('order_details.id','order_details.ETIN','product_listing_name','quantity_ordered','quantity_fulfilled')->get();

        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $orderDetail];
        return response($response, 200);
    }


    public function createPickupOrderPallet(Request $request){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        Log::channel('WMS')->info('=====================================');
        if($request->SelectedOrders){
            foreach($request->SelectedOrders as $row_selected_order){
                
                Log::channel('WMS')->info('Creating Pallet for Non-pickup Orders: ' . $row_selected_order['order_number']);
                $get_all_sub_orders = OrderDetail::where('order_number',$row_selected_order['order_number'])->get();

                if(count($get_all_sub_orders)>0){
                    foreach($get_all_sub_orders as $row_sub_orders){
                        $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                        ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                        ->where('master_shelf.ETIN', $row_sub_orders->ETIN)->whereIN('master_shelf.location_type_id', [1,2])
                        ->where('warehouses.warehouses', $row_sub_orders->warehouse)->first(['master_shelf.*']);
                        // If ETIN is not found than do not proceed
                        if (!$ms) {
                            Log::channel('WMS')->info('No Location found for ETIN: ' . $row_sub_orders->ETIN);
                            continue;     
                        }
                         
                        if($ms->cur_qty < $row_sub_orders->quantity_ordered){
                            $response = ["error" => true, 'message' => 'We unable to fulfil quantity for product: '.$row_sub_orders->ETIN.' (order numer: '.$row_selected_order['order_number'].')'];
                            return response($response, 400);
                        }
                    }
                }else{
                    $response = ["error" => true, 'message' => 'No Order found'];
                    return response($response, 400);
                }
            }
        }

        
        $PHO = new PalletHeaderNonPersonOrder();
        $PHO->status = 'Picked';
        $PHO->pallet_number = time();
        $PHO->save();
        if($request->SelectedOrders){
            foreach($request->SelectedOrders as $row_selected_order){
                $PDO = new PalletDetailsNonPersonOrder();
                $PDO->pallet_header_non_person_orders_id = $PHO->id;
                $PDO->order_numbers = $row_selected_order['order_number'];
                $PDO->save();
                $orderDetails = OrderDetail::where('order_number',$row_selected_order['order_number'])->get();
                foreach($orderDetails as $row){
                    $row->status = 3;
                    $row->quantity_fulfilled = $row->quantity_ordered;
                    $row->save();
                }

                OrderSummary::where('etailer_order_number',$row_selected_order['order_number'])->update(['order_status' => 4,'bol_number' =>$row_selected_order['bol_number'],'po_number' =>$row_selected_order['po_number'] ]);
                UpdateOrderHistory([
                    'order_number' => $row_selected_order['order_number'],
                    'detail' => 'Non person Order #'.$row_selected_order['order_number'].' picked',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($row_selected_order)
                ]);
            }

            foreach($request->SelectedOrders as $row_selected_order){
                $this->changeQty($row_selected_order['order_number'],$user_id);
            }
        }

        $response = ["error" => false, 'message' => 'Data found successfully'];
        return response($response, 200);
    }

    public function getOrderPallets(Request $request){
        
        $result_obj = PalletHeaderNonPersonOrder::whereNotNull('pallet_number');

        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        if(isset($request->text) && $request->text != ''){
			$search = $request->text;
			$result_obj->where(function($q) use($search){
				$q->where('pallet_number','LIKE','%'.$search.'%');
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
		}

        $offset = ($page - 1) * $limit;

        $all_obj = $result_obj;
        $all_result = $all_obj->get();
        $total_records = count($all_result);
        $result_obj->skip($offset)->take($limit);
        $result = $result_obj->get();

        $data = OrderPalletsResource::collection($result);
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => $data,
            'total_records' => $total_records
        ]);
    }

    public function ShipOrderPallets(Request $request,$id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $PHO = PalletHeaderNonPersonOrder::find($id);
        $PHO->status = 'Shiped';
        $PHO->save();

        $PDO = PalletDetailsNonPersonOrder::where('pallet_header_non_person_orders_id',$id)->get();
        if($PDO){
            foreach($PDO as $row_pdo){
                OrderDetail::where('order_number',$row_pdo->order_numbers)->update(['status' => 6]);
                OrderSummary::where('etailer_order_number',$row_pdo->order_numbers)->update(['order_status' => 17]);
                UpdateOrderHistory([
                    'order_number' => $row_pdo->order_numbers,
                    'detail' => 'Non person Order #'.$row_pdo->order_numbers.' shipped',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($row_pdo)
                ]);
            }
        }
        
        $response = ["error" => false, 'message' => 'Data found successfully'];
        return response($response, 200);
    }

    private function changeQty($order_numbers,$user_id) {

        // Getting all the orders with the sub order number
        $orders = OrderDetail::where('order_number',$order_numbers)->whereIn('status',[2,10])->get();
        if (!$orders || count($orders) <= 0) {
            Log::channel('WMS')->info('No orders found for Sub-Order: ' . $order_numbers);
        }

        // Assumption that Either Parent or Child Product will be orderd.
        // Product with Same Parent and Child will not happen.
        // Confirmation will be done by Steven.
        foreach ($orders as $order) {

            // Fetching record for master shelf with ordered ETIN
            $ms = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                    ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                    ->where('master_shelf.ETIN', $order->ETIN)->where('master_shelf.location_type_id', 1)
                    ->where('warehouses.warehouses', $order->warehouse)->first(['master_shelf.*']);
            
            // If ETIN is not found than do not proceed
            if (!$ms) {
                Log::channel('WMS')->info('No Product found for ETIN: ' . $order->ETIN);
                continue;     
            }

            $mpt = MasterProduct::where('ETIN', $order->ETIN)->first();

            // Getting the product from MPT. If it does not exist than do not proceed.
            if (!$mpt) {
                Log::channel('WMS')->info('No records found in MPT for ETIN: ' . $order->ETIN);
                continue;     
            }

            // Change the Product current quantity
            Log::channel('WMS')->info('Current Qty for ETIN: ' . $order->ETIN . ' is ' . $ms->cur_qty);
            InventoryAdjustmentLog([
                'ETIN' => $ms->ETIN,
                'location' => $ms->address,
                'starting_qty' => $ms->cur_qty,
                'ending_qty' => ($ms->cur_qty - $order->quantity_ordered),
                'total_change' => '-'.$order->quantity_ordered,
                'user' => $user_id,
                'reference' => 'Order Management',
                'reference_value' => 'order_numbers: '.$order_numbers,
                'reference_description' => 'Updating Qty for ETIN while pickup order: changeQty'
            ]);
            MasterShelf::where('id', $ms->id)->update(['cur_qty' => $ms->cur_qty - $order->quantity_ordered]);
            Log::channel('WMS')->info('Current Qty for ETIN: ' . $order->ETIN . ' is ' . ($ms->cur_qty - $order->quantity_ordered));

            // Checking if the product is a child or parent product
            if (isset($mpt->parent_ETIN)) {
                
                Log::channel('WMS')->info('Record found as a child for ETIN: ' . $order->ETIN . '. Parent ETIN: ' . $mpt->parent_ETIN);

                // Changing current qty for parent product
                $ms_parent = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                    ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                    ->where('master_shelf.ETIN', $mpt->parent_ETIN)->where('master_shelf.location_type_id', 1)
                    ->where('warehouses.warehouses', $order->warehouse)->first(['master_shelf.*']);
                
                    if (!$ms_parent) {
                    Log::channel('WMS')->info('No Record found for Parent ETIN: ' . $mpt->parent_ETIN);
                    continue;
                }

                Log::channel('WMS')->info('Record found for Parent ETIN: ' . $mpt->parent_ETIN);

                Log::channel('WMS')->info('Current Qty for ETIN: ' . $mpt->parent_ETIN . ' is ' . $ms_parent->cur_qty);
                $starting_qty = $ms_parent->cur_qty;
                $ms_parent->cur_qty = floor($ms_parent->cur_qty / $ms->cur_qty);
                Log::channel('WMS')->info('New Current Qty for ETIN: ' . $mpt->parent_ETIN . ' is ' . $ms_parent->cur_qty); 
                InventoryAdjustmentLog([
                    'ETIN' => $ms_parent->ETIN,
                    'location' => $ms_parent->address,
                    'starting_qty' => $starting_qty,
                    'ending_qty' => ($ms_parent->cur_qty),
                    'total_change' => ($starting_qty - $ms_parent->cur_qty),
                    'user' => $user_id,
                    'reference' => 'Order Management',
                    'reference_value' => 'order_numbers: '.$order_numbers,
                    'reference_description' => 'Updating Qty for ETIN while pickup order: changeQty'
                ]);
                MasterShelf::where('id', $ms_parent->id)->update(['cur_qty' => $ms_parent->cur_qty]);
                // $ms_parent->save();

            } else {
                Log::channel('WMS')->info('Record found as a parent for ETIN: ' . $order->ETIN);

                // Getting the units in pack for parent to callibarate the children(if any) accordingly
                $mp_units_in_pack = (isset($mpt->unit_in_pack) ? $mpt->unit_in_pack : 0);
                Log::channel('WMS')->info('Units in pack for ETIN: ' . $order->ETIN . ' is ' . $mp_units_in_pack);

                // Getting all children ETINs
                $mpt_children_etins = MasterProduct::where('parent_ETIN', 'like', '%'.$order->ETIN.'%')
                    ->where('is_approve', 1)->get(['ETIN'])->pluck('ETIN');
                $mpt_children = MasterProduct::where('parent_ETIN', 'like', '%'.$order->ETIN.'%')
                    ->where('is_approve', 1)->get(['ETIN', 'unit_in_pack']);

                // If no children than, do not proceed
                if (!$mpt_children_etins || count($mpt_children_etins) < 0) {
                    Log::channel('WMS')->info('No Child Records found for Parent ETIN: ' . $mpt->parent_ETIN);
                    continue;
                }

                // Getting all childrens in shelf. If nothing exists, than, do not proceed
                $ms_children = MasterShelf::join('master_aisle', 'master_aisle.id', '=', 'master_shelf.aisle_id')
                    ->join('warehouses', 'warehouses.id', '=', 'master_aisle.warehouse_id')
                    ->whereIn('master_shelf.ETIN', $mpt_children_etins)->where('master_shelf.location_type_id', 1)
                    ->where('warehouses.warehouses', $order->warehouse)->get(['master_shelf.*']);

                if (!$ms_children || count($ms_children) < 0) {
                    Log::channel('WMS')->info('No Child Record found in shelves for Parent ETIN: ' . $mpt->parent_ETIN);
                    continue;
                }

                Log::channel('WMS')->info(count($mpt_children_etins) . ' Children Records found for Parent ETIN: ' . $order->ETIN);

                // Getting children ETIN and units in pack for multiplication factor calculation
                $etin_units = [];
                foreach ($mpt_children as $mpt_child) {
                    $etin_units[$mpt_child->ETIN] = $mpt_child->unit_in_pack;
                }
                Log::channel('WMS')->info('Children ETIN Units: ' . json_encode($etin_units));

                Log::channel('WMS')->info(count($ms_children) . ' Children Record found in shelves for Parent ETIN: ' . $order->ETIN);

                // Calculating the new current qty for children
                foreach ($ms_children as $ms_child) {

                    $cp_unit_in_pack = isset($etin_units[$ms_child->ETIN]) ? $etin_units[$ms_child->ETIN] : 0;
                    if ($cp_unit_in_pack == 0) {
                        Log::channel('WMS')->info('Child Record has 0 as UNIT IN PACK for ETIN: ' . $ms_child->ETIN);
                    }
                    
                    Log::channel('WMS')->info('Child Record has ' . $cp_unit_in_pack . ' as UNIT IN PACK for ETIN: ' . $ms_child->ETIN);

                    $m_factor = floor($mp_units_in_pack / $cp_unit_in_pack);
                    Log::channel('WMS')->info('Multiplication Factor: ' . $m_factor);

                    Log::channel('WMS')->info('Current Qty for ETIN: ' . $ms_child->ETIN . ' is ' . $ms_child->cur_qty);
                    $new_qty = $ms->cur_qty * $m_factor;
                    Log::channel('WMS')->info('Modified Current Qty for ETIN: ' . $ms_child->ETIN . ' is ' . $new_qty);
                    InventoryAdjustmentLog([
                        'ETIN' => $ms_child->ETIN,
                        'location' => $ms_child->address,
                        'starting_qty' => $ms_child->cur_qty,
                        'ending_qty' => ($new_qty),
                        'total_change' => ($ms_child->cur_qty - $new_qty),
                        'user' => $user_id,
                        'reference' => 'Order Management',
                        'reference_value' => 'order_numbers: '.$order_numbers,
                        'reference_description' => 'Updating Qty for ETIN while pickup order: changeQty'
                    ]);
                    MasterShelf::where('id', $ms_child->id)->update(['cur_qty' => $new_qty]);
                    // $ms_child->save();
                }
            }
        }

        return "Success";
    }


    public function OrderDetailStatus(Request $request){
        $result = OrderDetailsStatus::get();
        $response = ["error" => false, 'message' => 'Data found successfully','data' => $result];
        return response($response, 200);
    }

    public function UpdateOrderDetailStatus(Request $request,$order_id, $status_id){
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $OrderDetail = new OrderDetail();
        $result = $OrderDetail->UpdateOrderStatus($order_id, $status_id, $user_id);
        if($result == "Error"){
            $response = ["error" => true, 'message' => 'Status Updated'];
            return response($response, 400);
        }else{
            $response = ["error" => false, 'message' => 'Status Updated'];
            return response($response, 200);
        }
        
    }

    
}
