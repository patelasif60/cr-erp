<?php

namespace App\Http\Controllers;
use App\{ScheduleReports,OrderSummaryStatus,MasterShelf,OrderDetail,OrderSummary,InventorySummery,ReceivingDetail,ReportsUserFilter,PutAway,ReportsMasterFilter,ProductListingFilter,SmartFilter,WareHouse,Client,MasterProduct,PurchasingDetail,PurchasingSummary,TransferInventoryDetails,Restock,InventoryAdjustmentReport};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Exports\{ReportExport,ReportExportCollection};
use Excel;
use Illuminate\Support\Facades\Mail;

class ReportBuilderController extends Controller
{

    public function index($id=null) {
		//dd($id);
        $selected_smart_filter = [];
		$visible_filters = [];
		$hidden_cols = '';
		$visible_columns = '';
		$smart_filter = [];
		$hidden_cols_arr = [];
		$not_default_columns = [];
		$main_filter = [];
		$max_chars_columns = '';
		$product_listing_filter = ProductListingFilter::where('type','product')->orderBy('sorting_order')->get();
		$smart_filters = SmartFilter::where('created_by',Auth::user()->id)->where('type','product')->get();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->get();
        $client = Client::get()->pluck('company_name','id')->toArray();
		// if($id != NULL){
		// 	$all_columns = ProductListingFilter::where('type','product')->pluck('sorting_order','id')->toArray();
		// 	$smart_filter = SmartFilter::find($id);

		// 	$selected_smart_filter = json_decode($smart_filter->filter_preferences,true);
		// 	$visible_columns = explode(',',$smart_filter->visible_columns);
		// 	$visible_filters = explode(',',$smart_filter->visible_filters);
		// 	if(!empty($all_columns)){
		// 		$hidden_cols_arr = array_diff($all_columns,$visible_columns);
		// 		$hidden_cols = implode(',',$hidden_cols_arr);
		// 	}
		// 	$main_filter = json_decode($smart_filter->main_filter,true);
		// }else{
			$not_default_columns = ProductListingFilter::where('is_default','!=',1)->where('type','product')->pluck('sorting_order','id')->toArray();
			if(!empty($not_default_columns)){
				$hidden_cols = implode(',',$not_default_columns);
			//}
		}
		return view('cranium.reports.reportbuilder.index',['client'=>$client,'warehouse'=>$warehouse,'product_listing_filter' => $product_listing_filter, 'smart_filters' => $smart_filters, 'selected_smart_filter' => $selected_smart_filter, 'id' => $id, 'hidden_cols' => $hidden_cols, 'visible_columns' => $visible_columns, 'visible_filters' => $visible_filters,'smart_filter' => $smart_filter,'main_filter' => $main_filter, 'hidden_cols_arr' => $hidden_cols_arr ]);
    }

    public function generateReport(Request $request) {
		$request->validate([
			'report' => 'required',
		]);
		if($request->report == 'product_report'){
            return $this->getProductReport($request);
		}
		if($request->report == 'inventory_report'){
			return $this->getInventoryReport($request);
		}
		if($request->report == 'order_report'){
			return $this->getOrderReport($request);
		}
		if($request->report == 'user_report'){
			return $this->getUserReport($request);
		}
		if($request->report == 'material_report'){
			return $this->getMaterialReport($request);
		}
		
		if($request->report == 'billing_report'){
			return $this->getBillingReport($request);
		}
    }

    public function getUserReport($request) {
        
    }

    public function getInventoryReport($request,$type=null) { 
		$request->reportSchedule= false;
		if($request->warehouseId=='' && $request->client_id==''){
			if($request->report_type == 'receive'){	
				$dataget = ReceivingDetail::all();
			}
			if($request->report_type == 'putaway'){
				$dataget = PutAway::all();
			}
			if($request->report_type == 'transfer')
			{
				$dataget = TransferInventoryDetails::all();
			}
			if($request->report_type == 'inventory_adjustment')
			{
				$dataget = InventoryAdjustmentReport::all()->groupBy('ETIN')->orderBy('id','desc');
			}
			if($request->report_type == 'inventory' || $request->report_type == 'own_inventory')
			{
				$dataget = InventorySummery::whereNotNull('ETIN')->groupBy('ETIN')->get();
			}
			if($request->report_type == 'restoke'){
				$dataget = Restock::all();
			}
			if($request->report_type == 'perpetual'){
				$dataget = MasterShelf::all();
			}
		}
		if($request->warehouseId!='' && $request->client_id!=''){
			$warehouse = WareHouse::where('warehouses',$request->warehouseId)->first();
			$clientId = $request->client_id;
			if($request->report_type == 'receive'){
				$dataget = ReceivingDetail::with('purchasingDetail')->where('warehouse_id',$warehouse->id);
				$dataget = $dataget->whereHas("purchasingDetail",function($q) use($clientId){$q->where("client_id",$clientId);})->get();
			}
			if($request->report_type == 'putaway'){
				$dataget = PutAway::with('purchasingDetail');
				$dataget = $dataget->whereHas("purchasingDetail",function($q) use($clientId,$warehouse){$q->where("client_id",$clientId)->where('warehouse_id',$warehouse->id);})->get();
			}
			if($request->report_type == 'transfer')
			{
				$dataget = TransferInventoryDetails::with('product')->where('current_warehouse',$warehouse->id)->whereHas("product",function($q) use($clientId){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->get();
			}
			if($request->report_type == 'inventory_adjustment')
			{
				//$dataget = InventoryAdjustmentReport::with('product')->whereHas("product",function($q) use($clientId,$request){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->where('warehouse',$request->warehouseId)->get();
				$dataget = \DB::select("SELECT * FROM inventry_adjustment_report  INNER JOIN master_shelf ON inventry_adjustment_report.`location` = master_shelf.`address` INNER JOIN location_type ON master_shelf.`location_type_id` = location_type.id INNER JOIN master_product ON inventry_adjustment_report.ETIN = master_product.`ETIN` INNER JOIN users ON inventry_adjustment_report.`user` = users.id WHERE FIND_IN_SET($request->client_id,master_product.`lobs`) AND inventry_adjustment_report.`warehouse` = '$request->warehouseId' AND inventry_adjustment_report.id IN (SELECT MAX(inventry_adjustment_report.id) AS id FROM inventry_adjustment_report WHERE inventry_adjustment_report.created_at  <= '$request->to_date' GROUP BY ETIN) GROUP BY inventry_adjustment_report.ETIN");
			}
			if($request->report_type == 'inventory' || $request->report_type == 'own_inventory')
			{
				$dataget = InventorySummery::with('product')->whereNotNull('ETIN')->whereHas("product",function($q) use($clientId){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->groupBy('ETIN')->get();
			}
			if($request->report_type == 'perpetual'){
				$dataget = MasterShelf::with('product','ailse')->whereHas("product",function($q) use($clientId){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->whereHas("ailse",function($q) use($warehouse){$q->where('warehouse_id',$warehouse->id);})->get();
			}
			if($request->report_type == 'restoke'){
				$dataget =  Restock::where('warehouse_id',$warehouse->id)->get();
			}
		}
		if($request->warehouseId=='' && $request->client_id!=''){
			$clientId = $request->client_id;
			if($request->report_type == 'receive'){
				$dataget = ReceivingDetail::with('purchasingDetail');
				$dataget = $dataget->whereHas("purchasingDetail",function($q) use($clientId){$q->where("client_id",$clientId);})->get();
			}
			if($request->report_type == 'putaway'){
				$dataget = PutAway::with('purchasingDetail');
				$dataget = $dataget->whereHas("purchasingDetail",function($q) use($clientId){$q->where("client_id",$clientId);})->get();
			}
			if($request->report_type == 'perpetual'){
				$dataget = MasterShelf::with('product')->whereHas("product",function($q) use($clientId){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->get();
			}
			if($request->report_type == 'inventory' || $request->report_type == 'own_inventory')
			{
				$dataget = InventorySummery::with('product')->whereNotNull('ETIN')->whereHas("product",function($q) use($clientId,$request){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->groupBy('ETIN')->get();
			}
			if($request->report_type == 'transfer')
			{
				$dataget = TransferInventoryDetails::with('product')->whereHas("product",function($q) use($clientId,$request){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->get();
			}
			if($request->report_type == 'inventory_adjustment')
			{
				//$dataget = InventoryAdjustmentReport::with('product')->whereHas("product",function($q) use($clientId,$request){$q->whereRaw('FIND_IN_SET(\''.$clientId.'\',master_product.lobs)');})->get();

				$dataget = \DB::select("SELECT * FROM inventry_adjustment_report  INNER JOIN master_shelf ON inventry_adjustment_report.`location` = master_shelf.`address` INNER JOIN location_type ON master_shelf.`location_type_id` = location_type.id INNER JOIN master_product ON inventry_adjustment_report.ETIN = master_product.`ETIN` INNER JOIN users ON inventry_adjustment_report.`user` = users.id WHERE FIND_IN_SET($request->client_id,master_product.`lobs`) AND inventry_adjustment_report.id IN (SELECT MAX(inventry_adjustment_report.id) AS id FROM inventry_adjustment_report WHERE inventry_adjustment_report.created_at  <= '$request->to_date' GROUP BY ETIN) GROUP BY inventry_adjustment_report.ETIN");
			}
			if($request->report_type == 'restoke'){
				$dataget = Restock::all();
			}
		}
		if($request->warehouseId!='' && $request->client_id==''){
			$warehouse = WareHouse::where('warehouses',$request->warehouseId)->first();
			if($request->report_type == 'receive'){
				$dataget = ReceivingDetail::with('purchasingDetail')->where('warehouse_id',$warehouse->id)->get();
			}
			if($request->report_type == 'putaway'){
				$dataget = PutAway::with('purchasingDetail');
				$dataget = $dataget->whereHas("purchasingDetail",function($q) use($warehouse){$q->where('warehouse_id',$warehouse->id);})->get();
			}
			if($request->report_type == 'transfer')
			{
				$dataget = TransferInventoryDetails::where('current_warehouse',$warehouse->id)->get();
			}
			if($request->report_type == 'inventory_adjustment')
			{
				//$dataget = InventoryAdjustmentReport::where('warehouse',$request->warehouseId)->get();
				$dataget = \DB::select("SELECT * FROM inventry_adjustment_report  INNER JOIN master_shelf ON inventry_adjustment_report.`location` = master_shelf.`address` INNER JOIN location_type ON master_shelf.`location_type_id` = location_type.id INNER JOIN master_product ON inventry_adjustment_report.ETIN = master_product.`ETIN` INNER JOIN users ON inventry_adjustment_report.`user` = users.id WHERE  inventry_adjustment_report.`warehouse` = '$request->warehouseId' AND inventry_adjustment_report.id IN (SELECT MAX(inventry_adjustment_report.id) AS id FROM inventry_adjustment_report WHERE inventry_adjustment_report.created_at  <= '$request->to_date' GROUP BY ETIN) GROUP BY inventry_adjustment_report.ETIN");
			}
			if($request->report_type == 'restoke'){
				$dataget = Restock::where('warehouse_id',$warehouse->id)->get();
			}
			if($request->report_type == 'perpetual'){
				$dataget = MasterShelf::with('ailse')->whereHas("ailse",function($q) use($warehouse){$q->where('warehouse_id',$warehouse->id);})->get();
			}
			if($request->report_type == 'inventory' || $request->report_type == 'own_inventory')
			{
				$dataget = InventorySummery::whereNotNull('ETIN')->groupBy('ETIN')->get();
			}
		}
		if($type)
		{
			$request->reportSchedule= true;
			return  Excel::download(new ReportExport($dataget,$request,'inventory_receiving'),'inventory_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx');
		}
		return  Excel::download(new ReportExport($dataget,$request,'inventory_receiving'),'inventory_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
    }
    public function getOrderReport($request,$type=null) {
		$request->reportSchedule= false;
		$statusIn = [2,4,6];
		//dd($test->OrderPackage->groupBy('id'));
		if($request->warehouseId=='' && $request->client_id==''){
			if($request->report_type == 'own_order' ||  $request->report_type == 'all_order'){
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->get();
				//$dataget = OrderSummary::where('etailer_order_number',10002)->get();
			}
			if($request->report_type == 'shipped_order'){
				//$dataget = OrderDetail::with('orderSummary')->where('order_number',10186)->groupBy('sub_order_number')->get();
				$dataget = \DB::select(" SELECT warehouse,c.*, SUM(order_packages.`shipped_qty`) AS shipped_qty, 
				order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`
			   FROM (
				 SELECT order_summary.*, sub_order_number,warehouse, SUM(order_details.`transit_days`) AS transit_days
				 ,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name
				 FROM order_summary
				 LEFT JOIN order_details
				 ON order_summary.`etailer_order_number` = order_details.`order_number`
				 LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_summary.`shipment_type`
				 LEFT JOIN carriers ON carriers.id = order_summary.`carrier_id`
				 LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				 where purchase_date>='$request->from_date' and purchase_date<='$request->to_date'
				 AND (order_details.`status`= 6 OR order_details.`status` = 13)
				 GROUP BY order_summary.`etailer_order_number`, order_details.`sub_order_number`
			   ) AS c
			   INNER JOIN order_packages
			   ON c.`sub_order_number` = order_packages.`order_id`
			   GROUP BY c.`sub_order_number`, order_packages.`package_num`");
				
			}
			if($request->report_type == 'open_order')
			{
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereIn('order_status',$statusIn)->get();
			}
			if($request->report_type == 'shipped_line_order' ){
				$dataget = OrderDetail::all();
			}
			if($request->report_type == 'unfulfill_order'){
				$dataNotInStatus = [7,8,9,10,11,12,13,15,16,24,25,26]; 
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereNotIn('order_status',$dataNotInStatus)->get();
			}
		}
		if($request->warehouseId!='' && $request->client_id!=''){
			$clientId = $request->client_id;
			$warehouseID = $request->warehouseId;
			if($request->report_type == 'own_order' || $request->report_type == 'all_order'){
				$dataget = OrderSummary::with('orderDetail')->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->where('client_id',$clientId);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
			if($request->report_type == 'open_order')
			{
				$dataget = OrderSummary::with('orderDetail')->whereIn('order_status',$statusIn)->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->where('client_id',$clientId);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
			if($request->report_type == 'shipped_order')
			{
				$dataget = \DB::select(" SELECT warehouse,c.*, SUM(order_packages.`shipped_qty`) AS shipped_qty, 
				order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`
			   FROM (
				 SELECT order_summary.*, sub_order_number,warehouse, SUM(order_details.`transit_days`) AS transit_days
				 ,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name
				 FROM order_summary
				 LEFT JOIN order_details
				 ON order_summary.`etailer_order_number` = order_details.`order_number`
				 LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_summary.`shipment_type`
				 LEFT JOIN carriers ON carriers.id = order_summary.`carrier_id`
				 LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				 where order_details.warehouse = '$warehouseID' and order_summary.client_id = $clientId and purchase_date >=' $request->from_date' and purchase_date <= '$request->to_date'
				 AND (order_details.`status`= 6 OR order_details.`status` = 13)
				 GROUP BY order_summary.`etailer_order_number`, order_details.`sub_order_number`
			   ) AS c
			   INNER JOIN order_packages
			   ON c.`sub_order_number` = order_packages.`order_id`
			   GROUP BY c.`sub_order_number`, order_packages.`package_num`");
			}
			if($request->report_type == 'shipped_line_order'){
				$dataget = OrderDetail::with('orderSummary')->where("warehouse",$warehouseID);
				$dataget = $dataget->whereHas("orderSummary",function($q) use($clientId){$q->where("client_id",$clientId);})->get();
			}
			if($request->report_type == 'unfulfill_order'){
				$dataNotInStatus = [7,8,9,10,11,12,13,15,16,24,25,26]; 
				$dataget = OrderSummary::with('orderDetail')->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereNotIn('order_status',$dataNotInStatus)->where('client_id',$clientId);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
		}
		if($request->warehouseId=='' && $request->client_id!=''){
			$clientId = $request->client_id;
			if($request->report_type == 'own_order' || $request->report_type == 'all_order' || $request->report_type == 'shipped_order' ){
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->where('client_id',$clientId)->get();
			}
			if($request->report_type == 'open_order')
			{
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereIn('order_status',$statusIn)->where('client_id',$clientId)->get();	
			}
			if($request->report_type == 'shipped_order')
			{
				$dataget = \DB::select(" SELECT warehouse,c.*, SUM(order_packages.`shipped_qty`) AS shipped_qty, 
				order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`
			   FROM (
				 SELECT order_summary.*, sub_order_number,warehouse, SUM(order_details.`transit_days`) AS transit_days
				 ,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name
				 FROM order_summary
				 LEFT JOIN order_details
				 ON order_summary.`etailer_order_number` = order_details.`order_number`
				 LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_summary.`shipment_type`
				 LEFT JOIN carriers ON carriers.id = order_summary.`carrier_id`
				 LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				 where order_summary.client_id = $clientId and purchase_date >= '$request->from_date' and purchase_date <= '$request->to_date'
				 AND (order_details.`status`= 6 OR order_details.`status` = 13)
				 GROUP BY order_summary.`etailer_order_number`, order_details.`sub_order_number`
			   ) AS c
			   INNER JOIN order_packages
			   ON c.`sub_order_number` = order_packages.`order_id`
			   GROUP BY c.`sub_order_number`, order_packages.`package_num`");
			}
			if($request->report_type == 'shipped_line_order'){
				$dataget = OrderDetail::with('orderSummary');
				$dataget = $dataget->whereHas("orderSummary",function($q) use($clientId){$q->where("client_id",$clientId);})->get();
			}
			if($request->report_type == 'unfulfill_order'){
				$dataNotInStatus = [7,8,9,10,11,12,13,15,16,24,25,26]; 
				$dataget = OrderSummary::where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereNotIn('order_status',$dataNotInStatus)->where('client_id',$clientId)->get();
			}
		}
		if($request->warehouseId!='' && $request->client_id==''){
			$warehouseID = $request->warehouseId;
			if( $request->report_type == 'own_order' || $request->report_type == 'all_order' || $request->report_type == 'shipped_order'){
				$dataget = OrderSummary::with('orderDetail')->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
			if($request->report_type == 'open_order')
			{
				$dataget = OrderSummary::with('orderDetail')->whereIn('order_status',$statusIn)->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
			if($request->report_type == 'shipped_order')
			{
				$dataget = \DB::select(" SELECT warehouse,c.*, SUM(order_packages.`shipped_qty`) AS shipped_qty, 
				order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`
			   FROM (
				 SELECT order_summary.*, sub_order_number,warehouse, SUM(order_details.`transit_days`) AS transit_days
				 ,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name
				 FROM order_summary
				 LEFT JOIN order_details
				 ON order_summary.`etailer_order_number` = order_details.`order_number`
				 LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_summary.`shipment_type`
				 LEFT JOIN carriers ON carriers.id = order_summary.`carrier_id`
				 LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				 where order_details.warehouse = '$warehouseID' and purchase_date >= '$request->from_date' and purchase_date <= '$request->to_date'
				 AND (order_details.`status`= 6 OR order_details.`status` = 13)
				 GROUP BY order_summary.`etailer_order_number`, order_details.`sub_order_number`
			   ) AS c
			   INNER JOIN order_packages
			   ON c.`sub_order_number` = order_packages.`order_id`
			   GROUP BY c.`sub_order_number`, order_packages.`package_num`");
			}
			if($request->report_type == 'shipped_line_order'){
				$dataget = OrderDetail::with('orderSummary')->where("warehouse",$warehouseID)->get();
			}
			if($request->report_type == 'unfulfill_order'){
				$dataNotInStatus = [7,8,9,10,11,12,13,15,16,24,25,26]; 
				$dataget = OrderSummary::with('orderDetail')->where('purchase_date','>=',$request->from_date)->where('purchase_date','<=',$request->to_date)->whereNotIn('order_status',$dataNotInStatus);
				$dataget = $dataget->whereHas("orderDetail",function($q) use($warehouseID){$q->where("warehouse",$warehouseID);})->get();
			}
		}
		if($type)
		{
			$request->reportSchedule= true;
			return  Excel::download(new ReportExport($dataget,$request,'order_report'),'order_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx');
		}
        return  Excel::download(new ReportExport($dataget,$request,'order_report'),'order_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
    }

    public function getProductReport($request,$type=null) {
		$request->reportSchedule= false;
        if($request->warehouseId=='' && $request->client_id==''){
			$dataget =  \DB::select("SELECT master_product.id,master_product.*,product_subcategory.`sub_category_1`,product_subcategory.`sub_category_2`,product_subcategory.`sub_category_3`,product_category.`product_category` as category,(SELECT GROUP_CONCAT(company_name) FROM clients WHERE FIND_IN_SET(clients.id,master_product.`lobs`))AS lobsName,(SELECT GROUP_CONCAT(tag) FROM product_tags WHERE FIND_IN_SET(product_tags.id,master_product.`product_tags`) )AS tagsName,(SELECT GROUP_CONCAT(allergens) FROM allergens WHERE FIND_IN_SET(allergens.id,master_product.`allergens`) )AS allergensName FROM master_product LEFT JOIN product_category ON master_product.`product_category` = product_category.`id` LEFT JOIN product_subcategory ON master_product.`product_subcategory1` = product_subcategory.`id`  OR master_product.`product_subcategory2` = product_subcategory.`id`  OR master_product.`product_subcategory3` = product_subcategory.`id` where is_approve=1  GROUP BY master_product.`id`");
			//dd($dataget);
			//$dataget = MasterProduct::where('is_approve', 1)->get();
		}
		elseif($request->warehouseId!='' && $request->client_id!=''){
			$dataget =  \DB::select("SELECT master_product.id,master_product.*,product_subcategory.`sub_category_1`,product_subcategory.`sub_category_2`,product_subcategory.`sub_category_3`,product_category.`product_category` as category,(SELECT GROUP_CONCAT(company_name) FROM clients WHERE FIND_IN_SET(clients.id,master_product.`lobs`))AS lobsName,(SELECT GROUP_CONCAT(tag) FROM product_tags WHERE FIND_IN_SET(product_tags.id,master_product.`product_tags`) )AS tagsName,(SELECT GROUP_CONCAT(allergens) FROM allergens WHERE FIND_IN_SET(allergens.id,master_product.`allergens`) )AS allergensName FROM master_product LEFT JOIN product_category ON master_product.`product_category` = product_category.`id` LEFT JOIN product_subcategory ON master_product.`product_subcategory1` = product_subcategory.`id`  OR master_product.`product_subcategory2` = product_subcategory.`id`  OR master_product.`product_subcategory3` = product_subcategory.`id` where is_approve = 1 and  FIND_IN_SET($request->client_id,lobs) AND FIND_IN_SET('$request->warehouseId',master_product.`warehouses_assigned`)  GROUP BY master_product.`id`");
			//dd($dataget->pluck('id'));
		}
		elseif($request->warehouseId=='' && $request->client_id!=''){
			$dataget =  \DB::select("SELECT master_product.id,master_product.*,product_subcategory.`sub_category_1`,product_subcategory.`sub_category_2`,product_subcategory.`sub_category_3`,product_category.`product_category` as category,(SELECT GROUP_CONCAT(company_name) FROM clients WHERE FIND_IN_SET(clients.id,master_product.`lobs`))AS lobsName,(SELECT GROUP_CONCAT(tag) FROM product_tags WHERE FIND_IN_SET(product_tags.id,master_product.`product_tags`) )AS tagsName,(SELECT GROUP_CONCAT(allergens) FROM allergens WHERE FIND_IN_SET(allergens.id,master_product.`allergens`) )AS allergensName FROM master_product LEFT JOIN product_category ON master_product.`product_category` = product_category.`id` LEFT JOIN product_subcategory ON master_product.`product_subcategory1` = product_subcategory.`id`  OR master_product.`product_subcategory2` = product_subcategory.`id`  OR master_product.`product_subcategory3` = product_subcategory.`id` where is_approve = 1 and  FIND_IN_SET($request->client_id,lobs)   GROUP BY master_product.`id`");
		}
		elseif($request->warehouseId!='' && $request->client_id==''){
			$dataget =  \DB::select("SELECT master_product.id,master_product.*,product_subcategory.`sub_category_1`,product_subcategory.`sub_category_2`,product_subcategory.`sub_category_3`,product_category.`product_category` as category,(SELECT GROUP_CONCAT(company_name) FROM clients WHERE FIND_IN_SET(clients.id,master_product.`lobs`))AS lobsName,(SELECT GROUP_CONCAT(tag) FROM product_tags WHERE FIND_IN_SET(product_tags.id,master_product.`product_tags`) )AS tagsName,(SELECT GROUP_CONCAT(allergens) FROM allergens WHERE FIND_IN_SET(allergens.id,master_product.`allergens`) )AS allergensName FROM master_product LEFT JOIN product_category ON master_product.`product_category` = product_category.`id` LEFT JOIN product_subcategory ON master_product.`product_subcategory1` = product_subcategory.`id`  OR master_product.`product_subcategory2` = product_subcategory.`id`  OR master_product.`product_subcategory3` = product_subcategory.`id` where is_approve = 1 and  FIND_IN_SET('$request->warehouseId',master_product.`warehouses_assigned`)  GROUP BY master_product.`id`");
		}

		if($type)
		{
			$request->reportSchedule= true;
			return  Excel::download(new ReportExport($dataget,$request,'product_report'),'Product_' . date('Ymdi') . '.xlsx');
		}
		return  Excel::download(new ReportExport($dataget,$request,'product_report'),'Product_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
    }
	public function getBillingReport($request,$type=null){
		$request->reportSchedule= false;
		if($request->warehouseId=='' && $request->client_id==''){
			if($request->report_type == 'billing_shipped_order'){
				$dataget =\DB::select("SELECT SUM(order_packages.`package_num`) AS packageTotal, warehouse,c.*,order_packages.`shipped_qty`, order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`,GROUP_CONCAT(CONCAT(order_packages.`ETIN`,' - QTY. ',order_packages.`shipped_qty`)) AS ETIN ,GROUP_CONCAT(master_product.`alternate_ETINs`) AS alternate_ETINs
				FROM (
				SELECT order_summary.*, sub_order_number,warehouse, order_details.`transit_days` AS transit_days
				,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name,order_details.status
				FROM order_summary
				LEFT JOIN order_details
				ON order_summary.`etailer_order_number` = order_details.`order_number`
				LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_details.`service_type_id`
				LEFT JOIN carriers ON carriers.id = order_details.`carrier_id`
				LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				WHERE (order_details.`status`= 6 OR order_details.`status` = 13)
				GROUP BY order_summary.`etailer_order_number`, order_details.`status`
				) AS c
				INNER JOIN order_packages
				ON c.`sub_order_number` = order_packages.`order_id`
				LEFT JOIN master_product ON master_product.`ETIN`= order_packages.`ETIN`
				WHERE order_packages.`ship_date`>='$request->from_date' and order_packages.`ship_date`<='$request->to_date'
				GROUP BY c.`etailer_order_number`,c.`status`");
			}
		}
		if($request->warehouseId!='' && $request->client_id!=''){
			$clientId = $request->client_id;
			$warehouseID = $request->warehouseId;
			if($request->report_type == 'billing_shipped_order'){
				$dataget =\DB::select("SELECT SUM(order_packages.`package_num`) AS packageTotal,warehouse,c.*,order_packages.`shipped_qty`, order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`,GROUP_CONCAT(CONCAT(order_packages.`ETIN`,' - QTY. ',order_packages.`shipped_qty`)) AS ETIN ,GROUP_CONCAT(master_product.`alternate_ETINs`) AS alternate_ETINs
				FROM (
				SELECT order_summary.*, sub_order_number,warehouse,order_details.`transit_days` AS transit_days
				,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name,order_details.status
				FROM order_summary
				LEFT JOIN order_details
				ON order_summary.`etailer_order_number` = order_details.`order_number`
				LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_details.`service_type_id`
				LEFT JOIN carriers ON carriers.id = order_details.`carrier_id`
				LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				WHERE order_details.`warehouse` = '$warehouseID' and order_summary.`client_id` = $clientId and (order_details.`status`= 6 OR order_details.`status` = 13)
				GROUP BY order_summary.`etailer_order_number`, order_details.`status`
				) AS c
				INNER JOIN order_packages
				ON c.`sub_order_number` = order_packages.`order_id`
				LEFT JOIN master_product ON master_product.`ETIN`= order_packages.`ETIN`
				WHERE order_packages.`ship_date`>='$request->from_date' and order_packages.`ship_date`<='$request->to_date'
				GROUP BY c.`etailer_order_number`,c.`status`");
			}
		}
		if($request->warehouseId=='' && $request->client_id!=''){
			$clientId = $request->client_id;
			if($request->report_type == 'billing_shipped_order'){
				$dataget =\DB::select("SELECT SUM(order_packages.`package_num`) AS packageTotal,warehouse,c.*,order_packages.`shipped_qty`, order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`,GROUP_CONCAT(CONCAT(order_packages.`ETIN`,' - QTY. ',order_packages.`shipped_qty`)) AS ETIN,GROUP_CONCAT(master_product.`alternate_ETINs`) AS alternate_ETINs
				FROM (
				SELECT order_summary.*, sub_order_number,warehouse, order_details.`transit_days` AS transit_days
				,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name,order_details.status
				FROM order_summary
				LEFT JOIN order_details
				ON order_summary.`etailer_order_number` = order_details.`order_number`
				LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_details.`service_type_id`
				LEFT JOIN carriers ON carriers.id = order_details.`carrier_id`
				LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				WHERE order_summary.`client_id` = $clientId and (order_details.`status`= 6 OR order_details.`status` = 13)
				GROUP BY order_summary.`etailer_order_number`, order_details.`status`
				) AS c
				INNER JOIN order_packages
				ON c.`sub_order_number` = order_packages.`order_id`
				LEFT JOIN master_product ON master_product.`ETIN`= order_packages.`ETIN`
				WHERE order_packages.`ship_date`>='$request->from_date' and order_packages.`ship_date`<='$request->to_date'
				GROUP BY c.`etailer_order_number`,c.`status`");
			}
		}
		if($request->warehouseId!='' && $request->client_id==''){
			$warehouseID = $request->warehouseId;
			if($request->report_type == 'billing_shipped_order'){
				$dataget =\DB::select("SELECT SUM(order_packages.`package_num`) AS packageTotal,warehouse,c.*,order_packages.`shipped_qty`, order_packages.`package_num`, order_packages.`tracking_number`, order_packages.`ship_date`, 
				order_packages.`shipping_label_creation_time`,GROUP_CONCAT(CONCAT(order_packages.`ETIN`,' - QTY. ',order_packages.`shipped_qty`)) AS ETIN,GROUP_CONCAT(master_product.`alternate_ETINs`) AS alternate_ETINs
				FROM (
				SELECT order_summary.*, sub_order_number,warehouse, order_details.`transit_days` AS transit_days
				,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name,order_details.status
				FROM order_summary
				LEFT JOIN order_details
				ON order_summary.`etailer_order_number` = order_details.`order_number`
				LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_details.`service_type_id`
				LEFT JOIN carriers ON carriers.id = order_details.`carrier_id`
				LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
				WHERE order_details.`warehouse` = '$warehouseID'and (order_details.`status`= 6 OR order_details.`status` = 13)
				GROUP BY order_summary.`etailer_order_number`, order_details.`status`
				) AS c
				INNER JOIN order_packages
				ON c.`sub_order_number` = order_packages.`order_id`
				LEFT JOIN master_product ON master_product.`ETIN`= order_packages.`ETIN`
				WHERE order_packages.`ship_date`>='$request->from_date' and order_packages.`ship_date`<='$request->to_date'
				GROUP BY c.`etailer_order_number`,c.`status`");
			}
		}
		if($type)
		{
			$request->reportSchedule= true;
			return  Excel::download(new ReportExport($dataget,$request,'order_report'),'order_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx');
		}
        return  Excel::download(new ReportExport($dataget,$request,'billing_report'),'order_'.$request->report_type.'_report' . date('Ymdi') . '.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
	}

	public function setReportSchedule($id=null){
		$selected_smart_filter = [];
		$visible_filters = [];
		$hidden_cols = '';
		$visible_columns = '';
		$smart_filter = [];
		$hidden_cols_arr = [];
		$not_default_columns = [];
		$main_filter = [];
		$max_chars_columns = '';
		$product_listing_filter = ProductListingFilter::where('type','product')->orderBy('sorting_order')->get();
		$smart_filters = SmartFilter::where('created_by',Auth::user()->id)->where('type','product')->get();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->get();
        $client = Client::get()->pluck('company_name','id')->toArray();
		$not_default_columns = ProductListingFilter::where('is_default','!=',1)->where('type','product')->pluck('sorting_order','id')->toArray();
		if(!empty($not_default_columns)){
			$hidden_cols = implode(',',$not_default_columns);
		}
		return view('cranium.reports.reportbuilder.schedule',['client'=>$client,'warehouse'=>$warehouse,'product_listing_filter' => $product_listing_filter, 'smart_filters' => $smart_filters, 'selected_smart_filter' => $selected_smart_filter, 'id' => $id, 'hidden_cols' => $hidden_cols, 'visible_columns' => $visible_columns, 'visible_filters' => $visible_filters,'smart_filter' => $smart_filter,'main_filter' => $main_filter, 'hidden_cols_arr' => $hidden_cols_arr ]);
	}
	public function setReportScheduleSave(Request $request){
		$own_field = null;
		if(isset($request->columns) && $request->report_type=="own"){
			$own_field = json_encode($request->columns);
		}
		if(isset($request->own_inventory_report_type) && $request->report_type=="own_inventory"){
			$own_field = json_encode($request->own_inventory_report_type);
		} 
		if(isset($request->own_order_report_type) && $request->report_type=="own_order"){
			$own_field = json_encode($request->own_order_report_type);
		}
		ScheduleReports::create([
			'emails' => $request->emails,
			'report'   => $request->report,
			'report_type'   => $request->report_type,
			'client_id' =>$request->client_id,
			'warehouseId' =>$request->warehouseId,
			'from_date' =>$request->from_date,
			'to_date' =>$request->to_date,
			'own_report_field' =>$own_field,
			'schedule_type' =>$request->schedule_type,
			'schedule_value' =>$request->schedule_value,
		]);
	}
	public function generateScheduleReport($request) {
		$date = date('Ymdi');
		$emails = explode(',',$request->emails);
		if($request->report == 'product_report'){
            $data = $this->getProductReport($request,'Schedule');
			$path = $data->getFile()->getRealPath();
			Mail::send('cranium.reports.reportbuilder.mail', [], function($message) use($path, $date,$request,$emails) {
                $message->to($emails)->subject('Reports');
                $message->attach($path, ['as' => 'Product_'.$request->report_type.'_report'. $date .'.xlsx']);
            });
		}
		if($request->report == 'inventory_report'){
			$data = $this->getInventoryReport($request,'Schedule');
			$path = $data->getFile()->getRealPath();
			Mail::send('cranium.reports.reportbuilder.mail', [], function($message) use($path, $date,$request,$emails) {
				$message->to($emails)->subject('Reports');
				$message->attach($path, ['as' => 'inventory_'.$request->report_type.'_report'. $date .'.xlsx']);
			});
		}
		if($request->report == 'order_report'){
			$data = $this->getOrderReport($request,'Schedule');
			$path = $data->getFile()->getRealPath();
			Mail::send('cranium.reports.reportbuilder.mail', [], function($message) use($path, $date,$request,$emails) {
				$message->to($emails)->subject('Reports');
				$message->attach($path, ['as' => 'order_'.$request->report_type.'_report'. $date .'.xlsx']);
			});
		}
		if($request->report == 'user_report'){
			return $this->getUserReport($request);
		}
		if($request->report == 'material_report'){
			return $this->getMaterialReport($request);
		}
		if($request->report == 'billing_report'){
			$data = $this->getBillingReport($request,'Schedule');
			$path = $data->getFile()->getRealPath();
			Mail::send('cranium.reports.reportbuilder.mail', [], function($message) use($path, $date,$request,$emails) {
				$message->to($emails)->subject('Reports');
				$message->attach($path, ['as' => 'order_'.$request->report_type.'_report'. $date .'.xlsx']);
			});
		}
    }
}
/*
SELECT warehouse,c.*,GROUP_CONCAT(order_packages.`tracking_number`),order_packages.`ship_date`
FROM (
 SELECT order_summary.*, sub_order_number,warehouse, SUM(order_details.`transit_days`) AS transit_days
 ,shipping_service_types.`service_name`,carriers.`company_name`,clients.`company_name` AS client_company_name,
 GROUP_CONCAT(CONCAT(order_details.`ETIN`,' - QTY. ',order_details.`quantity_fulfilled`)) AS ETIN,order_details.status
 FROM order_summary
 LEFT JOIN order_details
 ON order_summary.`etailer_order_number` = order_details.`order_number`
 LEFT JOIN shipping_service_types ON shipping_service_types.`id` = order_summary.`shipment_type`
 LEFT JOIN carriers ON carriers.id = order_summary.`carrier_id`
 LEFT JOIN clients ON clients.`id` = order_summary.`client_id`
 WHERE purchase_date>='2023-03-01' AND purchase_date<='2023-03-31'
 AND order_summary.`etailer_order_number`=11267
 AND (order_details.`status`= 6 OR order_details.`status` = 13)
 GROUP BY order_summary.`etailer_order_number`
) AS c
INNER JOIN order_packages
ON c.`sub_order_number` = order_packages.`order_id`
GROUP BY c.`etailer_order_number`

*/ 