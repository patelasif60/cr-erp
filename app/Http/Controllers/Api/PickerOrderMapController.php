<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OrderDetail;
use App\OrderSummary;
use App\PickerOrderMap;
use DB;
use App\WareHouse;

class PickerOrderMapController extends Controller
{

    public function getAllPickers($warehouse)
    {
        if($warehouse){
            $warehouses = WareHouse::where('warehouses',$warehouse)->first();
            $pickers = User::where(function ($query) use ($warehouses) {
                $query->whereIn('role', [4, 1, 2])
                    ->orWhere(function ($query)  use ($warehouses) {
                        $query->where('role', 5)
                            ->where('wh_id', $warehouses->id);
                    });
            })->get(['users.id', 'users.name']);
        }
        else{
            $pickers = User::join('user_roles', 'users.role', '=', 'user_roles.id')
            ->whereIn('user_roles.id', [4, 5,1,2])->get(['users.id', 'users.name']);
        }
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $pickers];
        return response($response, 200);
    }

    public function getAllUsers($warehouse_id)
    {
        $users = User::where('wh_id', $warehouse_id)->get(['users.id', 'users.name']);
        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $users];
        return response($response, 200);
    }

    public function getPickerOrders($pickerId)
    {
        $pickerRole = User::join('user_roles', 'users.role', '=', 'user_roles.id')
            ->where('users.id', $pickerId)->get(['user_roles.role', 'users.name']);

        // if (!$pickerRole || !in_array($pickerRole[0]['role'],['WMS User','WMS Manager','Admin','Manager'])) {
        //     $response = ["error" => true, 'message' => 'User is not a picker'];
        //     return response($response, 400);
        // }

        /*$pickerOrders = PickerOrderMap::join('order_details', 'order_details.id', '=', 'picker_order_maps.order_summary_id')
            ->join('order_summary', 'order_summary.etailer_order_number', '=', 'order_details.order_number')
            ->where('picker_order_maps.user_id', $pickerId)->where('is_active', 1)
            ->get(['order_summary.purchase_date', 'order_details.sub_order_number']);*/

        $pickerOrders = OrderDetail::join('order_summary', 'order_summary.etailer_order_number', '=', 'order_details.order_number')->leftJoin('order_details_status',function($q){
            $q->on('order_details_status.id','=','order_details.status');
        })
            ->where('order_details.picker_id', $pickerId)->whereIn('order_details.status', [2,10])
            ->select(DB::raw('DATE_FORMAT(order_summary.purchase_date,"%m-%d-%Y") as purchase_date'), 'order_details.sub_order_number', 'order_details.ETIN', 'order_details.etailer_product_name','order_details_status.status')
            ->groupBy('order_details.sub_order_number')->get();

        $data['picker'] = $pickerRole[0]['name'];
        $data['orders'] = $pickerOrders;

        $response = ["error" => false, 'message' => 'Data found successfully', "data" => $data];
        return response($response, 200);
    }

    public function updatePickerInOrder(Request $request, $subOrderId, $pickerId)
    {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $pickerRole = User::join('user_roles', 'users.role', '=', 'user_roles.id')
            ->where('users.id', $pickerId)->get(['user_roles.role', 'users.name']);

        if (!$pickerRole || !in_array($pickerRole[0]['role'],['WMS User','WMS Manager','Admin','Manager'])) {
            $response = ["error" => true, 'message' => 'User is not a picker'];
            return response($response, 400);
        }

        $os = OrderDetail::where('id', $subOrderId)->first();
        if (!$os) {
            $response = ["error" => true, 'message' => 'Sub Order Id not found'];
            return response($response, 400);
        }

        // if(in_array($os->status, [6,13])){
        //     $response = ["error" => true, 'message' => 'You can not change picker after order is shipped'];
        //     return response($response, 400);
        // }
        // if(in_array($os->status, [3,4])){
        //     $response = ["error" => true, 'message' => 'You can not change picker after order is picked,packed or assigned'];
        //     return response($response, 400);
        // }
        if(!in_array($os->status, [1,2,9,10])){
            $response = ["error" => true, 'message' => 'You can not change picker after order is '.$os->OrderDetailsStatus->status];
            return response($response, 400);
        }
        // if (!isset($os->picker_id)) {
        //     $response = ["error" => true, 'message' => 'Order is not assigned to any Picker'];
        //     return response($response, 400);
        // }

        // if (!isset($os->picker_id)) {
        //     $response = ["error" => true, 'message' => 'Order is not assigned to any Picker'];
        //     return response($response, 400);
        // }

        // if (!isset($os->picker_id) || $os->status != '2' || $os->status != '10') {
        //     $response = ["error" => true, 'message' => 'Order is not assigned to any Picker'];
        //     return response($response, 400);
        // }
        OrderDetail::where('sub_order_number',$os->sub_order_number)->update([
            'picker_id' => $pickerId
        ]);
        if($os->status == 1){
            OrderDetail::where('sub_order_number',$os->sub_order_number)->update([
                'status' => 2
            ]);
        }
        if($os->status == 9){
            OrderDetail::where('sub_order_number',$os->sub_order_number)->update([
                'status' => 10
            ]);
        }
        // $os->picker_id = $pickerId;
        // $os->save();

        UpdateOrderHistory([
            'order_number' => $os->order_number,
            'sub_order_number' => $os->sub_order_number,
            'detail' => 'Picker('.UserName($pickerId).') has been assigned to Sub order Number #: '.$os->sub_order_number,
            'title' => 'Sub Order Status Changed',
            'user_id' => $user_id,
            'reference' => 'API',
            'extras' => json_encode($os)
        ]);

        $response = ["error" => false, 'message' => 'Picker re-assign successful'];
        return response($response, 200);
    }
}
