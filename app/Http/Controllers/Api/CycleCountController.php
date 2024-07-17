<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CycleCountSummary;
use App\MasterShelf;
use App\CycleCountDetail;
use App\MasterProduct;
use App\OrderDetail;
use Illuminate\Support\Facades\Validator;

class CycleCountController extends Controller
{
    public function cycle_count_summary(Request $request){
        if($request->warehouse_id == "") $request->warehouse_id = 1;
        $result_obj = CycleCountSummary::where('warehouse_id',$request->warehouse_id)->orderByRaw("
        CASE
            WHEN STATUS = 'Scheduled' THEN 1
            WHEN STATUS = 'InProcess' THEN 2
            WHEN STATUS = 'AwaitingApproval' THEN 3
            WHEN STATUS = 'PartiallyCompleted' THEN 4
            ELSE 5
        END
    ");
        
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $all_obj = $result_obj;
        $all_result = $all_obj->get();
        $total_records = count($all_result);
        $result_obj->skip($offset)->take($limit);
        $allSummary = $result_obj->get();

        $data = $allCycleCountSummary = [];

        foreach($allSummary as $row){
            $data['id'] = $row->id;
            $data['warehouse'] = $row->warehouse->warehouses;
            $data['schedule_date'] =  $row->scheduled_date != '' ? date('Y-m-d',strtotime($row->scheduled_date)) : '';
            $data['start_date_time'] = $row->start_date_time != '' ? date('Y-m-d',strtotime($row->start_date_time)) : '';
            $data['complate_date_time'] = $row->complate_date_time != '' ? date('Y-m-d',strtotime($row->complate_date_time)) : '';
            $data['status'] = $row->status;
            $data['count_type'] = $row->count_type;
            $data['user_id'] = $row->user_id;
            $allCycleCountSummary[] = $data;
        }

        return response(["error" => false, 'data' => $allCycleCountSummary, 'total_records' => $total_records], 200);
    }

    public function cycle_count_summary_details($id){
        $getCycleCount = CycleCountSummary::find($id);

        if(!$getCycleCount){
            return response(["error" => true, 'message' => 'Cycle Count Summary Not found', $data => [] ], 400);
        }

        $getLocations = CycleCountDetail::where('cycle_count_summary_id', $id)->groupBy('address');

        $currentLocation = CycleCountDetail::where('cycle_count_summary_id', $id)->whereNull('status')->groupBy('address')->select('address')->first();
        $dataCycledetail = CycleCountDetail::where('cycle_count_summary_id', $id)->get();

        $data = [];

        $data['id'] = $getCycleCount->id;
        $data['warehouse'] = $getCycleCount->warehouse->warehouses;
        $data['schedule_date'] = $getCycleCount->schedule_date;
        $data['status'] = $getCycleCount->status;
        $data['locations'] = $getLocations->select('address')->get();
        $data['current_location'] = $currentLocation ? $currentLocation->address : '';
        $data['cycle_detail_data'] =$dataCycledetail;
        $total_records = count($dataCycledetail);
        if($currentLocation || $data['status'] == 'Complete'){
            return response(["error" => false, 'data' => $data,'total_records' => $total_records ], 200);
        }else{
            return response(["error" => true,'message' => 'No next location found' ], 400);
        }

        

    }

    public function NextLocation(Request $request){
        $request->validate([
            'id' => 'required',
            'location_address' => 'required'
        ]);
        $id = $request->id;
        $location = $request->location_address;

        CycleCountDetail::where('cycle_count_summary_id', $id)->where('address',$location)->update([
            'status' => 0
        ]);

        $currentLocation = CycleCountDetail::where('cycle_count_summary_id', $id)->whereNull('status')->groupBy('address')->select('address')->first();
        if($currentLocation){
            $data = [];
            $data['current_location'] = $currentLocation->address;
            return response(["error" => false,'message' => 'Success', 'data' => $data ], 200);
        }else{
            return response(["error" => true,'message' => 'No next location found' ], 400);
        }

        
    }

    public function add_items(Request $request){
        $req = $request->all();
        $validator = Validator::make($req, [
            'upc' => 'required',
            'location_address' => 'required',
            'qty' => 'required',
            'damaged' => 'required',
            'cycle_count_summary_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 400);
        }

        
        $ckLocation = MasterShelf::where('address', $request->location_address)->first();
        if(!$ckLocation){
            return response(["error" => true, 'message' => 'Location not found' ], 400);
        }
        
        $upc = $request->upc;
        $prod = MasterProduct::where(function($q) use($upc){
            $q->where('upc', $upc);
            $q->orWhere('gtin',$upc);
            $q->orWhere('ETIN',$upc);
        })->first();
        if(!$prod){
            return response(["error" => true, 'message' => 'Product not found' ], 400);
        }

        $cc_sum = CycleCountSummary::find($request->cycle_count_summary_id);
        $count = OrderDetail::where('ETIN',$prod->ETIN)->where('warehouse',$cc_sum->warehouse->warehouses)->whereIn('status',[1,2,9,10])->get()->count();
        if($count > 0){
            return response(["error" => true, 'message' => 'Product is in order status. so we can not proceed cycle count for this product.' ], 400);
        }

        $getCycleCountDetails = CycleCountDetail::where('cycle_count_summary_id', $request->cycle_count_summary_id)->where('ETIN', $prod->ETIN)->where('address',$request->location_address)->first();
        if(!$getCycleCountDetails){
            return response(["error" => true, 'message' => 'Product not found in this cycle count' ], 400);
        }
        
        


        $getCycleCountDetails->total_counted = $request->qty;
        $getCycleCountDetails->total_expired = $request->damaged;
        $getCycleCountDetails->type = $request->type;
        $getCycleCountDetails->exp_date = $request->exp_date;
        //$getCycleCountDetails->status = 0;
        $getCycleCountDetails->save();
    

        $product = [];
        $product['ETIN'] = $prod->ETIN;
        $product['qty'] = $request->qty;
        $product['damaged'] = $request->damaged;
        $product['product_listing_name'] = $prod->product_listing_name; 

        $childProductsArray = $childProduct = [];

        $childProducts = MasterProduct::where('parent_ETIN', $prod->ETIN)->get();
        foreach($childProducts as $child){
            $childProduct['qty'] = round(($prod->pack_form_count * $prod->unit_in_pack * $request->qty) / ($child->pack_form_count * $child->unit_in_pack));
            $childProduct['ETIN'] = $child->ETIN;
            $childProduct['product_listing_name'] = $child->product_listing_name;

            $childProductsArray[] = $childProduct;
        }

        return response(["error" => false, 'message' => 'Product counted successfully!', 'data' => $getCycleCountDetails, 'product' => $product, 'child_products' => $childProductsArray], 200);
    }

    public function complete_cycle_counts($id){
        $cycleCountSummary = CycleCountSummary::find($id);
        $cycleCountSummary->complate_date_time = date('Y-m-d H:i');
        $cycleCountSummary->status = 'AwaitingApproval';
        $cycleCountSummary->save();

        return response(["error" => false, 'message' => 'Cycle count completed!', 'data' => $cycleCountSummary], 200);

    }

    public function start_cycle_counts($id){
        $cycleCountSummary = CycleCountSummary::find($id);
        if($cycleCountSummary->start_date_time == ''){
            $cycleCountSummary->start_date_time = date('Y-m-d H:i');
            $cycleCountSummary->status = 'InProcess';
            $cycleCountSummary->save();
        }
        return response(["error" => false, 'message' => 'Cycle count Started!', 'data' => $cycleCountSummary], 200);
    }

}
