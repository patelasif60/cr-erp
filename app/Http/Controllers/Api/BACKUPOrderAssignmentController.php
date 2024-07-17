<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use App\WareHouse;
use App\OrderDetail;
use App\OrderSummary;
use App\UpsZipZoneByWH;
use App\ProcessingGroups;
use App\PickerConfigration;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\OrderDayShippingEligibility;
use App\ShippingWarehouseEligibilities;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use App\MasterShelf;
use App\MasterAisle;

class BACKUPOrderAssignmentController extends Controller 
{
    public function __construct(NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->NotificationRepository = $NotificationRepository;
	}

    /**
     * Orders are assignd to pickers
     *
     * @param [integer] $pickerId
     * @param [integer] $wah
     * @return JSON Response
     */
    public function assignOrdersToPicker(Request $request, $pickerId, $wah) {
        $header = $request->header('Authorization');
		$user_id = ExtractToken($header);
        $day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));

        if(in_array($day,['sunday','saturday'])){
            $response = ["error" => true, 'message' => 'You can not pick on '. $day];
            return response($response, 400);
        }

        Log::channel('OrderAssignmentController')->info('==============================================');
        Log::channel('OrderAssignmentController')->info('assignOrdersToPicker Start');

        Log::channel('OrderAssignmentController')->info('Picker: ' . $pickerId . ', Warehouse: ' . $wah);
        $pickerRole = User::join('user_roles', 'users.role', '=', 'user_roles.id')
            ->leftJoin('warehouses as wh', 'wh.id', '=', 'users.wh_id')
            ->where('users.id', $pickerId)->get(['user_roles.role', 'users.name', 'wh.time_zone']);

        Log::channel('OrderAssignmentController')->info('Picker Role: '.$pickerRole);
        
        if (!$pickerRole || !in_array($pickerRole[0]['role'],['WMS User','WMS Manager','Admin','Manager'])) {
            Log::channel('OrderAssignmentController')->info('Picker Type '.$pickerRole[0]['role']);
            $response = ["error" => true, 'message' => 'User is not a picker'];
            return response($response, 400);
        }

        $wh = WareHouse::where('warehouses', $wah)->first();

        // Have to orderby client and need_today
        $orders = OrderSummary::join('order_details', 'order_details.order_number', '=', 'order_summary.etailer_order_number')
            ->where('order_details.warehouse', $wah)
            ->whereNull('order_details.picker_id')
            ->whereIn('order_summary.order_status', [2, 3, 18, 23]) // For Checking only orders that are R2P, Partial Picked, M R2Pick
            ->whereIn('order_details.status', [1,9])    // For R2P and Re-R2P
            ->whereNotNull('order_details.sub_order_number')
            ->orderBy('order_summary.must_ship_today','desc')
            ->orderBy('order_summary.purchase_date', 'desc')           
            ->get(['order_details.*', 'order_summary.ship_to_state', 'order_summary.ship_to_zip','order_summary.etailer_order_number','order_summary.client_id','order_summary.saturday_eligible']);
        Log::channel('OrderAssignmentController')->info('Fetched sub-order items to sort. Size: ' . count($orders));

        $temps = [];
        if(count($orders) == 0){
            $response = ["error"=>true, 'message' => 'No orders found'];
			return response($response, 404);  
        }
        
        foreach ($orders as $order) {
            Log::channel('OrderAssignmentController')->info('Order # ' .$order->etailer_order_number );
            if($order->client_id == ''){
                $response = ["error"=>true, 'message' => 'Client is not assigned on order number '.$order->etailer_order_number];
                return response($response, 404);  
            }

            if($order->fulfilled_by == ''){
                $response = ["error"=>true, 'message' => 'Order fulfiled by is empty of sub order number '.$order->sub_order_number];
                return response($response, 404);  
            }


            $temp = $this->getTemp(strtolower($order->fulfilled_by), $order->sub_order_number);
            if (isset($temp) && !in_array($temp, $temps)) {
                array_push($temps, $temp);
            }
        }
        
        Log::channel('OrderAssignmentController')->info('Fetched Temperatures for orders: ' . json_encode($temps));
        
        // Getting day of the week and time of the day
        
        # $time = date("H", strtotime('now'));
        $default_tz = date_default_timezone_get();
        if (!isset($pickerRole[0]['time_zone'])) {
            Log::channel('OrderAssignmentController')->info('No TZ found for Picker. Using default TZ: ' . $default_tz);
        } else {
            Log::channel('OrderAssignmentController')->info('Using TZ for WH assigned to Picker: ' . $pickerRole[0]['time_zone']);
            date_default_timezone_set($pickerRole[0]['time_zone']);
        }        
        $time = localtime(time(),true)['tm_hour'];
        date_default_timezone_set($default_tz);

        Log::channel('OrderAssignmentController')->info('Day: ' . $day . ', Time: ' . $time);

        $selgs = ShippingWarehouseEligibilities::where('warehouse_id', $wh->id)->get($day);
        if (!isset($selgs) || count($selgs) <= 0) {
            $response = ["error"=>true, 'message' => 'Shipping Eligibity for warehouse not set.'];
			return response($response, 404);  
        }
        Log::channel('OrderAssignmentController')->info('Shipping WH Eligibility: ' . json_encode($selgs));

        $osge = OrderDayShippingEligibility::whereIn('id', $selgs)->get();
        if (!isset($selgs) || count($osge) <= 0) {
            $response = ["error"=>true, 'message' => 'Order Day Shipping Eligibity for warehouse not set.'];
			return response($response, 404);  
        }
        Log::channel('OrderAssignmentController')->info('Order Day Shipping: ' . count($osge));

        $pc = PickerConfigration::where('user_id', $pickerId)->first();
        if (!isset($pc)) {
            $response = ["error"=>true, 'message' => 'Picker is not assigned for group or max batch not found'];
			return response($response, 404);  
        }
        Log::channel('OrderAssignmentController')->info('Picker Config: ' . $pc->order_processing_id);

        $groups = ProcessingGroups::whereIn('id', explode(',', $pc->order_processing_id))->pluck('group_name')->toArray();
        if (!isset($groups) || count($groups) <= 0) {
            $response = ["error"=>true, 'message' => 'Picker is not assigned to any processing groups'];
			return response($response, 404);
        }
        Log::channel('OrderAssignmentController')->info('Picker Groups: ' . json_encode($groups));

        $packType = $this->getPackagingType($temps, $groups);
        if (!isset($packType)) {
            $response = ["error"=>true, 'message' => 'Picker is not assigned any groups'];
			return response($response, 404);
        }
        Log::channel('OrderAssignmentController')->info('Pack Type to Assign: ' . $packType);

        $maxBatch = $this->getMaxBatch($pc, $time);
        Log::channel('OrderAssignmentController')->info('Max batch for picker: ' . $maxBatch);

        try {            
            $batch = $this->assignOrders($orders, $osge, $maxBatch, $wah, $packType, $day);
            
            if (count($batch) > 0) {
                $this->savePickerIdAndBatch($user_id,$batch, $pickerId);
                $this->changeOrderSummaryStatus($batch,$user_id);
                
				UserLogs([
					'user_id' => $user_id,
					'action' => 'Click',
					'task' => 'Pick',
					'details' => 'User Clicked on Start pick button',
					'type' => 'CWMS'
				]);

                $response = [
                    "error" => false, 
                    'message' => 'Batch created successfully. Total Order: ' . count($orders) . '. Fullfilled: ' . count($batch), 
                    "data" => $batch
                ];
			    return response($response, 200);
            } else {
                $response = [
                    "error" => true, 
                    'message' => 'No order to assign. Total Order: ' . count($orders) . '. Fullfilled: 0'
                ];
			    return response($response, 404);
            }          
        } catch (Exception $ex) {
            $response = ["error"=>true, 'message' => 'Batch created failed', 'exception'=>$ex->getMessage()];
			return response($response, 500);
        }
    }

    private function changeOrderSummaryStatus($batch,$user_id) {
        $checked = [];
        foreach ($batch as $order) {
            if (in_array($order->order_number, $checked)) {
                continue;
            }
            $status = OrderDetail::where('order_number', $order->order_number)->distinct()->get(['status'])->pluck('status')->all();
            if (isset($status) && count($status) > 0 && in_array('2', $status)) {
                OrderSummary::where('etailer_order_number', $order->order_number)->update([
                        'order_status' => 3
                ]);
                UpdateOrderHistory([
                    'order_number' => $order->order_number,
                    'sub_order_number' => $order->sub_order_number,
                    'detail' => 'Order #'.$order->order_number.' Partially Picked',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order)
                ]);
            } else if (isset($status) && count($status) == 1 && in_array('3', $status)) {
                OrderSummary::where('etailer_order_number', $order->order_number)->update([
                    'order_status' => 4
                ]);
                UpdateOrderHistory([
                    'order_number' => $order->order_number,
                    'sub_order_number' => $order->sub_order_number,
                    'detail' => 'Order #'.$order->order_number.' Picked',
                    'title' => 'Order Status Changed',
                    'user_id' => $user_id,
                    'reference' => 'API',
                    'extras' => json_encode($order)
                ]);
            } 
        }
    }

    private function assignOrders($orders, $osge, $maxBatch, $wh, $packType, $day) {

        $batch = [];
        $orderNumbers = [];

        foreach ($orders as $order) {

            if (count($orderNumbers) == $maxBatch) {
                break;
            }

            /*if ($order->status == null || $order->status > 1) {
                Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Status is null or already assigned');
                continue;
            }*/

            $fullfilledBy = strtolower($order->fulfilled_by);
            
            $temp = $this->getTemp($fullfilledBy, $order->sub_order_number);
            Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Temp: ' . $temp . '. Pack Type: ' . $packType);

            //$transitDay = $this->getTransitDay($order->ship_to_state, $order->ship_to_zip, $wh);
            $transitDay = $order->transit_days;
            Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Transit day: ' . $transitDay);


            if (!(strcmp($temp, $packType) == 0 && $this->isTempAndTransitElligible($temp, $transitDay, $osge))) {
                Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Condition not fulfilled. Checking for Saturday Order.');            
                if ($order->saturday_eligible == 1 && $this->isSaturdayPickup($day, $order->transit_days, $order->service_type_id)) {
                    Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Order is Saturday Elligible. Adding to Order.');
                } else {
                    Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Condition not fulfilled. Not Adding');
                    continue;
                }
                // Log::channel('OrderAssignmentController')->info('Condition 1: ' . (strcmp($temp, $packType) == 0 ? 'true' : 'false'));
                // Log::channel('OrderAssignmentController')->info('Condition 2: ' . ($this->isTempAndTransitElligible($temp, $transitDay, $osge) ? 'true' : 'false'));
            }
            
            if (!in_array($order->etailer_order_number, $orderNumbers)) {
                array_push($orderNumbers, $order->etailer_order_number);
            }

            Log::channel('OrderAssignmentController')->info('Order Id: ' . $order->id . '. Order Added to batch.');

            array_push($batch, $order);
        }

        // Save the order_id and picker_id in DB.
        return $batch;
    }

    private function isTempAndTransitElligible($temp, $transit, $osge) {
        Log::channel('OrderAssignmentController')->info('To check. Temp: ' . $temp . ', Day: ' . $transit);
        foreach ($osge as $os) {
            Log::channel('OrderAssignmentController')->info('Temp: ' . $os->temperature . ', Day: ' . $os->day);
            if (strtolower($os->temperature) == strtolower($temp) && $os->day == $transit) {
                return true;
            }
        }
        return false;
    }

    private function getTemp($fullfilledBy, $subOrderNumber) {
        switch($fullfilledBy) {
            case "e-tailer":
                if (str_contains($subOrderNumber, '.001')) {
                    return 'Frozen';
                } else if (str_contains($subOrderNumber, '.002')) {
                    return 'Dry';
                } else if (str_contains($subOrderNumber, '.003')) {
                    return 'Refrigerated';
                } 
            case "dot":
                if (str_contains($subOrderNumber, '.004')) {
                    return 'Frozen';
                } else if (str_contains($subOrderNumber, '.005')) {
                    return 'Dry';
                } else if (str_contains($subOrderNumber, '.006')) {
                    return 'Refrigerated';
                }                
            case "kehe":
                if (str_contains($subOrderNumber, '.006')) {
                    return 'Dry';
                } 
                break;
            default:
                break;
        }
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

    private function getMaxBatch($pc, $time) {
        if ($time < 14) {
            return $pc->batch_max_until_2pm;
        } else if ($time >= 14 && $time <= 16) {
            return $pc->batch_max_2pm_to_4pm;
        } else {
            return $pc->batch_max_after_4pm;
        }
    }

    private function getPackagingType($temps, $groups) {
        
        if (in_array('Frozen', $temps) && in_array('Frozen', $groups)) {
            return 'Frozen';
        } else if (in_array('Refrigerated', $temps) && in_array('Refrigerated', $groups)) {
            return 'Refrigerated';
        } else if (in_array('Dry', $temps) && in_array('Dry', $groups)) {
            return 'Dry';
        } else {
            return null;
        }
    }

    private function savePickerIdAndBatch($user_id,$batch, $pickerId) {
        foreach ($batch as $order) {
            OrderDetail::where('id', $order->id)->where('status',1)->update(['picker_id' => $pickerId, 'status' => 2]);
            OrderDetail::where('id', $order->id)->where('status',9)->update(['picker_id' => $pickerId, 'status' => 10]);    
            $this->NotificationRepository->SendOrderNotification([
                'subject' => "Order Status: Picker Assigned",
                'body' => 'Picker Is Assigned to Sub Order No: '.$order->sub_order_number,
                'user_id' => $pickerId,
                'order_number' => $order->order_number
            ]);
            
            UpdateOrderHistory([
                'order_number' => $order->order_number,
                'sub_order_number' => $order->sub_order_number,
                'detail' => 'Sub Order #'.$order->sub_order_number.' Picker Assigned('.UserName($pickerId).')',
                'title' => 'Sub Order Status Changed',
                'user_id' => $user_id,
                'reference' => 'API',
                'extras' => json_encode($order),
            ]);
        }
    }

    private function isSaturdayPickup($day, $transitDay, $serviceTypeId) {
        
        if ($day === 'thursday' && $transitDay == '2' && in_array($serviceTypeId, [5, 11, 22, 23])) {
            Log::channel('OrderAssignmentController')->info('Order Picked as Saturday Elligible');
            return true;
        }
        
        if ($day === 'friday' && $transitDay == '1' && in_array($serviceTypeId, [2, 3, 4, 20, 21])) {
            Log::channel('OrderAssignmentController')->info('Order Picked as Saturday Elligible');
            return true;
        }
        
        if ($day === 'thursday' || $day === 'friday') {
            Log::channel('OrderAssignmentController')->info(
                $day === 'thursday' 
                ? 'Thursday with 2-D Pickup is eligible only for Service Type Id: [5, 11, 22, 23].'
                : 'Friday with 1-D Pickup is eligible only for Service Type Id: [2, 3, 4, 20, 21].');
        }
            
        return false;
    }
}