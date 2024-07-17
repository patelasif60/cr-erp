<?php

namespace App\Http\Controllers\Api;

use App\PurchasingSummary;
use App\PurchasingDetail;
use DB;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchasingSummaryController extends Controller
{

    /*
        Method: addOrUpdatePurchasingSummary
        Description: Add or Update the Purchasing Summary
    */
    public function addOrUpdatePurchasingSummary(Request $request) {

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'purchasing_asn_date' => 'required',
            'order' => 'required',
            'invoice' => 'required',
            'bol' => 'required',
            'product_cost' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => true, 'message' => $validator->errors()->all()], 422);
        }

        if ($request->id) {
            $record = PurchasingSummary::where('id', $request->id)->first();
            if ($record) {
                $record->supplier_id = $request->supplier_id;
                $record->purchasing_asn_date = $request->purchasing_asn_date;
                $record->order = $request->order;
                $record->invoice = $request->invoice;
                $record->bol = $request->bol;
                $record->product_cost = $request->product_cost;
                $record->delivery_inbound_fees = $request->delivery_inbound_fees;
                $record->freight_shipping_charge = $request->freight_shipping_charge;
                $record->misc_acquisition_cost = $request->misc_acquisition_cost;
                $record->surcharge_1 = $request->surcharge_1;
                $record->surcharge_2 = $request->surcharge_2;
                $record->surcharge_3 = $request->surcharge_3;
                $record->surcharge_4 = $request->surcharge_4;
                $record->surcharge_5 = $request->surcharge_5;
                $record->status = $request->status ? $request->status : 'Ready';
                $record->save();

                return response(["error" => false, 'message' => 'Record updated successfully!'], 200);
            } else {
                return response(["error" => true, 'message' => 'Record not found!'], 404);
            }
        } else {
            PurchasingSummary::create([
                'supplier_id' => $request->supplier_id,
                'purchasing_asn_date' => $request->purchasing_asn_date,
                'order' => $request->order,
                'invoice' => $request->invoice,
                'bol' => $request->bol,
                'product_cost' => $request->product_cost,
                'delivery_inbound_fees' => $request->delivery_inbound_fees,
                'freight_shipping_charge' => $request->freight_shipping_charge,
                'misc_acquisition_cost' => $request->misc_acquisition_cost,
                'surcharge_1' => $request->surcharge_1,
                'surcharge_2' => $request->surcharge_2,
                'surcharge_3' => $request->surcharge_3,
                'surcharge_4' => $request->surcharge_4,
                'surcharge_5' => $request->surcharge_5,
                'status' => 'Ready'
            ]);
            return response(["error" => false, 'message' => 'Record added successfully!'], 200);
        }
    }

    /*
        Method: getAllPurchasingSummary
        Description: Get all Purchasing Summary
    */
    public function getAllPurchasingSummary(Request $request) {
        if($request->warehouse_id == "") $request->warehouse_id = 1;
        $limit = 12;
        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        $page = 15;
        if (isset($request->page)) {
            $page = $request->page;
        }

        $offset = ($page - 1) * $limit;

        $result_obj = PurchasingSummary::join('warehouses', 'warehouses.id', '=', 'purchasing_summaries.warehouse_id')->where('purchasing_summaries.warehouse_id',$request->warehouse_id);
        $result_obj->select(['purchasing_summaries.*', 'warehouses.warehouses as warehouse_name']);

        $all_obj = $result_obj;
        $all_result = $all_obj->get();
        $total_records = count($all_result);
        $result_obj->skip($offset)->take($limit);
        $result = $result_obj->get();

    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result,'total_records' => $total_records, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400, 'error' => true];
			return response($response, 400);
    	}
    }

    /*
        Method: getAllPurchasingSummaryBySeller
        Description: Get all Purchasing Summary for a particular seller
    */
    public function getAllPurchasingSummaryBySupplier($supplierId) {

        $result = PurchasingSummary::where('supplier_id', $supplierId)->get();
    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400, 'error' => true];
			return response($response, 400);
    	}
    }

    /*
        Method: getAllPurchasingSummaryBySeller
        Description: Get all Purchasing Summary for a particular seller
    */
    public function getAllPurchasingSummaryById($id) {

        $result = PurchasingSummary::where('id', $id)->first();
    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400, 'error' => true];
			return response($response, 400);
    	}
    }

    /*
        Method: getAllPurchasingSummaryByWarehouseId
        Description: Get all Purchasing Summary
    */
    public function getAllPurchasingSummaryByWareHouseId($whId) {
        $result = PurchasingSummary::where('warehouse_id', $whId)->get();
    	if (count($result->toArray()) > 0) {
    		$response = ["data" => $result, 'message' => 'Data found successfully', 'status' => 200];
			return response($response, 200);
    	}
    	else{
    		$response = ["message" => 'Data not found!', 'status' => 400, 'error' => true];
			return response($response, 400);
    	}
    }



    public function GetAllUniqueBol(Request $request){
        $result_obj = PurchasingDetail::leftJoin('purchasing_summaries',function($q){
            $q->on('purchasing_summaries.order','=','purchasing_details.po');
        })->leftJoin('clients',function($q){
            $q->on('clients.id','=','purchasing_details.client_id');
        })
        ->leftJoin('suppliers',function($q){
            $q->on('suppliers.id','=','purchasing_details.supplier_id');
        })
        ->select('purchasing_details.bol_number','purchasing_details.status','purchasing_details.updated_at',DB::raw('DATE_FORMAT(purchasing_details.reference_date,\'%m-%d-%Y\') as reference_date'),DB::raw('DATE_FORMAT(purchasing_summaries.created_at,\'%m-%d-%Y\') as purchase_date'),DB::raw('DATE_FORMAT(purchasing_summaries.purchasing_asn_date,\'%m-%d-%Y\') as order_date'),DB::raw('SUM(purchasing_details.qty_ordered) as total_order_qty'), DB::raw('CASE WHEN clients.company_name IS NULL THEN suppliers.name ELSE clients.company_name  END as client_name'))->where('purchasing_details.warehouse_id',$request->warehouse_id)->whereNotNull('purchasing_details.bol_number')->where('purchasing_details.bol_number','!=','null')->where('purchasing_details.status',$request->status);

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
        $result_obj->groupBy('bol_number');

        if(isset($request->sortBy['id'])){
			if($request->sortBy['desc']){
				$sort = 'DESC';
			}else{
				$sort = 'ASC';
			}
            $sortBy = $request->sortBy['id'];
			$result_obj->orderByRaw("CAST($sortBy AS UNSIGNED), $sortBy $sort");
		}else{
            $result_obj->orderBy('purchasing_summaries.created_at','DESC');
        }



		if(isset($request->text) && $request->text != ''){
			$search = $request->text;
			$result_obj->where(function($q) use($search){
				$q->where('purchasing_details.bol_number','LIKE','%'.$search.'%');
				$q->OrWhere('purchasing_details.status','LIKE','%'.$search.'%');
				$q->OrWhere('purchasing_details.updated_at','LIKE','%'.$search.'%');
			});
		}

        $all_result = $all_obj->get();
        $total_records = count($all_result);
        $result_obj->skip($offset)->take($limit);
        $result = $result_obj->get();

        return response()->json(["data" => $result,'total_records' => $total_records, 'message' => 'Data found successfully', 'status' => 200,'error' => false]);
    }

    public function GetBOlInfo($bol_number){
        $result = PurchasingDetail::select('bol_number','status','updated_at')->where('bol_number',$bol_number)->orderBy('status','ASC')->first();
        return response()->json(["data" => $result, 'message' => 'Data found successfully', 'status' => 200,'error' => false]);
    }
}
