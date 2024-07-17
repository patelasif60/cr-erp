<?php

namespace App\Http\Controllers;

use App\User;
use App\Client;

use DataTables;
use App\Carrier;
use App\HotRoute;
use App\WareHouse;
use Carbon\Carbon;
use App\OrderTypes;
use App\AisleMaster;
use App\MasterShelf;
use App\OrderDetail;
use App\SmartFilter;
use App\FaultCodeKey;
use App\OrderHistory;
use App\OrderPackage;
use App\OrderSummary;
use App\MasterProduct;
use App\EtailerService;
use App\OrderReshipFault;
use App\ReshipReasonCode;
use App\OrderDetailsStatus;
use Illuminate\Support\Str;
use App\ShippingServiceType;
use Illuminate\Http\Request;
use App\ProductListingFilter;
use App\OrderAutomaticUpgrades;
use App\SubOrderShippingDetails;
use Illuminate\Support\Facades\DB;
use App\ClientChannelConfiguration;
use App\MasterProductKitComponents;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Repositories\NotificationRepository;

class OrdersController extends Controller
{

    public function __construct(MasterProduct $masterProduct, SmartFilter $SmartFilter ,ProductListingFilter $ProductListingFilter, OrderDetail $OrderDetail, NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->masterProduct = $masterProduct;
		$this->SmartFilter = $SmartFilter;
		$this->ProductListingFilter = $ProductListingFilter;
		$this->OrderDetail = $OrderDetail;
		$this->NotificationRepository = $NotificationRepository;
	}

    public function index($id = null){
		
		if(auth()->user()->client != ''){
			return redirect(route('clients.edit',auth()->user()->client));
		}
		$selected_smart_filter = [];
		$visible_filters = [];
		$hidden_cols = '';
		$visible_columns = '';
		$smart_filter = [];
		$hidden_cols_arr = [];
		$not_default_columns = [];
		$main_filter = [];
		$max_chars_columns = '';
		$product_listing_filter = ProductListingFilter::where('type','order')->orderBy('sorting_order')->get();
		$smart_filters = $this->SmartFilter::where('created_by',Auth::user()->id)->where('type','order')->get();
		if($id != NULL){
			$all_columns = $this->ProductListingFilter::where('type','order')->pluck('sorting_order','id')->toArray();
			
			$smart_filter = $this->SmartFilter->find($id);

			$selected_smart_filter = json_decode($smart_filter->filter_preferences,true);
			$visible_columns = explode(',',$smart_filter->visible_columns);
			$visible_filters = explode(',',$smart_filter->visible_filters);
			if(!empty($all_columns)){
				$hidden_cols_arr = array_diff($all_columns,$visible_columns);
				$hidden_cols = implode(',',$hidden_cols_arr);
			}
			$main_filter = json_decode($smart_filter->main_filter,true);
		}else{
			$not_default_columns = $this->ProductListingFilter::where('is_default','!=',1)->where('type','order')->pluck('sorting_order','id')->toArray();
			if(!empty($not_default_columns)){
				$hidden_cols = implode(',',$not_default_columns);
			}
		}

		$count = OrderSummary::whereIn('order_status', [19, 21, 22, 23])->count();

		return view('orders.index',[
			'product_listing_filter' => $product_listing_filter, 
			'smart_filters' => $smart_filters, 
			'selected_smart_filter' => $selected_smart_filter, 
			'id' => $id, 
			'hidden_cols' => $hidden_cols, 
			'visible_columns' => $visible_columns, 
			'visible_filters' => $visible_filters,
			'smart_filter' => $smart_filter,
			'main_filter' => $main_filter,
			'hidden_cols_arr' => $hidden_cols_arr,
			'new_manual_count' => $count
		]);

    }

	public function getOptimizedorders(Request $request)
    {
		if ($request->ajax()) {

            $dataget = DB::table('order_summary')
				->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
				->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
				->leftjoin('order_details as od', 'od.order_number', '=', 'order_summary.etailer_order_number')
				->leftjoin('order_packages as op', 'op.order_id', '=', 'od.sub_order_number')
				->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name',DB::raw('group_concat(distinct op.ship_date separator \' || \') as ship_dates'),DB::raw('group_concat(distinct od.warehouse separator \' , \') as warehouse'),DB::raw('group_concat(distinct od.transit_days separator \' , \') as transit_days'));

			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
			if(isset($request->client_order) && $request->client_order == 1){
				$dataget->where('order_summary.client_id',$request->client_id);
			} 
			if(isset($filter_val)){
				foreach($filter_val as $key => $row_val){
					if(isset($row_val[$key])){

						$search_value_key = $row_val[$key];
						$search_value = '';
						if(isset($row_val[$search_value_key])){
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						for($i = 1; $i < 10; $i++){
							if($column_name == 'product_subcategory'.$i){
								$select_table = 'subcat'.$i;
							}
						}

						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$key = $key === 'client_name' ? 'company_name' : $key;
								$dataget->where($key,$search_value);
							}

						}


						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->whereNotIN($select_table.'.'.$select_label_column,explode(',',$search_value));
							}else{
								$dataget->whereNotIN($key,explode(',',$search_value));
							}

						}
						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->Orwhere('order_summary.etailer_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.channel_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_source','LIKE','%'.$search_text.'%');

						$query->Orwhere('order_summary.channel_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.purchase_date','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_email','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address1','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address2','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address3','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_city','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_state','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_zip','LIKE','%'.$search_text.'%');$query->Orwhere('order_summary.ship_to_country','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.shipping_method','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.delivery_notes','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_total_price','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary_status.order_status_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.complete_date','LIKE','%'.$search_text.'%');


					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){
				foreach($main_filter as $key_main => $row_main_filter){
					if($row_main_filter != ''){
						// $excluded_array = ['product_tags','warehouses_assigned','product_listing_ETIN','alternate_ETINs','prop_65_ingredient','ingredients','parent_ETIN','prop_65_flag'];
						// if(in_array($key_main,$excluded_array)){
						// 	$unique_value = array_unique(explode(',',$row_main_filter));
						// 	$dataget->where(function($q) use($unique_value,$key_main){
						// 		if($unique_value){
						// 			foreach($unique_value as $row_un_val){
						// 				if($key_main == 'allergens'){
						// 					$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\',master_product.'.$key_main.') > "0"');
						// 				}else{
						// 					$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\','.$key_main.') > "0"');
						// 				}
						// 			}
						// 		}
						// 	});
						// }else{
							$dataget->where($key_main,'LIKE','%'.$row_main_filter.'%');
						// }
					}
				}
			}

			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$order_by = 'order_summary.id';
			$order = 'DESC';

			if(isset($request->order[0]['column'])){
				$order_by = $request->columns[$request->order[0]['column']]['name'];
				$order = $request->order[0]['dir'];
			}
			
			$dataget->orderBy($order_by,$order);

			$dataget->groupBy("order_summary.id");
			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			$limit = 12;
			if(isset($input['limit']))$limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			if($request->get('length') >= 0 && $limit != -1)
			{
				$offset = $request->get('start');
				$limit = $request->get('length');
				$dataget->skip($offset)->take($limit);	
			}


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $this->convertDateTimeTo12Hour($dataget->get());

			// dd($data);
			//return response()->json(['data' => $data]);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							
                })
			->addIndexColumn()
			->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
					$btn = '';
					
					$btn = '<a href="'.route('orders.view',$row->id).'"  class="edit btn btn-primary btn-sm">View</a>';
						
					

					return $btn;
			})
		// 	->addColumn('warehouse_and_transit', function($row){
		// 		$btn = '';
		// 		if($row->transit_days != ''){
		// 			$btn.='Transit Days: '.$row->transit_days;
		// 		}

		// 		if($row->warehouse != ''){
		// 			$btn.='<br>Warehouse: '.$row->warehouse;
		// 		}
				

		// 		return $btn;
		// })
			//->rawColumns(['action','warehouse_and_transit'])
			->rawColumns(['action'])
			->setTotalRecords($total)
			->setFilteredRecords($total)
			->skipPaging()
			->make(true);

        }
    }

	public function getOptimizedordersFilter(Request $request)
    {
		ini_set('max_execution_time', '0');
		ini_set('max_input_time', '0');
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		if ($request->ajax()) {

            $dataget = DB::table('order_summary')
				->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
				->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
				->leftjoin('order_details as od', 'od.order_number', '=', 'order_summary.etailer_order_number')
				->leftjoin('order_packages as op', 'op.order_id', '=', 'od.sub_order_number')
				->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name',DB::raw('group_concat(distinct op.ship_date separator \' || \') as ship_dates'),DB::raw('group_concat(distinct od.warehouse separator \' , \') as warehouse'),DB::raw('group_concat(distinct od.transit_days separator \' , \') as transit_days'));
			if($request->status != ''){
				$status = $request->status;
				if($status == 'on_hold'){
					$dataget->whereIN('order_summary.order_status',[10,11,12,13]);
				}
				if($status == 'action_needed'){
					$dataget->whereIN('order_summary.order_status',[7,8,9,14,15,16,25]);
				}
			}
			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
            //dd($request->main_filter);
			if(isset($filter_val)){				
				foreach($filter_val as $key => $row_val){
					$key = str_replace('_auto', '', $key);
					if(isset($row_val[$key])){

					$search_value_key = $row_val[$key];
					$search_value = '';						
					if(isset($row_val[$search_value_key])){
						$search_value = $row_val[$search_value_key];
					}

					if(strpos($search_value, 'canceled') !== false){
						str_replace("canceled", "cancelled", $my_str);
					}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');						
						
						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$key = $key === 'client_name' ? 'company_name' : $key;
								$dataget->where($key,$search_value);
							}

						}
						if($search_value_key == "multiple" && $search_value != ""){
							$dataget->whereIn('master_product.ETIN',explode(',',$search_value));
						}

						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									if( strpos($search_value, ',') !== false ) {
										$values = explode(',', $search_value);
										foreach($values as $value) {
											$query->where($select_table.'.'.$select_label_column, '!=',  ltrim(rtrim(ucwords($value))));
										}
								   	}
									else{
										$query->where($select_table.'.'.$select_label_column, '!=',  ltrim(rtrim(ucwords($search_value))));
									}
									
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									if( strpos($search_value, ',') !== false ) {
										$values = explode(',', $search_value);
										foreach($values as $value) {
											$query->where($key, '!=',  ltrim(rtrim(ucwords($value))));
										}
									}
									else{
										$query->where($key, '!=',  ltrim(rtrim(ucwords($search_value))));
									}
									
								});
							}
						}

						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}

				$range = [];
				foreach($filter_val as $key => $row_vals) {
					foreach($row_vals as $rv_key => $val) {
						if (!isset($val) || $val == '') continue;
						$rv_key = str_replace('_auto', '', $rv_key);
						if ($rv_key === 'onorbefore') {
							$key == 'ship_date'
							? $dataget->whereRaw('op.' . $key . ' <= \'' . $val . '\'')
							: $dataget->whereRaw('CONVERT(order_summary.' . $key . ', DATE) <= \'' . $val . '\'');
							break;
						} else if ($rv_key === 'onorafter') {
							$key == 'ship_date'
							? $dataget->whereRaw('op.' . $key . ' >= \'' . $val . '\'')
							: $dataget->whereRaw('CONVERT(order_summary.' . $key . ', DATE) >= \'' . $val . '\'');
							break;
						} else if ($rv_key === 'from') {
							$range[$key]['from'] = $val;
						} else if ($rv_key === 'to') {
							$range[$key]['to'] = $val;
						} 
					}
				}
				if (count($range) > 0) {
					foreach($range as $col => $values) {
						if (isset($values['from']) && isset($values['to'])) {
							$col == 'ship_date'
							? $dataget->whereRaw('op.' . $col . ' between \''.$values['from'].'\' and \''.$values['to'].'\' ')
							: $dataget->whereRaw('CONVERT(order_summary.' . $col . ', DATE) between \''.$values['from'].'\' and \''.$values['to'].'\' ');
						}						
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->Orwhere('order_summary.etailer_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.channel_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_source','LIKE','%'.$search_text.'%');

						$query->Orwhere('order_summary.channel_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.purchase_date','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_email','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address1','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address2','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address3','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_city','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_state','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_zip','LIKE','%'.$search_text.'%');$query->Orwhere('order_summary.ship_to_country','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.shipping_method','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.delivery_notes','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_total_price','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary_status.order_status_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.complete_date','LIKE','%'.$search_text.'%');

					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){	
				foreach($main_filter as $key => $row_main_filter){					
					if($row_main_filter != ''){
						$key_main = str_replace('_auto', '', $key);
						if ($key_main === 'client_name' || $key_main === 'order_status_name') {
							$key_main = $key_main === 'client_name' ? 'client_id' : 'order_status';
							$dataget->whereIn('order_summary.'.$key_main, explode(',', $row_main_filter));							
						} else if ($key_main === 'order_source' || $key_main === 'ship_to_state') {
							$dataget->whereIn($key_main, explode(',', $row_main_filter));							
						}else if ($key_main === 'warehouse') {
							$dataget->whereIn($key_main, explode(',', $row_main_filter));							
						} else {
							$dataget->where($key_main, 'LIKE','%'.implode(',', $row_main_filter).'%');						
						}
					}
				}
			}

			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$order_by = 'order_summary.id';
			$order = 'DESC';

			if(isset($request->order[0]['column'])){
				$order_by = $request->columns[$request->order[0]['column']]['data'];
				$order = $request->order[0]['dir'];
			}
			
			$dataget->orderBy($order_by,$order);


			$dataget->groupBy("order_summary.id");
			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			
			$limit = 12;
			if(isset($input['limit']))$limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			$offset = $request->get('start');
			$limit = $request->get('length');
			// dump($offset);
			// dump($limit);
			if ($limit != -1) {
				$dataget->skip($offset)->take($limit);
			}


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $this->convertDateTimeTo12Hour($dataget->get());
			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							
                })
			->addIndexColumn()
			->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
					$btn = '';
					
					$btn = '<a href="'.route('orders.view',$row->id).'"  class="edit btn btn-primary btn-sm">View</a>';
						
					

					return $btn;
			})
			->rawColumns(['action'])					
			->setTotalRecords($total)
			->setFilteredRecords($total)
			->skipPaging()
			->make(true);

        }
    }

	public function getOptimizedorders2(Request $request)
    {
		ini_set('max_execution_time', '0');
		ini_set('max_input_time', '0');
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		if ($request->ajax()) {

            $dataget = DB::table('order_summary')->whereIn('order_summary.order_status', [19, 21, 22, 23])
				->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
				->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
				->leftjoin('order_details as od', 'od.order_number', '=', 'order_summary.etailer_order_number')
				->leftjoin('order_packages as op', 'op.order_id', '=', 'od.sub_order_number')
				->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name',DB::raw('group_concat(distinct op.ship_date separator \' || \') as ship_dates'));

			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
			if(isset($filter_val)){
				foreach($filter_val as $key => $row_val){
					if(isset($row_val[$key])){

						$search_value_key = $row_val[$key];
						$search_value = '';
						if(isset($row_val[$search_value_key])){
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						for($i = 1; $i < 10; $i++){
							if($column_name == 'product_subcategory'.$i){
								$select_table = 'subcat'.$i;
							}
						}

						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$dataget->where($key,$search_value);
							}

						}


						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'not like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'not like', '%'.$value.'%');
									}
								});
							}

						}
						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->Orwhere('order_summary.etailer_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.channel_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_source','LIKE','%'.$search_text.'%');

						$query->Orwhere('order_summary.channel_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.purchase_date','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_email','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address1','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address2','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address3','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_city','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_state','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_zip','LIKE','%'.$search_text.'%');$query->Orwhere('order_summary.ship_to_country','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.shipping_method','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.delivery_notes','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_total_price','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary_status.order_status_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.complete_date','LIKE','%'.$search_text.'%');


					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){		
				foreach($main_filter as $key_main => $row_main_filter){					
					if($row_main_filter != ''){
						// $excluded_array = ['product_tags','warehouses_assigned','product_listing_ETIN','alternate_ETINs','prop_65_ingredient','ingredients','parent_ETIN','prop_65_flag'];
						// if(in_array($key_main,$excluded_array)){
						// 	$unique_value = array_unique(explode(',',$row_main_filter));
						// 	$dataget->where(function($q) use($unique_value,$key_main){
						// 		if($unique_value){
						// 			foreach($unique_value as $row_un_val){
						// 				if($key_main == 'allergens'){
						// 					$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\',master_product.'.$key_main.') > "0"');
						// 				}else{
						// 					$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\','.$key_main.') > "0"');
						// 				}
						// 			}
						// 		}
						// 	});
						// }else{
							$dataget->where($key_main,'LIKE','%'.$row_main_filter.'%');
						// }
					}
				}
			}

			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$dataget->groupBy("order_summary.id");
			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			$limit = 12;
			if(isset($input['limit'])) $limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			if($request->get('length') >= 0 && $limit != -1)
			{
				$offset = $request->get('start');
				$limit = $request->get('length');
				$dataget->skip($offset)->take($limit);	
			}


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $this->convertDateTimeTo12Hour($dataget->get());

			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							
                })
			->addIndexColumn()
			->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
					$btn = '';
					
					$btn = '<a href="'.route('orders.view',$row->id).'"  class="edit btn btn-primary btn-sm">View</a>';
						
					

					return $btn;
			})
			->rawColumns(['action'])
			->setTotalRecords($total)
			->setFilteredRecords($total)
			->skipPaging()
			->make(true);

        }
    }

	public function getOptimizedordersFilter2(Request $request)
    {
		if ($request->ajax()) {

            $dataget = DB::table('order_summary')->whereIn('order_summary.order_status', [19, 21, 22, 23])
				->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
				->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
				->leftjoin('order_details as od', 'od.order_number', '=', 'order_summary.etailer_order_number')
				->leftjoin('order_packages as op', 'op.order_id', '=', 'od.sub_order_number')
				->select('order_summary.*','order_summary_status.order_status_name', 'clients.company_name as client_name',DB::raw('group_concat(distinct op.ship_date separator \' || \') as ship_dates'));
			if($request->status != ''){
				$status = $request->status;
				if($status == 'on_hold'){
					$dataget->whereIN('order_summary.order_status',[10,11,12,13]);
				}
				if($status == 'action_needed'){
					$dataget->whereIN('order_summary.order_status',[14,15,16]);
				}
			}
			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
            //dd($request->main_filter);
			if(isset($filter_val)){
				foreach($filter_val as $key => $row_val){
					if(isset($row_val[$key])){

						$search_value_key = $row_val[$key];
						$search_value = '';
						if(isset($row_val[$search_value_key])){
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						
						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$dataget->where($key,$search_value);
							}

						}
						if($search_value_key == "multiple" && $search_value != ""){
							$dataget->whereIn('master_product.ETIN',explode(',',$search_value));
						}

						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'not like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'not like', '%'.$value.'%');
									}
								});
							}

						}
						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}

				$range = [];
				foreach($filter_val as $key => $row_vals) {
					foreach($row_vals as $rv_key => $val) {
						if (!isset($val) || $val == '') continue;
						if ($rv_key === 'onorbefore') {
							$key == 'ship_date'
							? $dataget->whereRaw('op.' . $key . ' <= \'' . $val . '\'')
							: $dataget->whereRaw('CONVERT(order_summary.' . $key . ', DATE) <= \'' . $val . '\'');
							break;
						} else if ($rv_key === 'onorafter') {
							$key == 'ship_date'
							? $dataget->whereRaw('op.' . $key . ' >= \'' . $val . '\'')
							: $dataget->whereRaw('CONVERT(order_summary.' . $key . ', DATE) >= \'' . $val . '\'');
							break;
						} else if ($rv_key === 'from') {
							$range[$key]['from'] = $val;
						} else if ($rv_key === 'to') {
							$range[$key]['to'] = $val;
						} 
					}
				}
				if (count($range) > 0) {
					foreach($range as $col => $values) {
						$col == 'ship_date'
						? $dataget->whereRaw('op.' . $col . ' between \''.$values['from'].'\' and \''.$values['to'].'\' ')
						: $dataget->whereRaw('CONVERT(order_summary.' . $col . ', DATE) between \''.$values['from'].'\' and \''.$values['to'].'\' ');
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->Orwhere('order_summary.etailer_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.channel_order_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_source','LIKE','%'.$search_text.'%');

						$query->Orwhere('order_summary.channel_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.purchase_date','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_email','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.customer_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address1','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address2','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_address3','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_city','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_state','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_zip','LIKE','%'.$search_text.'%');$query->Orwhere('order_summary.ship_to_country','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.ship_to_phone','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.shipping_method','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.delivery_notes','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.order_total_price','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary_status.order_status_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('order_summary.complete_date','LIKE','%'.$search_text.'%');

					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){
				foreach($main_filter as $key_main => $row_main_filter){					
					if($row_main_filter != ''){
						if ($key_main === 'client_name' || $key_main === 'order_status_name') {
							$key_main = $key_main === 'client_name' ? 'client_id' : 'order_status';
							$dataget->whereIn('order_summary.'.$key_main, explode(',', $row_main_filter));							
						} else if ($key_main === 'order_source' || $key_main === 'ship_to_state') {
							$dataget->whereIn($key_main, explode(',', $row_main_filter));		
						} else {
							$dataget->where($key_main, 'LIKE','%'.implode(',', $row_main_filter).'%');						
						}
					}
				}
			}



			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$dataget->groupBy("order_summary.id");
			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			
			$limit = 12;
			if(isset($input['limit']))$limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			$offset = $request->get('start');
			$limit = $request->get('length');
			// dump($offset);
			// dump($limit);
			if ($limit != -1) {
				$dataget->skip($offset)->take($limit);
			}


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $this->convertDateTimeTo12Hour($dataget->get());
			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							
                })
			->addIndexColumn()
			->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
					$btn = '';
					
					$btn = '<a href="'.route('orders.view',$row->id).'"  class="edit btn btn-primary btn-sm">View</a>';
						
					

					return $btn;
			})
			->rawColumns(['action'])					
			->setTotalRecords($total)
			->setFilteredRecords($total)
			->skipPaging()
			->make(true);

        }
    }

	public function view($id){
		$row = OrderSummary::find($id);
		$whs = WareHouse::all();
		$items = OrderDetail::with('product')
			->leftjoin('order_details_status as ods', 'ods.id', '=', 'order_details.status')
			->leftjoin('shipping_service_types as sst','sst.id', '=', 'order_details.service_type_id')
			->leftjoin('carriers','carriers.id', '=', 'order_details.carrier_id')
			->where('order_number',$row->etailer_order_number)
			->select('ods.status', 'order_details.order_number','order_details.ETIN','order_details.id','order_details.quantity_ordered','sub_order_number','order_details.quantity_fulfilled','order_details.warehouse','sst.service_name', 'carriers.company_name as carrier_name')->get();
		$result = array();
		$kit_items = array();

		foreach ($items as $key => $item) {
			$result[$item->sub_order_number][$key] = $item;

			if (isset($item->product) && str_contains(strtolower($item->product->item_form_description), 'kit')) {

				$kit_com = MasterProductKitComponents::leftJoin('master_product',function($join){
					$join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
				})
				->select('master_product_kit_components.*')
				->where('master_product_kit_components.ETIN',$item->ETIN)->get();

				if($kit_com){
					foreach($kit_com as $key => $row_kit_components){
						$kit_items[$item->ETIN][$key] = $row_kit_components;						
					}
				}
			}
		}

		$dataget = DB::table('order_summary')
		->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
		->leftJoin('carriers', 'carriers.id', '=', 'order_summary.carrier_id')
		->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
		->where('order_summary.id', $id)
		->select('order_summary.*','order_summary_status.order_status_name as order_status_name', 'clients.company_name as client_name', 'carriers.company_name as carrier_name');

		$summary = $dataget->get()[0];

		$statuses = DB::select('SELECT * FROM order_summary_status WHERE 
					order_status_name like \'%review%\' or order_status_name like \'%hold%\' 
					or order_status_name = \'Invalid Shipment Type\' or order_status_name = \'Ready to Pick\'
					or order_status_name = \'Manual\' or order_status_name = \'New Manual\' or order_status_name = \'New\'');

		$shipment_types = DB::select("select od.sub_order_number from order_details od 
									where od.order_number = '" . $row->etailer_order_number . "'
									group by od.sub_order_number");
		$sub_order_ship_type = [];
		$sub_orders = [];
		if (isset($shipment_types)) {
			foreach($shipment_types as $st) {
				array_push($sub_orders, $st->sub_order_number);
			}
		}

		$status_present = false;
		foreach($statuses as $st) {
			if ($st->order_status_name == $summary->order_status_name) {
				$status_present = true;
				break;
			}
		}

		$ship_details = SubOrderShippingDetails::whereIn('sub_order_number', $sub_orders)->get();
		foreach ($ship_details as $sosd) {
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_name'] = $sosd->ship_to_name;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address_type'] = $sosd->ship_to_address_type;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address1'] = $sosd->ship_to_address1;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address2'] = $sosd->ship_to_address2;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address3'] = $sosd->ship_to_address3;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_city'] = $sosd->ship_to_city;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_state'] = $sosd->ship_to_state;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_zip'] = $sosd->ship_to_zip;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_phone'] = $sosd->ship_to_phone;
			$sub_order_ship_type[$sosd->sub_order_number]['shipping_method'] = $sosd->shipping_method;
			$sub_order_ship_type[$sosd->sub_order_number]['delivery_notes'] = $sosd->delivery_notes;			
		}

		$ship_details = OrderDetail::whereIn('sub_order_number', $sub_orders)
						->leftjoin('order_details_status', 'order_details_status.id', '=', 'order_details.status')
						->leftJoin('carriers', 'carriers.id', '=', 'order_details.carrier_id')
						->get(['carrier_id', 'service_type_id', 'sub_order_number', 'carriers.company_name as carrier_name', 'order_details.status', 'order_details_status.status as status_name','order_details.id']);
		foreach ($ship_details as $sosd) {
			$sub_order_ship_type[$sosd->sub_order_number]['carrier_id'] = $sosd->carrier_id;
			$sub_order_ship_type[$sosd->sub_order_number]['service_type_id'] = $sosd->service_type_id;
			$sub_order_ship_type[$sosd->sub_order_number]['carrier_name'] = $sosd->carrier_name;
			$sub_order_ship_type[$sosd->sub_order_number]['status'] = $sosd->status;
			$sub_order_ship_type[$sosd->sub_order_number]['status_name'] = $sosd->status_name;
			$sub_order_ship_type[$sosd->sub_order_number]['id'] = $sosd->id;
		}

		$carr = Carrier::all();

		$ups_st = ShippingServiceType::where('service_type', 'ups')->get();
		$fedex_st = ShippingServiceType::where('service_type', 'fedex')->get();
		$non_pickup_st = ShippingServiceType::where('service_type', 'non_person_pickup')->get();

		$fault_codes = FaultCodeKey::all();
		$reship_codes = ReshipReasonCode::all();
		$ots = OrderTypes::whereIn('id', [2, 3])->get();

		$np_wh = '';
		if ($summary->order_type_id == 3) {
			$d = OrderDetail::where('order_number', $summary->etailer_order_number)->first();
			if (isset($d)) {
				$np_wh = isset($d->warehouse) ? $d->warehouse : '';
			}
		}

		$wh_assigned = $this->getAssignedWarehouses($summary->etailer_order_number, null);
		$zip = $summary->ship_to_zip;

		$t_day = [];
		if (isset($zip)) {
			$zip = substr($zip, 0, 3);
            $transit_days = DB::table('ups_zip_zone_wh')->where('zip_3', $zip)->first();
			if (isset($transit_days)) {
				$t_day['WI'] = $transit_days->transit_days_WI;
				$t_day['PA'] = $transit_days->transit_days_PA;
				$t_day['NV'] = $transit_days->transit_days_NV;
				$t_day['OKC'] = $transit_days->transit_days_OKC;
			}

			$sql = 'select w.warehouses, hr.transit_days from hot_routes hr 
				inner join warehouses w on hr.wh_id = w.id 
				where zip = \''. $summary->ship_to_zip .'\'';
			$recs = DB::select($sql);
			if(isset($recs) && count($recs) > 0) {
				foreach($recs as $rec) {
					$t_day[$rec->warehouses] = $rec->transit_days;
				}
			}
		}
		// dd($t_day);

		$set_whs = OrderDetail::where('order_number', $summary->etailer_order_number)->whereNotNull('warehouse')
			->groupBy('warehouse')->select(['warehouse'])->pluck('warehouse')->toArray();

		$etailer_services = EtailerService::with(['upsShippingServiceType', 'fdxShippingServiceType'])->get();

		return view('orders.view',
			compact('summary','row','result', 'statuses', 'whs', 'sub_order_ship_type', 
					'carr', 'ups_st', 'fedex_st', 'kit_items', 'fault_codes', 'reship_codes', 
					'ots', 'np_wh', 'non_pickup_st', 'wh_assigned', 't_day', 'set_whs', 
					'status_present', 'etailer_services'));
	}

	public function create($client_id = null){
		$client = Client::get();
		$carr = Carrier::all();
		$ups_st = ShippingServiceType::where('service_type', 'ups')->get();
		$fedex_st = ShippingServiceType::where('service_type', 'fedex')->get();
		$non_pickup_st = ShippingServiceType::where('service_type', 'non_person_pickup')->get();
		$ots = OrderTypes::whereIn('id', [2, 3])->get();
		$etailer_services = EtailerService::with(['upsShippingServiceType', 'fdxShippingServiceType'])->get();
		return view('orders.create',compact('client','client_id', 'carr', 'ups_st', 'fedex_st', 'ots','non_pickup_st','etailer_services'));
	}

	public function store(Request $request, $client_id = null){
		
		$client = Client::find($request->client_id);
		if(!isset($client)){
			return response()->json([
				'error' => true,
				'msg' => 'Invalid Client'
			]);
		}

		if($request->order_type_id == 3){
			$request->validate([
				'ship_to_name' => 'required',
				'ship_to_address1' => 'required',
				'ship_to_city' => 'required',
				'ship_to_state' => 'required',
				'ship_to_zip' => 'required',
				'ship_to_country' => 'required',
				'client_id' => 'required',
			]);
		}
		else{
			$request->validate([
				'ship_to_name' => 'required',
				'ship_to_address1' => 'required',
				'ship_to_city' => 'required',
				'ship_to_state' => 'required',
				'ship_to_zip' => 'required',
				'ship_to_country' => 'required',
				'client_id' => 'required',
				'sum_carrier' => 'required',
				'sum_shipment_type' => 'required',
				'ship_to_phone' => 'required'
			]);
		}

		if($request->order_type_id != 3){
			$carr = Carrier::where('company_name', $request->sum_carrier)->first();
			if (!isset($carr)) {
				return response()->json([
					'error' => true,
					'msg' => 'Invalid Carrier'
				]);
			}
		}

		if($request->order_type_id == 1){
			return response()->json([
				'error' => true,
				'msg' => 'Order Type Auto not selected for Manual Orders'
			]);
		}

		$last_order = OrderSummary::orderBy('id', 'desc')->first();
		$last_order_number = !$last_order ? 9999 : $last_order->etailer_order_number;
		$order = new OrderSummary();
		$order->order_status = $client->is_enable == 2 ? 10 : 19;
		$order->old_status = 19;
		$order->etailer_order_number = $last_order_number+1;
		$order->order_source = 'Manual';
		$order->purchase_date = $this->changeTimeFormat($request->purchase_date);
		$order->channel_estimated_ship_date = $request->channel_estimated_ship_date;
		$order->must_ship_today = $request->must_ship_today;
		$order->release_date = $request->release_date;
		$order->customer_name = $request->customer_name;
		$order->customer_email = $request->customer_email;
		$order->customer_number = $request->customer_number;
		$order->ship_to_name = $request->ship_to_name;
		$order->ship_to_address_type = $request->ship_to_address_type;
		$order->ship_to_address1 = $request->ship_to_address1;
		$order->ship_to_address2 = $request->ship_to_address2;
		$order->ship_to_address3 = $request->ship_to_address3;
		$order->ship_to_city = $request->ship_to_city;
		$order->ship_to_state = $request->ship_to_state;
		$order->ship_to_zip = $request->ship_to_zip;
		$order->ship_to_country = $request->ship_to_country;
		$order->ship_to_phone = $request->ship_to_phone;
		$order->shipping_method = $request->shipping_method;
		$order->delivery_notes = $request->delivery_notes;
		$order->customer_shipping_price = $request->customer_shipping_price;
		if($request->order_type_id == 2){
			$order->shipment_type = $request->sum_shipment_type;
			$order->carrier_id = $carr->id;
		}
		if($request->order_type_id == 3){
			$carr = Carrier::where('company_name', 'Non-person Pickup')->first();
			$shipmentType = ShippingServiceType::where('service_type', 'non_person_pickup')->first();

			$order->shipment_type = $shipmentType->service_type?$shipmentType->service_type:NULL;
			$order->carrier_id = $carr->id?$carr->id:NULL;
		}
		$order->client_id = $request->client_id;
		$order->order_type_id = $request->order_type_id;
		$order->gift_message = isset($request->gift_message) && trim($request->gift_message) !== '' ? trim($request->gift_message) : NULL;
		$order->saturday_eligible = $request->sat_elli;
		$order->save();

		UpdateOrderHistory([
			'order_number' => $order->etailer_order_number,
			'detail' => 'Order #: '.$order->etailer_order_number .' has been Created Manually',
			'title' => 'Order Created',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($order)
		]);

		return response()->json([
			'url' => route('orders.view', $order->id),
			'error' => false,
			'msg' => 'Success'
		]);

	}

	public function edit($id,$client_id=null){
		$client = Client::get();
		$order = OrderSummary::leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')->select('order_summary.*','order_summary_status.order_status_name as order_status_name')->where('order_summary.id',$id)->first();
		
		$row = OrderSummary::find($id);
		$whs = WareHouse::all();
		$items = OrderDetail::with('product')
			->leftjoin('order_details_status as ods', 'ods.id', '=', 'order_details.status')
			->leftjoin('shipping_service_types as sst','sst.id', '=', 'order_details.service_type_id')
			->leftjoin('carriers','carriers.id', '=', 'order_details.carrier_id')
			->where('order_number',$row->etailer_order_number)
			->select('ods.status', 'order_details.order_number','order_details.ETIN','order_details.id','order_details.quantity_ordered','sub_order_number','order_details.quantity_fulfilled','order_details.warehouse','sst.service_name', 'carriers.company_name as carrier_name')->get();
		$result = array();
		$kit_items = array();

		foreach ($items as $key => $item) {
			$result[$item->sub_order_number][$key] = $item;

			if (str_contains(strtolower($item->product->item_form_description), 'kit')) {

				$kit_com = MasterProductKitComponents::leftJoin('master_product',function($join){
					$join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
				})
				->select('master_product_kit_components.*')
				->where('master_product_kit_components.ETIN',$item->ETIN)->get();

				if($kit_com){
					foreach($kit_com as $key => $row_kit_components){
						$kit_items[$item->ETIN][$key] = $row_kit_components;						
					}
				}
			}
		}

		$dataget = DB::table('order_summary')
		->leftjoin('clients', 'clients.id', '=', 'order_summary.client_id')
		->leftJoin('carriers', 'carriers.id', '=', 'order_summary.carrier_id')
		->leftjoin('order_summary_status', 'order_summary_status.id', '=', 'order_summary.order_status')
		->where('order_summary.id', $id)
		->select('order_summary.*','order_summary_status.order_status_name as order_status_name', 'clients.company_name as client_name', 'carriers.company_name as carrier_name');

		$summary = $dataget->get()[0];

		$statuses = DB::select('SELECT * FROM order_summary_status WHERE 
					order_status_name like \'%review%\' or order_status_name like \'%hold%\'');

		$shipment_types = DB::select("select od.sub_order_number from order_details od 
									where od.order_number = '" . $row->etailer_order_number . "'
									group by od.sub_order_number");
		$sub_order_ship_type = [];
		$sub_orders = [];
		if (isset($shipment_types)) {
			foreach($shipment_types as $st) {
				array_push($sub_orders, $st->sub_order_number);
			}
		}

		$ship_details = SubOrderShippingDetails::whereIn('sub_order_number', $sub_orders)->get();
		foreach ($ship_details as $sosd) {
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_name'] = $sosd->ship_to_name;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address_type'] = $sosd->ship_to_address_type;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address1'] = $sosd->ship_to_address1;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address2'] = $sosd->ship_to_address2;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_address3'] = $sosd->ship_to_address3;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_city'] = $sosd->ship_to_city;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_state'] = $sosd->ship_to_state;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_zip'] = $sosd->ship_to_zip;
			$sub_order_ship_type[$sosd->sub_order_number]['ship_to_phone'] = $sosd->ship_to_phone;
			$sub_order_ship_type[$sosd->sub_order_number]['shipping_method'] = $sosd->shipping_method;
			$sub_order_ship_type[$sosd->sub_order_number]['delivery_notes'] = $sosd->delivery_notes;			
		}

		$ship_details = OrderDetail::whereIn('sub_order_number', $sub_orders)
						->leftJoin('carriers', 'carriers.id', '=', 'order_details.carrier_id')
						->get(['carrier_id', 'service_type_id', 'sub_order_number', 'carriers.company_name as carrier_name', 'status']);
		foreach ($ship_details as $sosd) {
			$sub_order_ship_type[$sosd->sub_order_number]['carrier_id'] = $sosd->carrier_id;
			$sub_order_ship_type[$sosd->sub_order_number]['service_type_id'] = $sosd->service_type_id;
			$sub_order_ship_type[$sosd->sub_order_number]['carrier_name'] = $sosd->carrier_name;
			$sub_order_ship_type[$sosd->sub_order_number]['status'] = $sosd->status;
		}

		$carr = Carrier::all();

		$ups_st = ShippingServiceType::where('service_type', 'ups')->get();
		$fedex_st = ShippingServiceType::where('service_type', 'fedex')->get();

		$fault_codes = FaultCodeKey::all();
		$reship_codes = ReshipReasonCode::all();

		$etailer_services = EtailerService::with(['upsShippingServiceType', 'fdxShippingServiceType'])->get();

		return view('orders.edit',compact('summary','row','result', 'statuses', 'whs', 'sub_order_ship_type', 'carr', 'ups_st', 'fedex_st', 'kit_items', 'fault_codes', 'reship_codes','client','order','client_id','etailer_services'));

		
	}

	public function update(Request $request,$id,$clinet_id = null){
		$request->validate([
			'ship_to_name' => 'required',
			'ship_to_address_type' => 'required',
			'ship_to_address1' => 'required',
			'ship_to_city' => 'required',
			'ship_to_state' => 'required',
			'ship_to_zip' => 'required',
			'ship_to_country' => 'required',
			// 'shipping_method' => 'required',
			'client_id' => 'required'
		]);

		$order = OrderSummary::find($id);
		$order->purchase_date = $this->changeTimeFormat($request->purchase_date);
		$order->channel_estimated_ship_date = $request->channel_estimated_ship_date;
		$order->must_ship_today = $request->must_ship_today;
		$order->release_date = $request->release_date;
		$order->customer_name = $request->customer_name;
		$order->customer_email = $request->customer_email;
		$order->customer_number = $request->customer_number;
		$order->ship_to_name = $request->ship_to_name;
		$order->ship_to_address_type = $request->ship_to_address_type;
		$order->ship_to_address1 = $request->ship_to_address1;
		$order->ship_to_address2 = $request->ship_to_address2;
		$order->ship_to_address3 = $request->ship_to_address3;
		$order->ship_to_city = $request->ship_to_city;
		$order->ship_to_state = $request->ship_to_state;
		$order->ship_to_zip = $request->ship_to_zip;
		$order->ship_to_country = $request->ship_to_country;
		$order->ship_to_phone = $request->ship_to_phone;
		$order->shipping_method = $request->shipping_method;
		$order->delivery_notes = $request->delivery_notes;
		$order->customer_shipping_price = $request->customer_shipping_price;
		if($request->order_type_id == 3){
			$carr = Carrier::where('company_name', 'Non-person Pickup')->first();
			$shipmentType = ShippingServiceType::where('service_type', 'non_person_pickup')->first();
			
			$order->shipment_type = $shipmentType->service_type?$shipmentType->service_type:NULL;
			$order->carrier_id = $carr->id?$carr->id:NULL;
		}
		$order->client_id = $request->client_id;
		$order->save();

		return response()->json([
			'url' => route('orders.edit',[$order->id,$clinet_id]),
			'error' => false,
			'msg' => 'Success'
		]);

		UpdateOrderHistory([
			'order_number' => $order->etailer_order_number,
			'detail' => 'Order #: '.$order->etailer_order_number .' has been Updated Manually',
			'title' => 'Order Updated',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($order)
		]);

	}

	public function view_sub_order($order_number){
		$data = DB::select('select 
						distinct od.sub_order_number, od.fulfilled_by, od.warehouse,  
						sst.service_name, op.transit_actual as transit_time, ods.status, u.name  
						from order_details od 
						left join shipping_service_types sst on sst.id = od.service_type_id
						left join order_details_status ods on ods.id = od.status 
						left join order_packages op on op.order_id = od.sub_order_number 
						left join users u on u.id = od.picker_id 
						where od.order_number = ?', [$order_number]);

		$result = [];
		foreach($data as $res) {
			$pack_type = $this->getTemp($res->fulfilled_by, $res->sub_order_number);
			array_push($result, [
				'sub_order_number' => $res->sub_order_number,
                'fulfilled_by' => $res->fulfilled_by,
                'warehouse' => $res->warehouse,
                'service_name' => $res->service_name,
                'transit_time' => $res->transit_time,
                'pack_type' => $pack_type,
                'status' => $res->status,
				'name' => $res->name
			]);
		}

		return view('orders.view_sub_order',compact('result', 'order_number'));
	}

	public function update_qty(Request $request){
		$order_items = $request->order_item;
		if($order_items){
			foreach($order_items as $key => $row){
				DB::table('order_details')->where('id',$key)->update(['quantity_ordered' => $row, 'quantity_fulfilled' => $row]);
				$or = DB::table('order_details')->where('id',$key)->first();
				if($or){
					UpdateOrderHistory([
						'order_number' => $or->order_number,
            			'sub_order_number' => $or->sub_order_number,
						'detail' => 'Sub Order #: '.$or->sub_order_number .' with ETIN '.$or->ETIN.' Qty Updated',
						'title' => 'Sub order Qty Updated',
						'user_id' => auth()->user()->id,
						'reference' => 'PM',
						'extras' => json_encode($or)
					]);
				}
			}
		}

		$whs = $request->wh;
		if($whs){
			foreach($whs as $key => $row){
				DB::table('order_details')->where('id', $key)->update(['warehouse' => $row]);
				$or = DB::table('order_details')->where('id',$key)->first();
				if($or){
					UpdateOrderHistory([
						'order_number' => $or->order_number,
            			'sub_order_number' => $or->sub_order_number,
						'detail' => 'Sub Order #: '.$or->sub_order_number .' with ETIN '.$or->ETIN.' Warehouse Updated with '.$row,
						'title' => 'Sub Warehouse Updated',
						'user_id' => auth()->user()->id,
						'reference' => 'PM',
						'extras' => json_encode($or)
					]);
				}
			}
		}

		return redirect()->back()->with('success','Order Qty Updated');
	}

	public function add_product(Request $request, $order_number){
		$os = OrderSummary::where('etailer_order_number', $order_number)->first();
		$result_obj = null;

		if (isset($os)) {
			$result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $os->client_id. ',lobs)');
			// $search = $request->search;
			// $result_obj->where(function($q) use($search){
			// 	$q->where('ETIN',$search);
			// 	$q->orwhere('product_listing_name','LIKE','%'.$search.'%');
			// });
			$result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','cost');
		}		
		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();
		
		return view('orders.add_product', compact('products','order_number','request'));
	}

	public function getProductsAutoComplete(Request $request){
		$result_obj = MasterProduct::where('status','Active');
		if($request->search !== ''){
			$search = $request->search;
			$result_obj->where(function($q) use($search){
				$q->where('ETIN',$search);
				$q->orwhere('product_listing_name','LIKE','%'.$search.'%');
			});
			$result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'id','cost');
			$result = $result_obj->get()->toArray();
			return response()->json($result);
		}
	}

	public function store_product(Request $request){
		$summary = OrderSummary::where('etailer_order_number',$request->order_number)->first();

		$OrderDetail = new OrderDetail();
		$OrderDetail->order_number = $request->order_number;
		$OrderDetail->ETIN = $request->ETIN;
		$OrderDetail->ETIN_flag = 0;
		$OrderDetail->quantity_ordered = $request->quantity_ordered;
		$OrderDetail->channel_unit_price = $request->channel_unit_price;
		$OrderDetail->status = $summary->order_status == 10 ? 18 : 1;
		if($request->frm_type == 'reship')
		{
			$orddetail = OrderDetail::where('order_number', $request->order_number)->orderByDesc('id')->first();
			$OrderDetail->status = 9;
			$OrderDetail->sub_order_number = $request->re_sub_order;
			$OrderDetail->quantity_fulfilled = $request->quantity_ordered;
			$OrderDetail->service_type_id = $orddetail->service_type_id;
			$OrderDetail->carrier_id =$orddetail->carrier_id;
			$OrderDetail->warehouse =$orddetail->warehouse;

		}
		$result = $OrderDetail->save();

		UpdateOrderHistory([
			'order_number' => $request->order_number,
			'detail' => 'Order #: '.$request->order_number .' ETIN '.$request->ETIN.' Added',
			'title' => 'Order Product Added',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($request)
		]);
		
		if ($summary->order_status != 10) {
			$this->OrderDetail->checkFulfilledBy($summary, $OrderDetail);
			if($request->frm_type != 'reship')
			{
				$this->OrderDetail->createSubOrders($summary, $OrderDetail);
			}
		}
		
		return response()->json([
			'error' => false,
			'msg' => 'Success'
		]);
	}

	public function product_wh_count($etin) {

		$getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();

		$mp = MasterProduct::where('ETIN', $etin)->first();
		$units_in_pack_child = 0;
		$units_in_pack_parent = 0;
		$is_kit = false;
		$etins = [];

		if (isset($mp) && isset($mp->item_form_description) && str_contains(strtolower($mp->item_form_description), 'kit')) {
            $kit_comps = MasterProductKitComponents::leftJoin('master_product', function($join){
                $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
            })
            ->select('master_product_kit_components.*')
            ->where('master_product_kit_components.ETIN', $mp->ETIN)->get();
            
            if($kit_comps && count($kit_comps) > 0){
                foreach($kit_comps as $row_kit_components){
                    array_push($etins, $row_kit_components->components_ETIN);
                }
                $is_kit = true;
            }
        } else if (isset($mp) && isset($mp->parent_ETIN)) {			
			$etin = $mp->parent_ETIN;
			$units_in_pack_child = $mp->unit_in_pack;

			$parent = MasterProduct::where('ETIN', $etin)->first();
			if(!$parent){
				return response()->json([
					'error' => true,
					'msg' => 'Parent product not present into our app '.$etin,
				]);
			}
			$units_in_pack_parent = $parent->unit_in_pack;
		}

		
		$onHandQty = [];
		foreach ($getwarehouses as $warehouselist) {
			$AisleMaster = AisleMaster::where('warehouse_id',$warehouselist->id)->pluck('id')->toArray();
			$OD = new OrderDetail;
        	$GetAvailableQty = $OD->GetAvailableQty($etin, $warehouselist->warehouses);
			if ($is_kit) {
                $masterShelfSum = 0;
                foreach($etins as $et) {
                    $shelfSum = MasterShelf::where('ETIN',$et)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
                    $mp = MasterProduct::where('ETIN', $et)->first();
                    if (isset($mp) && isset($mp->parent_ETIN)) {		
                        $etin = $mp->parent_ETIN;
                        $units_in_pack_child = $mp->unit_in_pack;
            
                        $parent = MasterProduct::where('ETIN', $etin)->first();
                        if($parent){
                            $units_in_pack_parent = $parent->unit_in_pack;
    
                            $masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
                            if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
                                $count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
                            }
                            if ($masterShelfSum < ($shelfSum + $count)) {
                                $masterShelfSum = $shelfSum + $count;
                            }
                        }                    
                    } else {
                        $sum = MasterShelf::whereIn('ETIN',$etins)
                            ->whereIN('aisle_id',$AisleMaster)
                            ->where('cur_qty', '>', 0)
							->whereIN('location_type_id', [1,2])
                            ->orderBy('cur_qty', 'asc')
                            ->limit(1)
                            ->sum('cur_qty');
                        $masterShelfSum += !isset($sum) && $sum <= 0 ? 0 : $sum;                        
                    }
                }                
            } else {
				$masterShelfSum = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
				$masterShelf_child_sum = MasterShelf::where('ETIN',$mp->ETIN)->whereIN('aisle_id',$AisleMaster)->whereIN('location_type_id', [1,2])->sum('cur_qty');
				
				$count = 0;
				if (isset($masterShelfSum) && $masterShelfSum >= 0 && $units_in_pack_child > 0) {
					$count = floor(($masterShelfSum * $units_in_pack_parent)/$units_in_pack_child);
					if($masterShelf_child_sum > 0){
						$count = $count + $masterShelf_child_sum;
					}
				}
			}  
			
			$return = isset($count) && $count > 0 ? $count : $masterShelfSum;
			$AVQ = $return - $GetAvailableQty;
			$onHandQty[] = [
				'count' => $return,
				'name' => $warehouselist->warehouses,
				'GetAvailableQty' => $GetAvailableQty,
				'count_1' => $AVQ < 0 ? 0 : $AVQ
			];
			// $onHandQty[$warehouselist->id]['count'] =  $masterShelfSum;
			// $onHandQty[$warehouselist->id]['name'] = $warehouselist->warehouses;
		}

		

		return response(['data' => $onHandQty]);
	}

	private function convertDateTimeTo12Hour($data) {

		$toReturn = [];
		foreach ($data as $datum) {

			$time = strtotime($datum->created_at);
			$new_time = date("m/d/Y g:i:s A", $time);
			$datum->created_at = $new_time;
			array_push($toReturn, $datum);
		}
		return $toReturn;
	}

	private function getTemp($fullfilledBy, $subOrderNumber) {
        switch(strtolower($fullfilledBy)) {
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

	private function getAssignedWarehouses($order_number, $sub_order_number) {
		$wh_assigned = [];
		$etins = null;
		if ($order_number != null) {
			$etins = OrderDetail::where('order_number', $order_number)->get(['ETIN'])->pluck('ETIN')->toArray();
		} else {
			$etins = OrderDetail::where('sub_order_number', $sub_order_number)->get(['ETIN'])->pluck('ETIN')->toArray();
		}

		$mps = MasterProduct::whereIn('ETIN', $etins)->pluck('warehouses_assigned')->toArray();
		if (isset($mps) && count($mps) > 0) {
			foreach($mps as $mp) {
				foreach(explode(',', $mp) as $wh) { array_push($wh_assigned, $wh); }
			}
		}
		return array_unique($wh_assigned);
	}

	public function changeStatus(Request $request) {

		try{

		
		
		$values = $request->all();
		$user_id = auth()->user()->id;

		$summary_id = $values['sum_id'];

		$order_summary = OrderSummary::where('id', $summary_id)->first();
		$OD = OrderDetail::where('order_number', $order_summary->etailer_order_number)->first();
		if (!isset($order_summary)) {
			return response()->json([
				'error' => true,
				'msg' => 'Invalid Summary Id'
			]);
		}
		
		$day = strtolower(jddayofweek(date("w", strtotime('now')) - 1, 1));		
		if (!in_array($day,['thursday', 'friday']) && $order_summary->saturday_eligible == 0 && $request->sat_elli == 1) {

			return response()->json([
				'error' => true,
				'msg' => 'Saturday Orders are created only on Thursday/Friday'
			]);
		}		

		$is_sat = $request->sat_elli == 1;

		$type_id = $values['type_id'];
		$wh_np = $values['wh_np'];
		$new_status = $values['new_status'];
		$old_status = $values['old_status'];
		$must_ship = isset($values['must_ship']) && $values['must_ship'] === '1'
				? date('Y-m-d')
				: null; 

		$hold_release_date = $values['hold_release_date'];

		if ($new_status !== '-1' && $new_status !== '-2' && $new_status !== '25' && $old_status !== '25') {

			if ($hold_release_date !== NULL && Carbon::createFromFormat('Y-m-d', $hold_release_date)->isPast() ) {
				return response()->json([
					'error' => true,
					'msg' => 'Release date cannot be past'
				]);
			}

			$order_summary->old_status = $old_status;
			$order_summary->order_status = $new_status;
			$order_summary->release_date = $hold_release_date;
		} 
		
		
		if ($new_status === '25' && in_array($old_status, array('3', '4', '5', '6'))) {			
			
			$OrderDetail = new OrderDetail();
			$result = $OrderDetail->UpdateOrderStatus($OD->id,15,$user_id);
			if($result == "Error"){
				return response()->json([
					'error' => true,
					'msg' => 'Something went wrong'
				]);	
			}

			$order_summary->old_status = $old_status;
			$order_summary->order_status = $new_status;
			$order_summary->save();


			OrderDetail::where('order_number', $order_summary->etailer_order_number)->update(['status' => 15]);			
			
			return response()->json([
				'error' => false,
				'msg' => 'Status Updated'
			]);
		} 
		
		if ($old_status === '25' && $new_status === '2') {
			$OrderDetail = new OrderDetail();
			$result = $OrderDetail->UpdateOrderStatus($OD->id,1,$user_id);
			if($result == "Error"){
				return response()->json([
					'error' => true,
					'msg' => 'Something went wrong'
				]);	
			}

			$order_summary->old_status = $old_status;
			$order_summary->order_status = $new_status;
			$order_summary->save();

			OrderDetail::where('order_number', $order_summary->etailer_order_number)->update(['status' => 1]);

			return response()->json([
				'error' => false,
				'msg' => 'Status Updated'
			]);
		}

		$order_summary->must_ship_today = $must_ship;
		$order_summary->order_type_id = $type_id;
		if($type_id == 3){
			$carr = Carrier::where('company_name', 'Non-person Pickup')->first();
			$shipmentType = ShippingServiceType::where('service_type', 'non_person_pickup')->first();
			
			$order_summary->shipment_type = isset($shipmentType->service_type)?$shipmentType->service_type:NULL;
			$order_summary->carrier_id = isset($carr->id)?$carr->id:NULL;
		}
		$order_summary->receive_notification = $request->receive_notification;
		$order_summary->po_number = $request->po_number;
		$order_summary->bol_number = $request->bol_number;
		$order_summary->saturday_eligible = $request->sat_elli;
		$order_summary->gift_message = isset($request->gift_message) && trim($request->gift_message) !== '' ? trim($request->gift_message) : NULL;
		$order_summary->save();

		OrderDetail::where('order_number', $order_summary->etailer_order_number)->update(['warehouse' => $wh_np]);
		UpdateOrderHistory([
			'order_number' => $order_summary->etailer_order_number,
			'detail' => 'Order #: '.$order_summary->etailer_order_number .' Updated with Status '.OrderSummeryStatusName($new_status).' And Warehouse Updated in all Order Items with '.$wh_np,
			'title' => 'Order Status and warehouse updated',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($order_summary)
		]);

		if ($old_status == 10 && !in_array($new_status, [10, 11, 12, 13])) {
			$OrderDetail = new OrderDetail();
			$result = $OrderDetail->UpdateOrderStatus($OD->id,1,$user_id);
			if($result == "Error"){
				return response()->json([
					'error' => true,
					'msg' => 'Something went wrong'
				]);	
			}

			OrderDetail::where('order_number', $order_summary->etailer_order_number)->update(['status' => 1]);
			DB::table('order_history')->insert([
				'mp_order_number' => $order_summary->channel_order_number,
				'etailer_order_number' => $order_summary->etailer_order_number,
				'date' => date("Y-m-d H:i:s", strtotime('now')),
				'action' => 'Order Hold Released',
				'details' => 'Due to Client/Channel Hold Released, Order status is Released',
				'user' => 'Auto Process',
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);
		}

		if (array_key_exists('wh_assigned', $values) && isset($values['wh_assigned'])) {
			OrderDetail::where('order_number', $order_summary->etailer_order_number)->update(['warehouse' => $values['wh_assigned']]);
			$this->setTransitDayAndAssignCarriers($order_summary->etailer_order_number, null, '', $is_sat);
			checkHotRoute($order_summary);
		}

		if (in_array($new_status, ['1', '19','2','3','23']) && $old_status != $new_status) {
			$OrderDetail = new OrderDetail();
			$result = $OrderDetail->UpdateOrderStatus($OD->id,1,$user_id);
			if($result == "Error"){
				return response()->json([
					'error' => true,
					'msg' => 'Something went wrong'
				]);	
			}
			if (in_array($new_status, ['1', '19']) && $old_status != $new_status) {
				OrderDetail::where('order_number', $order_summary->etailer_order_number)
					->update([
					'warehouse' => NULL,
					'sub_order_number' => NULL,				
					'status' => '1',				
					'fulfilled_by' => NULL,				
					'picker_id' => NULL,				
					'carrier_id' => NULL,				
					'carrier_account_id' => NULL,				
					'service_type_id' => NULL,				
					'transit_days' => NULL,				
				]);
			}
			
			OrderPackage::where('order_id', 'LIKE', "%".$order_summary->etailer_order_number."%")->delete();
			
			$order_summary->old_status = $old_status;
			$order_summary->order_status = $new_status;
			$order_summary->save();
		}

		if ($request->sat_elli == 1 && in_array($day, ['thursday', 'friday'])) {
			$ods = OrderDetail::where('order_number', $order_summary->etailer_order_number)->get();
			if (isset($ods) && count($ods) > 0) {
				foreach($ods as $od) {
					if ($od->carrier_id == 1) {
						OrderDetail::where('order_number', $order_summary->etailer_order_number)
							->update(['service_type_id' => $day == 'thursday' ? '5' : '2']);
					} else if ($od->carrier_id == 2) {
						OrderDetail::where('order_number', $order_summary->etailer_order_number)
							->update(['service_type_id' => $day == 'thursday' ? '22' : '20']);
					}
				}
			}
		}

		return response()->json([
			'error' => false,
			'msg' => 'Status Updated'
		]);
		}catch (\Exception $e) {
			return response()->json([
				'error' => true,
				'msg' => $e->getMessage()
			]);
		}
	}

	public function changeShippingAndCustomerDetails(Request $request) {

		$values = $request->all();

		$type = $values['type'];
		$sum_id = $values['sum_id'];

		$sum = OrderSummary::where('id', $sum_id)->first();
		if (!isset($sum)) {
			return response()->json([
				'error' => true,
				'msg' => 'Invalid Summary Id'
			]);
		}

		if ($type === '1') {
			$sum->customer_name = $values['customer_name'];
			$sum->customer_email = $values['customer_email'];
			$sum->customer_number = $values['customer_number'];
		} else {
			$sum->ship_to_name = $values['ship_to_name'];
			$sum->ship_to_address_type = $values['ship_to_address_type'];
			$sum->ship_to_address1 = $values['ship_to_address1'];
			$sum->ship_to_address2 = $values['ship_to_address2'];
			$sum->ship_to_address3 = $values['ship_to_address3'];
			$sum->ship_to_city = $values['ship_to_city'];
			$sum->ship_to_state = $values['ship_to_state'];
			$sum->ship_to_zip = $values['ship_to_zip'];
			$sum->ship_to_phone = $values['ship_to_phone'];
			$sum->shipping_method = $values['shipping_method'];
			$sum->delivery_notes = $values['delivery_notes'];
			$sum->customer_shipping_price = $values['customer_shipping_price'];					
			$sum->ship_to_country = $values['ship_to_country'];

			if (isset($values['shipment_type']) && isset($values['carrier_type'])) {

				$car = Carrier::where('company_name', $values['carrier_type'])->first();

				$sum->shipment_type = $values['shipment_type'];
				$sum->carrier_id = $car['id'];			

				OrderDetail::where('order_number', $sum->etailer_order_number)
					->update([
						'service_type_id' => $values['shipment_type'], 
						'carrier_id' => $car['id']
					]);

				if(in_array($values['shipment_type'], [2,3,4,20,21])){
					OrderDetail::where('order_number', $sum->etailer_order_number)
					->update(['transit_days' => 1]);
				}

				if(in_array($values['shipment_type'], [5,11,22,23])){
					OrderDetail::where('order_number', $sum->etailer_order_number)
					->update(['transit_days' => 2]);
				}
			}			
		}

		$sum->save();
		UpdateOrderHistory([
			'order_number' => $sum->etailer_order_number,
			'detail' => 'Order #: '.$sum->etailer_order_number .' Updated with Shipping and Customer Details Also Carrier and Service Updated to All Order Items',
			'title' => 'Order Shipping and Customer Detail Updated',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($sum)
		]);
		return response()->json([
			'error' => false,
			'msg' => 'Details Updated'
		]);
	}

	public function updateSubOrderShipDetails(Request $request) {

		$values = $request->all();
		$sub_order_id = $values['sub_order_id'];

		$sosd = SubOrderShippingDetails::where('sub_order_number', $sub_order_id)->first();

		if (isset($sosd)) {
			$sosd->ship_to_name = $values['ship_to_name'];
			$sosd->ship_to_address_type = $values['ship_to_address_type'];
			$sosd->ship_to_address1 = $values['ship_to_address1'];
			$sosd->ship_to_address2 = $values['ship_to_address2'];
			$sosd->ship_to_address3 = $values['ship_to_address3'];
			$sosd->ship_to_city = $values['ship_to_city'];
			$sosd->ship_to_state = $values['ship_to_state'];
			$sosd->ship_to_zip = $values['ship_to_zip'];
			$sosd->ship_to_phone = $values['ship_to_phone'];
			$sosd->shipping_method = $values['shipping_method'];
			$sosd->delivery_notes = $values['delivery_notes'];
			$sosd->customer_shipping_price = $values['customer_shipping_price'];
			$sosd->save();
		} else {
			SubOrderShippingDetails::create([
				'sub_order_number' => $sub_order_id,
				'ship_to_name' => $values['ship_to_name'],
				'ship_to_address_type' => $values['ship_to_address_type'],
				'ship_to_address1' => $values['ship_to_address1'],
				'ship_to_address2' => $values['ship_to_address2'],
				'ship_to_address3' => $values['ship_to_address3'],
				'ship_to_city' => $values['ship_to_city'],
				'ship_to_state' => $values['ship_to_state'],
				'ship_to_zip' => $values['ship_to_zip'],
				'ship_to_phone' => $values['ship_to_phone'],
				'shipping_method' => $values['shipping_method'],
				'delivery_notes' => $values['delivery_notes'],
				'customer_shipping_price' => $values['customer_shipping_price']
			]);
		}

		if (isset($values['shipment_type']) && isset($values['carrier_type'])) {

			$car = Carrier::where('company_name', $values['carrier_type'])->first();
			$or = DB::table('order_details')->where('sub_order_number', $sub_order_id)->first();
			DB::table('order_details')->where('sub_order_number', $sub_order_id)
								->update([
									'service_type_id' => $values['shipment_type'],
									'carrier_id' => $car->id
								]);
			
			//update transit days to 1 for next day carrier type
			if(in_array($values['shipment_type'], [2,3,4,20,21])){
				DB::table('order_details')->where('sub_order_number', $sub_order_id)
								->update([
									'transit_days' => 1,
								]);
			}

			//update transit days to 2 for all the 2days shipment type
			if(in_array($values['shipment_type'], [5,11,22,23])){
				DB::table('order_details')->where('sub_order_number', $sub_order_id)
								->update([
									'transit_days' => 2,
								]);
			}

			UpdateOrderHistory([
				'sub_order_number' => $sub_order_id,
				'order_number' => $or->order_number,
				'detail' => 'Sub Order #: '.$sub_order_id .' Carrier and Service Updated',
				'title' => 'Sub Order Carrier and Service Updated',
				'user_id' => auth()->user()->id,
				'reference' => 'PM',
				'extras' => json_encode($or)
			]);
		}

		return response()->json([
			'error' => false,
			'msg' => 'Details Updated'
		]);
	}

	public function splitOrders(Request $request) {

		$values = $request->all();

		$ids = $values['ids'];
		$order_number = $values['order_number'];

		if (!isset($ids)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Ids to split'
			]);
		}

		if (!isset($order_number)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Order Number'
			]);
		}

		$id_arr = explode(',', $ids);

		$max_sub_order = OrderDetail::where('order_number', $order_number)->max('sub_order_number');
		$after_decimal_str = substr($max_sub_order, stripos($max_sub_order, '.') + 1);
		$after_decimal = intval($after_decimal_str);

		if ($after_decimal >= 7) {
			$after_decimal = $after_decimal + 1;
		} else {
			$after_decimal = 7;
		}
		
		$after_decimal = strval($after_decimal);
		if (strlen($after_decimal) == 1) {
			$after_decimal = '00' . $after_decimal;
		} if (strlen($after_decimal) == 2) {
			$after_decimal = '0' . $after_decimal;
		}

		$new_sub_order = $order_number . '.' . $after_decimal;
		OrderDetail::whereIn('id', $id_arr)->update(['sub_order_number' => $new_sub_order]);
		UpdateOrderHistory([
			'order_number' => $order_number,
			'detail' => 'Order #: '.$order_number .' has been Splited to Sub order # '.$new_sub_order,
			'title' => 'Sub Order Split',
			'user_id' => auth()->user()->id,
			'reference' => 'PM'
		]);

		return response()->json([
			'error' => false,
			'msg' => 'Orders splitted'
		]);
	}

	public function mergeOrders(Request $request) {

		$values = $request->all();

		$ids = $values['ids'];
		$order_number = $values['order_number'];

		if (!isset($ids)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Ids to split'
			]);
		}

		if (!isset($order_number)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Order Number'
			]);
		}

		$id_arr = explode(',', $ids);

		$max_sub_order = OrderDetail::where('order_number', $order_number)->max('sub_order_number');
		$after_decimal_str = substr($max_sub_order, stripos($max_sub_order, '.') + 1);
		$after_decimal = intval($after_decimal_str);

		if ($after_decimal >= 7) {
			$after_decimal = $after_decimal + 1;
		} else {
			$after_decimal = 7;
		}
		
		$after_decimal = strval($after_decimal);
		if (strlen($after_decimal) == 1) {
			$after_decimal = '00' . $after_decimal;
		} if (strlen($after_decimal) == 2) {
			$after_decimal = '0' . $after_decimal;
		}

		$new_sub_order = $order_number . '.' . $after_decimal;
		OrderDetail::whereIn('sub_order_number', $id_arr)
				->update(['sub_order_number' => $new_sub_order]);

		UpdateOrderHistory([
			'order_number' => $order_number,
			'detail' => 'Sub Order #: '.$ids .' has been Merged into # '.$new_sub_order,
			'title' => 'Sub Order Merged',
			'user_id' => auth()->user()->id,
			'reference' => 'PM'
		]);
		return response()->json([
			'error' => false,
			'msg' => 'Orders Merged'
		]);
	}

	public function sub_order(Request $request, $order_number){
		$data = DB::select('select 
						distinct od.sub_order_number
						from order_details od						
						where od.order_number = ?', [$order_number]);

		$result = [];
		foreach($data as $res) {
			array_push($result, [
				'sub_order_number' => $res->sub_order_number
			]);
		}

		return view('orders.merge_sub_order',compact('result', 'order_number'));
	}

	public function reShipOrder(Request $request) {

		$values = $request->all();

		$order_number = $values['order_number'];
		if (!isset($order_number)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Order Number to Re-Ship'
			]);
		}

		$order_summary = OrderSummary::where('etailer_order_number', $order_number);

		$sub_order_number = $values['sub_order_number'];
		if (!isset($sub_order_number)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Sub Order Number to Re-Ship'
			]);
		}

		$ids = $values['ids'];
		if (!isset($ids)) {
			return response()->json([
				'error' => true,
				'msg' => 'No Ids to split'
			]);
		}

		$carrier = $values['carrier_id'];
		$shipment_type = $values['shipment_type'];
		if (!(isset($carrier) || isset($shipment_type))) {
			return response()->json([
				'error' => true,
				'msg' => 'Shipment/Carrier must be specified'
			]);
		}
		$car = Carrier::where('company_name', $carrier)->first();

		$toAdd = [];
		foreach (explode(',', $ids) as $id) {

			$idQuant = explode('#', $id);
			if (is_numeric($idQuant[0])) {
				$od = OrderDetail::where('id', $idQuant[0])->first();
				$od->quantity_ordered = $idQuant[1];
				$od->carrier_id = $car->id;
				$od->service_type_id = $shipment_type;
				array_push($toAdd, $od);
			} else {
				$mp = MasterProduct::where('ETIN', $idQuant[0])->first();
				$od = new OrderDetail();
				$od->order_number = $order_number;
				$od->ETIN = $mp->ETIN;
				$od->ETIN_flag = 0;
				$od->quantity_ordered = $idQuant[1];
				$od->channel_unit_price = $mp->cost;
				$od->carrier_id = $car->id;
				$od->service_type_id = $shipment_type;
				array_push($toAdd, $od);
			}
		}

		$fault_id = $values['fault_id'];
		$reship_reason_id = $values['reship_reason_id'];		
		if (!(isset($fault_id) || isset($reship_reason_id))) {
			return response()->json([
				'error' => true,
				'msg' => 'Fault/Reship Reason must be specified'
			]);
		}

		$fault = FaultCodeKey::where('id', $fault_id)->first();
		if (!isset($fault)) {
			return response()->json([
				'error' => true,
				'msg' => 'Inavlid Fault Code Seleted'
			]);
		}

		$reship_reason = ReshipReasonCode::where('id', $reship_reason_id)->first();
		if (!isset($reship_reason)) {
			return response()->json([
				'error' => true,
				'msg' => 'Inavlid Reship Reason Seleted'
			]);
		}

		$new_sub_order_number = $sub_order_number . '_' . $fault->code . '_' . $reship_reason->code;
		foreach($toAdd as $toAdd) {

			DB::table('order_details')->insert([
				"order_number" => $toAdd->order_number,
				"ETIN" => $toAdd->ETIN,
				"SA_line_number" => $toAdd->SA_line_number,
				"SA_sku" => $toAdd->SA_sku,
				"channel_product_name" => $toAdd->channel_product_name,
				"etailer_product_name" => $toAdd->etailer_product_name,
				"channel_unit_price" => $toAdd->channel_unit_price,
				"channel_extended_price" => $toAdd->channel_extended_price,
				"etailer_channel_price" => $toAdd->etailer_channel_price,
				"discount_name" => $toAdd->discount_name,
				"customer_discount" => $toAdd->customer_discount,
				"customer_paid_price" => $toAdd->customer_paid_price,
				"quantity_ordered" => $toAdd->quantity_ordered,
				"quantity_fulfilled" => $toAdd->quantity_fulfilled,
				"ETIN_flag" => $toAdd->ETIN_flag,
				"created_at" => $toAdd->created_at,
				"updated_at" => $toAdd->updated_at,
				"sub_order_number" => $new_sub_order_number,
				"warehouse" => $toAdd->warehouse,
				"fulfilled_by" => $toAdd->fulfilled_by,
				"picker_id" => NULL,
				"status" => 9,
				"carrier_id" => $toAdd->carrier_id,
				"carrier_account_id" => $toAdd->carrier_account_id,
				"service_type_id" => $toAdd->service_type_id,
				"transit_days" => $toAdd->transit_days,
			]);

			if ($toAdd->ETIN_flag == 0) {
				$this->OrderDetail->checkFulfilledBy($order_summary, $toAdd);
			}
		}

		OrderReshipFault::create([
			'sub_order_number' => $new_sub_order_number,
			'fault_code_id' => $fault_id,
			'reship_reason_id' => $reship_reason_id
		]);

		OrderSummary::where('etailer_order_number', $order_number)
			->update(['order_status' => 18, 'must_ship_today' => 1]);

		UpdateOrderHistory([
			'order_number' => $order_number,
			'detail' => 'Order #: '.$order_number .' has been Partially Shipped',
			'title' => 'Order Status Changed',
			'user_id' => auth()->user()->id,
			'reference' => 'PM'
		]);

		return response()->json([
			'error' => false,
			'msg' => 'Re-Ship Created'
		]);
	}

	public function viewReshipOptionsPage(Request $request) {

		$fault_codes = FaultCodeKey::all();
		$reship_codes = ReshipReasonCode::all();
		$ups_st = ShippingServiceType::where('service_type', 'ups')->get();
		$fedex_st = ShippingServiceType::where('service_type', 'fedex')->get();
		$carr = Carrier::all();

		$ids = $request->all()['ids'];
		$key = $request->all()['sub_order'];
		$on = $request->all()['order_number'];
		$prev_carr_id = $request->all()['prev_carrier_id'];
		$prev_carr_name = $request->all()['prev_carrier_name'];
		$prev_ship_type = $request->all()['prev_ship_type'];

		return view('orders.view_reship_option',
			compact('fault_codes', 'reship_codes', 'key', 'ids', 'on', 'ups_st', 'fedex_st', 'prev_carr_id', 'prev_ship_type', 'carr', 'prev_carr_name'));
	}

	public function bulk_order_page($client = NULL) {
		$clients = Client::all();
		$ots = OrderTypes::whereIn('id', [2, 3])->get();
		return view('orders.upload_bulk_order', compact('clients', 'client', 'ots'));
	}

	public function process_bulk_upload(Request $request) {

		if(!$request->hasFile('csv_file')){			
			return response()->json(['msg' => 'No files uploaded', 'error' => 1]);
		}

		if(!($request->get('client') || $request->get('client_name'))){			
			return response()->json(['msg' => 'No client found in if', 'error' => 1]);
		}

		if(!$request->get('order_type')){			
			return response()->json(['msg' => 'No pickup type found', 'error' => 1]);
		}

		$fol = 'orders' . DIRECTORY_SEPARATOR;
		$fileName = time() . '.' . $request->file('csv_file')->getClientOriginalExtension();
		$request->file('csv_file')->move(storage_path($fol), $fileName);

		$filePath = storage_path($fol . DIRECTORY_SEPARATOR . $fileName);

		$client = $request->get('client') ? $request->get('client') : $request->get('client_name');

		$resp = $this->processBulkOrderFile($filePath, $client, $request->get('order_type'));		
		unlink($filePath);

		return $resp;
	}

	private function processBulkOrderFile($filePath, $client, $order_type) {

		$mandatory_values = array('manual_order_number', 'shipping_full_name',
			'shipping_address1', 'shipping_city', 'shipping_state', 'shipping_postal_code','shipping_phone');

		$data = Excel::toArray([], $filePath);
		$maincount = $data[0];

		$count = count($maincount);
        $headers = $data[0][0];
		$total_price = 0;
		$client_id = NULL;

		$total_processed = 0;
		$total_pre_present = 0;

		$errors = array();
		$orders_processed = array();

		for ($j = 1; $j < $count; $j++){
			$values = $data[0][$j];
			$customer_email = '';
			$customer_phone = '';
			$sku = '';
			$customer_paid_price = 0;
			$channel_extended_price = 0;
			$is_client_on_hold = false;
			
			// Insert into sa_incoming_order_template table
			$rowid = DB::table('sa_incoming_order_template')->insertGetId(['created_at' => date("Y-m-d H:i:s", strtotime('now')), 'updated_at' => date("Y-m-d H:i:s", strtotime('now'))]);
			
			$row_error = "Row " . $j . " has the following errors: ";
			$err_col_names = array();
			$manual_order_number = '';
			for ($i = 0; $i < count($headers); $i++) {
				$rowName = str_replace('**', '', $headers[$i]);
				$rowVal = $values[$i];

				if(in_array($rowName, $mandatory_values) && !isset($rowVal)) {
					array_push($err_col_names, $rowName);					
				}

				if ($rowName === 'manual_order_number') {
					$manual_order_number = $rowVal;
				}
			}
			if (count($err_col_names) > 0) {
				$row_error = $row_error . json_encode($err_col_names) . ' are required.';
				array_push($errors, $row_error);				
				continue;
			}

			for ($i = 0; $i < count($headers); $i++) {
				$rowName = str_replace('**', '', $headers[$i]);
				$rowVal = $values[$i];

				if($rowName == 'shipping_country_code'){
					if(!$rowVal){
						$rowVal = 'US';
					}
				}
				if($rowVal != ''){
						DB::table('sa_incoming_order_template')->where('id', $rowid)->update([strtolower($rowName) => $rowVal]);
				}
				if($rowName == 'customer_email'){
					$customer_email = $rowVal;
				} else if($rowName == 'customer_phone'){
					$customer_phone = $rowVal;
				} else if($rowName == 'sku'){
					$sku = $rowVal;
				} else if($rowName == 'sa_line_number'){
					$sa_line_number = $rowVal;
				}								
			}

			// Product Validation
			$clientDetails = Client::find($client);
			if(!$clientDetails){
				return response()->json(					
					[
						'msg' => 'Client not found!!',
						'error' => 1
					]					
				);
			}
			
			if (isset($sku)) {
				$masterProduct = MasterProduct::where('ETIN', $sku)->first();
				if (!isset($masterProduct)) {
					$row_error = $sku . " is not found.";
					array_push($errors, $row_error);
					continue;
				} else if(strpos($masterProduct->lobs, ',') !== false ) {				
					$lobs = explode(',', $masterProduct->lobs);
					if(!in_array($clientDetails->id, $lobs)){
						$row_error = $sku." dont belongs to this Client";
						array_push($errors, $row_error);
						continue;
					}
				} else {
					$lobs = $masterProduct->lobs;
					if($masterProduct->lobs != $clientDetails['id']){
						$row_error = $sku." dont belongs to this Client";
						array_push($errors, $row_error);
						continue;
					}
				}
			}

			$is_client_on_hold = $clientDetails->is_enable == 2 ? true : false;

			$sa_incoming_order_table = DB::table('sa_incoming_order_template')->where('id', $rowid)->get();
			
			if(isset($sa_incoming_order_table[0]->purchase_date)){
				$sa_incoming_order_table[0]->purchase_date = $this->changeTimeFormat($sa_incoming_order_table[0]->purchase_date);
			}
			if(isset($sa_incoming_order_table[0]->estimated_ship_date)){
				$sa_incoming_order_table[0]->estimated_ship_date = $this->changeTimeFormat($sa_incoming_order_table[0]->estimated_ship_date);
			}
			if(isset($sa_incoming_order_table[0]->estimated_delivery_date)){
				$sa_incoming_order_table[0]->estimated_delivery_date = $this->changeTimeFormat($sa_incoming_order_table[0]->estimated_delivery_date);
			}

			// Add User Info into ship_to_customer table
			$this->updateShipToUserTable($rowid, $customer_email, $customer_phone, $sa_incoming_order_table);

			// Find last order number
			$last_order = OrderSummary::orderBy('id', 'desc')->first();
			$last_order_number = !$last_order ? 9999 : $last_order->etailer_order_number;

			# Log::channel('ImportSaInventoryTemplate')->info('Last order number: '.$last_order_number);

			// Insert into order_summary table
			$customer_number = $sa_incoming_order_table[0]->customer_phone;
			
			// $find_client = ClientChannelConfiguration::where('channel', $sa_incoming_order_table[0]->marketplace_channel)->first();
			// if($find_client){
			// 	$client = Client::find($find_client->client_id);
			// 	if($client){
			// 		$client_id = $client->id;
			// 	}
			// } else {
			// 	$client_id = $client;
			// }
			

			$ifOrderSummaryExsists = OrderSummary::where('channel_order_number', $sa_incoming_order_table[0]->manual_order_number)->get();
			$carr = Carrier::where('company_name', $sa_incoming_order_table[0]->carrier_type)->first();
			$st = ShippingServiceType::where('service_name', $sa_incoming_order_table[0]->shipment_type)
				->where('service_type', strtolower($sa_incoming_order_table[0]->carrier_type))->first();

			if ($ifOrderSummaryExsists->isEmpty() ){
				$validation_errors = $this->validateFieldValues($sa_incoming_order_table[0]);
				if (count($validation_errors) > 0) {
					array_push($errors, 'Row: ' . $j . ' has the errors: ' . json_encode($validation_errors));
					continue;
				}				

				# Log::channel('ImportSaInventoryTemplate')->info('Order Insert Number: ' . ($last_order_number + 1));
				array_push($orders_processed, $manual_order_number);
				DB::table('order_summary')->insert([				
					'etailer_order_number' => $last_order_number + 1,
					'channel_order_number' => $sa_incoming_order_table[0]->manual_order_number,
					'order_source' => 'Manual',
					'channel_type' => $sa_incoming_order_table[0]->marketplace_channel,
					'purchase_date' => isset($sa_incoming_order_table[0]->purchase_date) 
										? $sa_incoming_order_table[0]->purchase_date : $this->changeTimeFormat(date("Y-m-d")),
					'customer_number' => $customer_number,
					'customer_name' => $sa_incoming_order_table[0]->customer_full_name,
					'customer_email' => $sa_incoming_order_table[0]->customer_email,
					'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'ship_to_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'ship_to_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'ship_to_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'ship_to_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'ship_to_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'ship_to_city' => $sa_incoming_order_table[0]->shipping_city,
					'ship_to_state' => $sa_incoming_order_table[0]->shipping_state,
					'ship_to_zip' => $sa_incoming_order_table[0]->shipping_postal_code,
					'ship_to_country' => $sa_incoming_order_table[0]->shipping_country_code,
					'ship_to_phone' => $sa_incoming_order_table[0]->shipping_phone,
					'shipping_method' => $sa_incoming_order_table[0]->shipping_method,
					'delivery_notes' => $sa_incoming_order_table[0]->delivery_notes,
					'customer_shipping_price' => $sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount),
					'gift_message' => $sa_incoming_order_table[0]->gift_message,
					'sales_tax' => $sa_incoming_order_table[0]->sales_tax,
					'shipping_tax' => $sa_incoming_order_table[0]->shipping_tax,
					'shipping_discount_type' => $sa_incoming_order_table[0]->shipping_discount_name,
					'shipping_discount_amount' => $sa_incoming_order_table[0]->shipping_discount,
					'channel_estimated_ship_date' => $sa_incoming_order_table[0]->estimated_ship_date,
					'channel_estimated_delivery_date' => $sa_incoming_order_table[0]->estimated_delivery_date,
					'is_amazon_prime' => $sa_incoming_order_table[0]->is_amazon_prime,
					'paypal_transaction_ids' => $sa_incoming_order_table[0]->paypal_transaction_ids,
					'customer_vat' => $sa_incoming_order_table[0]->customer_vat,
					'currency' => $sa_incoming_order_table[0]->currency,
					'order_status' => $is_client_on_hold ? '10' : '19', // New Manual
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
					'client_id' => $clientDetails->id,
					'order_type_id' => isset($order_type) ? $order_type : 1,
					'carrier_id' => isset($carr) ? $carr->id : NULL,
					'shipment_type' => isset($st) ? $st->id : NULL
				]);

				$etailer_order_number = $last_order_number + 1;
				UpdateOrderHistory([
					'order_number' => $etailer_order_number,
					'detail' => 'Order #: '.$etailer_order_number .' has been Created with New Manual Order from bulk upload',
					'title' => 'Order Created',
					'user_id' => auth()->user()->id,
					'reference' => 'PM'
				]);

				/* Notify other admins */
				$note = $etailer_order_number;
				$note .= ' New Etailer Order Placed';
				$url_id = '';

				$order = OrderSummary::where('etailer_order_number', $etailer_order_number)->first();
				if($order){
					$url_id = $order->id;
					$url = '/summery_orders/'.$url_id.'/view';
					$type = "New Order";
					$this->NotificationRepository->SendOrderNotification([
						'subject' => $type,
						'body' => $note,
						'order_number' => $order->etailer_order_number
					]);
				}
								
			} else {
						
				$exsisting_order = OrderSummary::where('channel_order_number', $sa_incoming_order_table[0]->manual_order_number)->first();

				if ( ! isset($exsisting_order->etailer_order_number)){
					$previous_price = 0;
				} else {
					$previous_price = $exsisting_order->customer_shipping_price;
				}
				$customer_shipping_price = $previous_price + ($sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount));
				$channel_extended_price = $sa_incoming_order_table[0]->quantity * $sa_incoming_order_table[0]->unit_price;
				$customer_paid_price =  $sa_incoming_order_table[0]->quantity * ($sa_incoming_order_table[0]->unit_price - $sa_incoming_order_table[0]->discount);

				OrderSummary::where('channel_order_number', $sa_incoming_order_table[0]->manual_order_number)->update([
					'customer_shipping_price' => $customer_shipping_price,
					'order_total_price' => $customer_paid_price,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
					'client_id' => $clientDetails->id,
				]);	
				
			}
			
			if (!isset($sku)) {
				continue;
			}

			// Insert into order_detail table
			$ifmptexsists = DB::table('master_product')->where('ETIN', $sku)->orWhere('product_listing_ETIN',$sku)->orWhere('alternate_ETINs','like','%'.$sku.'%')->get();
			
			if($ifmptexsists->isEmpty()){
				# Log::channel('ImportSaInventoryTemplate')->info('Product ETIN : '.$sku.' is not exisits in MPT.');
				//continue;
				$etin = $sa_incoming_order_table[0]->sku;
				$etailer_product_name = $sa_incoming_order_table[0]->product_name;
				$etailer_channel_price = $sa_incoming_order_table[0]->unit_price * $sa_incoming_order_table[0]->quantity;
			} else {
				$etin = $ifmptexsists[0]->ETIN;
				$etailer_product_name = $ifmptexsists[0]->product_listing_name ;
				$etailer_channel_price = $ifmptexsists[0]->cost * $sa_incoming_order_table[0]->quantity;
			}

			$checkEtin = DB::table('master_product')->where('ETIN', $sku)->get();
			$checkProductListEtin = DB::table('master_product')->where('product_listing_ETIN',$sku)->get();
			$checkAlternateEtin = DB::table('master_product')->where('alternate_ETINs','like','%'.$sku.'%')->get();
			if(!$checkEtin->isEmpty()){
				$ETIN_flag = 0;
			} else if(!$checkProductListEtin->isEmpty()){
				$ETIN_flag = 1;
			} else if(!$checkAlternateEtin->isEmpty()){
				$ETIN_flag = 2;
			} else {
				$ETIN_flag = 3;
			}

			$total_price += $customer_paid_price;
			$last_OrderSummary = OrderSummary::latest('id')->first();
			
			if($last_OrderSummary != null){
				
				if($last_OrderSummary->customer_email == $sa_incoming_order_table[0]->customer_email){
					$orderid = $last_OrderSummary->etailer_order_number;
				} else {
					$orderid = $last_order_number + 1;
				}
			} else {
				$orderid = $last_order_number + 1;
			}

			$orderDetailsCount = OrderDetail::where('order_number', $orderid)->where('ETIN', $etin)->count();

			if($orderDetailsCount == 0){
				DB::table('order_details')->insert([
					'order_number' => $orderid,
					'ETIN' => $etin,
					'SA_line_number' => $sa_incoming_order_table[0]->mp_line_number,
					'SA_sku' => $sa_incoming_order_table[0]->sku,
					'channel_product_name' => $sa_incoming_order_table[0]->product_name,
					'etailer_product_name' => $etailer_product_name,
					'channel_unit_price' => $sa_incoming_order_table[0]->unit_price,
					'channel_extended_price' => $channel_extended_price,
					'etailer_channel_price' => $etailer_channel_price,
					'discount_name' => $sa_incoming_order_table[0]->discount_name,
					'customer_discount' => $sa_incoming_order_table[0]->discount,
					'customer_paid_price' => $customer_paid_price,
					'quantity_ordered' => $sa_incoming_order_table[0]->quantity,
					'ETIN_flag' => $ETIN_flag,
					'status' => $is_client_on_hold ? '18' : '7',
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
					'carrier_id' => isset($carr) ? $carr->id : NULL,
					'service_type_id' => isset($st) ? $st->id : NULL,
				]);
					
				// Insert into sa_order_conformation_template table
				
				DB::table('sa_order_conformation_template')->insert([
					'order_id' => $orderid,
					'sku' => $sa_incoming_order_table[0]->sku,
					'shippedCost' => $customer_paid_price,
					'created_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);				
			}
		}

		return response()->json(
			count($errors) > 0
			? 
			[
				'msg' => json_encode($errors),
				'error' => 1,
				'type' => 'validation'
			]
			:
			[
				'msg' => 'File uploaded Successfully. Total Order Processed: ' . count($orders_processed),
				'error' => 0
			]
		);
	}

	private function validateFieldValues($record) {
		$errors = array();

		if (!isset($record->manual_order_number)) {
			array_push($errors, 'Manual Order number is required.');
		}

		if (isset($record->customer_phone) && !(is_numeric($record->customer_phone) && strlen($record->customer_phone) == 10)) {
			array_push($errors, 'Phone number must contains 10 digits.');
		}

		if(isset($record->carrier_type)){
			$carr = Carrier::where('company_name', $record->carrier_type)->first();
			if (!isset($carr)) {
				array_push($errors, 'Invalid Carrier.');
			} else {
				$st = ShippingServiceType::where('service_name', $record->shipment_type)
					->where('service_type', strtolower($record->carrier_type))->first();
				if (!isset($st)) {
					array_push($errors, 'Invalid Shipemnt Type for Carrier: ' . $record->carrier_type);
				}
			}
		}		

		return $errors;
	}

	private function updateShipToUserTable($rowid, $customer_email, $customer_phone, $sa_incoming_order_table){
		
		$ship_to_customer_exsists = DB::table('ship_to_customer')->where('customer_email', $customer_email)->orWhere('customer_phone', $customer_phone)->get();
		
		$last_customer_number = DB::table('ship_to_customer')->max('customer_number');
			
			if(!$last_customer_number){
				$last_customer_number = 9999;
			}
			

		if(!$ship_to_customer_exsists->isEmpty()){
			
			$ifemailexsists = DB::table('ship_to_customer')->where('customer_email', $customer_email)->get();
			$ifphoneexsists = DB::table('ship_to_customer')->where('customer_phone', $customer_phone)->get();
			if(!$ifemailexsists->isEmpty()){
				DB::table('ship_to_customer')->where('customer_email', $sa_incoming_order_table[0]->customer_email)->update([
					//'customer_number' => $last_customer_number + 1,
					'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
					//'customer_email' => $sa_incoming_order_table[0]->customer_email,
					'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
					'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
					'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
					'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
					'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);
			
				# Log::channel('ImportSaInventoryTemplate')->info('Email id: '.$customer_email.' already exsists in ship_to_customer table');
				$error_log[] = 'Email id: '.$customer_email.' already exsists in ship_to_customer table';
			} elseif(!$ifphoneexsists->isEmpty()){
				DB::table('ship_to_customer')->where('customer_phone', $sa_incoming_order_table[0]->customer_phone)->update([
					//'customer_number' => $last_customer_number + 1,
					'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
					'customer_email' => $sa_incoming_order_table[0]->customer_email,
					//'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
					'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
					'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
					'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
					'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
					'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
					'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
					'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
					'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
					'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
					'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
					'updated_at' => date("Y-m-d H:i:s", strtotime('now')),
				]);
				# Log::channel('ImportSaInventoryTemplate')->info('Phone Number: '.$customer_phone.' already exsists in ship_to_customer table');
				$error_log[] = 'Phone Number: '.$customer_phone.' already exsists in ship_to_customer table';
			}
		} else {
			DB::table('ship_to_customer')->insert([
				
				'customer_number' => $last_customer_number + 1,
				'customer_full_name' => $sa_incoming_order_table[0]->customer_full_name,
				'customer_email' => $sa_incoming_order_table[0]->customer_email,
				'customer_phone' => $sa_incoming_order_table[0]->customer_phone,
				'shipping_full_name' => $sa_incoming_order_table[0]->shipping_full_name,
				'shipping_address_type' => $sa_incoming_order_table[0]->shipping_address_type,
				'shipping_address1' => $sa_incoming_order_table[0]->shipping_address1,
				'shipping_address2' => $sa_incoming_order_table[0]->shipping_address2,
				'shipping_address3' => $sa_incoming_order_table[0]->shipping_address3,
				'shipping_city' => $sa_incoming_order_table[0]->shipping_city,
				'shipping_state' => $sa_incoming_order_table[0]->shipping_state,
				'shipping_postal_code' => $sa_incoming_order_table[0]->shipping_postal_code,
				'shipping_country_code' => $sa_incoming_order_table[0]->shipping_country_code,
				'shipping_phone' => $sa_incoming_order_table[0]->shipping_phone,
				'created_at' => date("Y-m-d H:i:s", strtotime('now')),
			]);
			
		}		
	}

	private function changeTimeFormat($time){
		$changedTime = date("Y-m-d H:i:s", strtotime($time));
		return $changedTime;		
	}

	public function shipManualOrders(Request $request) {
		
		$values = $request->all();

		if (!isset($values['order'])) {
			return response()->json(['msg' => 'Order is Invalid/Empty', 'error' => 1]);
		}

		if (!isset($values['sub_order'])) {
			return response()->json(['msg' => 'Sub Order is Invalid/Empty', 'error' => 1]);
		}

		OrderDetail::where('sub_order_number', $values['sub_order'])->update(['status' => 6]);
		$status = OrderDetail::where('order_number', $values['order'])->get(['status'])->unique('status')->pluck('status')->toArray();

		if (count($status) == 1 && in_array('6', $status)) {
			OrderSummary::where('etailer_order_number', $values['order'])->update(['order_status' => 22]);
			UpdateOrderHistory([
				'order_number' => $values['order'],
				'detail' => 'Order #: '.$values['order'] .' has been Manual - Shipped',
				'title' => 'Order Status Changed',
				'user_id' => auth()->user()->id,
				'reference' => 'PM',
				'extras' => json_encode($values)
			]);			
		} else if (count($status) > 1) {
			OrderSummary::where('etailer_order_number', $values['order'])->update(['order_status' => 21]);
			UpdateOrderHistory([
				'order_number' => $values['order'],
				'detail' => 'Order #: '.$values['order'] .' has been Manual - Partially Shipped',
				'title' => 'Order Status Changed',
				'user_id' => auth()->user()->id,
				'reference' => 'PM',
				'extras' => json_encode($values)
			]);			
		}

		return response()->json(['msg' => 'Order Shipped', 'error' => 0]);
	}
	
	public function FilterOrderProducts($order_id = NULL,$id = NULL){
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
		$smart_filters = $this->SmartFilter::where('created_by',Auth::user()->id)->where('type','product')->get();
		if($id != NULL){
			$all_columns = $this->ProductListingFilter::where('type','product')->pluck('sorting_order','id')->toArray();
			$smart_filter = $this->SmartFilter->find($id);

			$selected_smart_filter = json_decode($smart_filter->filter_preferences,true);
			$visible_columns = explode(',',$smart_filter->visible_columns);
			$visible_filters = explode(',',$smart_filter->visible_filters);
			if(!empty($all_columns)){
				$hidden_cols_arr = array_diff($all_columns,$visible_columns);
				$hidden_cols = implode(',',$hidden_cols_arr);
			}
			$main_filter = json_decode($smart_filter->main_filter,true);
		}else{
			$not_default_columns = $this->ProductListingFilter::where('is_default','!=',1)->where('type','product')->pluck('sorting_order','id')->toArray();
			if(!empty($not_default_columns)){
				$hidden_cols = implode(',',$not_default_columns);
			}
		}

		$order_info = OrderSummary::find($order_id);
		return view('orders.order_product_filter',['product_listing_filter' => $product_listing_filter, 'smart_filters' => $smart_filters, 'selected_smart_filter' => $selected_smart_filter, 'id' => $id, 'hidden_cols' => $hidden_cols, 'visible_columns' => $visible_columns, 'visible_filters' => $visible_filters,'smart_filter' => $smart_filter,'main_filter' => $main_filter, 'hidden_cols_arr' => $hidden_cols_arr,'order_id' => $order_id, 'order_info' => $order_info ]);
	}

	public function getOptimizedMasterproductsForOrder(Request $request)
    {
		if ($request->ajax()) {
			$selected_products = $request->selected_products;
			$client_id = $request->client_id;
            $dataget = DB::table('master_product')->where('is_approve', 1)
			->leftjoin("categories",'categories.id',"=",'master_product.product_category')
			->select(['master_product.id','master_product.ETIN','master_product.product_listing_name','master_product.product_type','master_product.upc','master_product.gtin','master_product.status','categories.name as product_category','master_product.item_form_description','master_product.is_approve','full_product_desc','about_this_item','ingredients','allergens','product_tags','current_supplier']);

			$dataget->whereRaw('FIND_IN_SET(\''.$client_id.'\',master_product.lobs)');

			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
			if(isset($filter_val)){
				foreach($filter_val as $key => $row_val){
					if(isset($row_val[$key])){

						$search_value_key = $row_val[$key];
						$search_value = '';
						if(isset($row_val[$search_value_key])){
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						for($i = 1; $i < 10; $i++){
							if($column_name == 'product_subcategory'.$i){
								$select_table = 'subcat'.$i;
							}
						}

						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$dataget->where($key,$search_value);
							}

						}


						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'not like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'not like', '%'.$value.'%');
									}
								});
							}
						}
						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->Orwhere('master_product.ETIN','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.product_listing_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.product_type','LIKE','%'.$search_text.'%');

						$query->Orwhere('master_product.upc','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.gtin','LIKE','%'.$search_text.'%');
						$query->Orwhere('categories.name','LIKE','%'.$search_text.'%');

					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){
				foreach($main_filter as $key_main => $row_main_filter){
					if($row_main_filter != ''){
						$excluded_array = ['product_tags','warehouses_assigned','product_listing_ETIN','alternate_ETINs','prop_65_ingredient','ingredients','parent_ETIN','prop_65_flag'];
						if(in_array($key_main,$excluded_array)){
							$unique_value = array_unique(explode(',',$row_main_filter));
							$dataget->where(function($q) use($unique_value,$key_main){
								if($unique_value){
									foreach($unique_value as $row_un_val){
										if($key_main == 'allergens'){
											$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\',master_product.'.$key_main.') > "0"');
										}else{
											$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\','.$key_main.') > "0"');
										}
									}
								}
							});
						}else{
							$dataget->where($key_main,'LIKE','%'.$row_main_filter.'%');
						}
					}
				}
			}

			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$dataget->groupBy("master_product.id");

			$order_by = 'ETIN';
			$order = 'ASC';

			if(isset($request->order[0]['column'])){
				$order_by = $request->columns[$request->order[0]['column']]['data'];
				$order = $request->order[0]['dir'];
			}
			
			$dataget->orderBy($order_by,$order);

			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			$limit = 12;
			if(isset($input['limit']))$limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			if($request->get('length') >= 0)
			{
				$offset = $request->get('start');
				$limit = $request->get('length');
				$dataget->skip($offset)->take($limit);	
			}


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $dataget->get();
			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							// if (request()->has('name')) {
							//     $query->where('name', 'like', "%" . request('name') . "%");
							// }

							// if (request()->has('email')) {
							//     $query->where('email', 'like', "%" . request('email') . "%");
							// }
                })
					->editColumn('is_approve', function ($data) {
						return  ($data->is_approve == '0')?"No":"Yes";
					})
					->addIndexColumn()
					->addColumn('action', function($row) use($ActiveProductListingsEditProduct,$selected_products){
						$checked = '';
						if($selected_products != ''){
							$SP = explode(',',$selected_products);

							if(in_array($row->ETIN,$SP)){
								$checked = 'checked=checked';
							}
						}
						$btn = '';
						$btn = '<input type="checkbox" onClick="SelectedOrderProducts(\''.$row->ETIN.'\')" '.$checked.'>';
						return $btn;
					})
					->editColumn('ETIN', function ($data) {
  						$btn = '';
							if($data->item_form_description == 'Kit'){
								$btn = '<a href="'.route('kits.edit',$data->id).'"  target="_blank">'.$data->ETIN.'</a>';
							}else{
								$btn = '<a href="'.route('editmasterproduct',$data->id).'"  target="_blank" >'.$data->ETIN.'</a>';
							}

                            return $btn;
					})
					->editColumn('product_listing_name', function ($data) {
  						return Str::limit($data->product_listing_name,100, ('...'));
					})
					->editColumn('full_product_desc', function ($data) {
						return Str::limit($data->full_product_desc,100, ('...'));
					})
					->editColumn('about_this_item', function ($data) {
						return Str::limit($data->about_this_item,100, ('...'));

					})
					->editColumn('ingredients', function ($data) {
						return Str::limit($data->ingredients,100, ('...'));
					})
					->editColumn('allergens', function ($data) {
						$allergens = DB::table('allergens')->select(\DB::raw("GROUP_CONCAT(allergens) as allergens"))->whereIN('id',explode(',',$data->allergens))->first();
						return Str::limit($allergens->allergens,100, ('...'));
					})
					->editColumn('product_tags', function ($data) {
						$product_tags = DB::table('product_tags')->select(\DB::raw("GROUP_CONCAT(tag	) as product_tags"))->whereIN('id',explode(',',$data->product_tags))->first();
						return Str::limit($product_tags->product_tags,100, ('...'));
					})
					// ->editColumn('supplier_description', function ($data) {
					// 	return Str::limit($data->supplier_description,100, ('...'));
					// })
					->editColumn('current_supplier', function ($data) {
						return Str::limit($data->current_supplier,100, ('...'));
					})
                    ->rawColumns(['action','ETIN'])
					->setTotalRecords($total)
					->setFilteredRecords($total)
					->skipPaging()
                    ->make(true);

        }
    }

	public function masterproductsFilterForOrder(Request $request)
    {
		if ($request->ajax()) {

			$selected_products = $request->selected_products;
			$client_id = $request->client_id;

            $dataget = DB::table('master_product')->where('is_approve', 1)->leftJoin('users',function($join){
				$join->on('users.id','=','master_product.updated_by');
			})
			->leftjoin("clients",\DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"),">",\DB::raw("'0'"))
			->leftjoin("prop_ingredients",\DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"),"!=",\DB::raw("''"))
			->leftjoin("categories",'categories.id',"=",'master_product.product_category');

			$dataget->whereRaw('FIND_IN_SET(\''.$client_id.'\',master_product.lobs)');


			for($i = 1; $i < 10; $i++){
				$dataget->leftjoin("categories as subcat".$i,"subcat".$i.".id","=","master_product.product_subcategory".$i);
			}

			$dataget->leftjoin("supplier_status",'supplier_status.id',"=",'master_product.supplier_status')
			->select(['master_product.*','categories.name as product_category','subcat1.name as product_subcategory1','subcat2.name as product_subcategory2','subcat2.name as product_subcategory2','subcat3.name as product_subcategory3','subcat4.name as product_subcategory4','subcat5.name as product_subcategory5','subcat6.name as product_subcategory6','subcat7.name as product_subcategory7','subcat8.name as product_subcategory8','subcat9.name as product_subcategory9','users.name as username',\DB::raw("GROUP_CONCAT(clients.company_name) as lobs"),\DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient"),\DB::raw("supplier_status.supplier_status as supplier_status")]);

			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;
            $searchBox = $request->text_data;
            //dd($request->main_filter);
			if(isset($filter_val)){
				foreach($filter_val as $key => $row_val){
					if(isset($row_val[$key])){

						$search_value_key = $row_val[$key];
						$search_value = '';
						if(isset($row_val[$search_value_key])){
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'],true);

						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						for($i = 1; $i < 10; $i++){
							if($column_name == 'product_subcategory'.$i){
								$select_table = 'subcat'.$i;
							}
						}

						if($search_value_key == "is_blank"){
							$dataget->whereNull($key);
						}

						if($search_value_key == "is_not_blank"){
							$dataget->whereNotNull($key);
						}

						if($search_value_key == "equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,$search_value);
							}else{
								$dataget->where($key,$search_value);
							}

						}
						if($search_value_key == "multiple" && $search_value != ""){
							$dataget->whereIn('master_product.ETIN',explode(',',$search_value));
						}

						if($search_value_key == "include_only" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'like', '%'.$value.'%');
									}
								});
							}
						}

						if($search_value_key == "exclude" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where(function ($query) use($select_table, $select_label_column, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($select_table.'.'.$select_label_column, 'not like', '%'.$value.'%');
									}
								});
							}else{
								$dataget->where(function ($query) use($key, $search_value) {
									$values = explode(',', $search_value);
									foreach($values as $value) {
										$query->orWhere($key, 'not like', '%'.$value.'%');
									}
								});
							}
						}
						if($search_value_key == "does_not_equals" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
							}else{
								$dataget->where($key,'!=',$search_value);
							}
						}

						if($search_value_key == "contains" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'%');
							}
						}

						if($search_value_key == "starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "ends_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'LIKE','%'.$search_value.'');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
							}else{
								$dataget->where($key,'NOT LIKE',''.$search_value.'%');
							}
						}

						if($search_value_key == "does_not_starts_with" && $search_value != ""){
							if($select_value_column == 'id' && $select_table != ''){
								$dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
							}else{
								$dataget->where($key,'NOT LIKE','%'.$search_value.'');
							}
						}
					}
				}
			}

			if(isset($request->search['value'])){
				$search_text = $request->search['value'];
				if($search_text != ''){
					$dataget->where(function($query) use($search_text){
						$query->where('clients.company_name','LIKE','%'.$search_text.'%');
						//$query->OrwhereIn('master_product.ETIN','%'.$search_text.'%');
						$query->Orwhere('master_product.parent_ETIN','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.product_listing_name','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.full_product_desc','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.about_this_item','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.manufacturer','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.brand','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.flavor','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.product_type','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.unit_size','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.unit_description','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.pack_form_count','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.unit_in_pack','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.item_form_description','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.supplier_product_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.upc','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.gtin','LIKE','%'.$search_text.'%');
						$query->Orwhere('master_product.manufacture_product_number','LIKE','%'.$search_text.'%');
						$query->Orwhere('categories.name','LIKE','%'.$search_text.'%');
						$query->Orwhere('subcat1.name','LIKE','%'.$search_text.'%');
						$query->Orwhere('subcat2.name','LIKE','%'.$search_text.'%');
						$query->Orwhere('subcat3.name','LIKE','%'.$search_text.'%');

						$query->Orwhere('supplier_status.supplier_status','LIKE','%'.$search_text.'%');
						$query->Orwhere('prop_ingredients.prop_ingredients','LIKE','%'.$search_text.'%');

						$query->Orwhere('users.name','LIKE','%'.$search_text.'%');
						$query->Orwhere('clients.company_name','LIKE','%'.$search_text.'%');

					});
				}

			}

			$main_filter = $request->main_filter;
			if(isset($main_filter)){
				foreach($main_filter as $key_main => $row_main_filter){
					if($row_main_filter != ''){
						$excluded_array = ['product_tags','warehouses_assigned','product_listing_ETIN','alternate_ETINs','ETIN','prop_65_ingredient','ingredients','parent_ETIN','prop_65_flag'];

						if(in_array($key_main,$excluded_array)){
							$unique_value = array_unique(explode(',',$row_main_filter));
							$dataget->where(function($q) use($unique_value,$key_main){
								if($unique_value){
									foreach($unique_value as $row_un_val){
										if($key_main == 'allergens'){
											$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\',master_product.'.$key_main.') > "0"');
										}else{
											$q->orWhereRaw('FIND_IN_SET(\''.$row_un_val.'\','.$key_main.') > "0"');
										}
									}
								}
							});
						}else{
							$dataget->where($key_main,'LIKE','%'.$row_main_filter.'%');
						}


					}
				}
			}



			$boolean_filters = $request->boolean_filters;
			if(!empty($boolean_filters)){
				foreach($boolean_filters as $key=>$value){
					if($value != '')
					$dataget->where($key,$value);
				}
			}

			$dataget->groupBy("master_product.id");
			// $total = count($dataget->get()->toArray());
			$total = count($dataget->get()->toArray());
			
			$limit = 12;
			if(isset($input['limit']))$limit = $input['limit'];

			$page = 1;
			if(isset($input['page']))$page = $input['page'];

			// $offset = ($page-1) * $limit;
			$offset = $request->get('start');
			$limit = $request->get('length');
			// dump($offset);
			// dump($limit);
			$dataget->skip($offset)->take($limit);


			// $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
			// $qry = vsprintf($qry, $dataget->getBindings());
			// dd($qry);
			$data = $dataget->get();
			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
            return Datatables::of($data)
			 ->filter(function ($query) {
							// if (request()->has('name')) {
							//     $query->where('name', 'like', "%" . request('name') . "%");
							// }

							// if (request()->has('email')) {
							//     $query->where('email', 'like', "%" . request('email') . "%");
							// }
                })
					->editColumn('is_approve', function ($data) {
								return  ($data->is_approve == '0')?"No":"Yes";
							})
					->addIndexColumn()
                    ->addColumn('action', function($row) use($ActiveProductListingsEditProduct,$selected_products){
						$checked = '';
						if($selected_products != ''){
							$SP = explode(',',$selected_products);

							if(in_array($row->ETIN,$SP)){
								$checked = 'checked=checked';
							}
						}
						$btn = '';
						$btn = '<input type="checkbox" onClick="SelectedOrderProducts(\''.$row->ETIN.'\')" '.$checked.'>';
						return $btn;
					})
					->editColumn('ETIN', function ($data) {
  						$btn = '';
							if($data->item_form_description == 'Kit'){
								$btn = '<a href="'.route('kits.edit',$data->id).'"  target="_blank">'.$data->ETIN.'</a>';
							}else{
								$btn = '<a href="'.route('editmasterproduct',$data->id).'"  target="_blank" >'.$data->ETIN.'</a>';
							}

                            return $btn;
					})
					->editColumn('product_listing_name', function ($data) {
  						return Str::limit($data->product_listing_name,100, ('...'));
					})
					->editColumn('full_product_desc', function ($data) {
						return Str::limit($data->full_product_desc,100, ('...'));
					})
					->editColumn('about_this_item', function ($data) {
						return Str::limit($data->about_this_item,100, ('...'));

					})
					->editColumn('ingredients', function ($data) {
						return Str::limit($data->ingredients,100, ('...'));
					})
					->editColumn('allergens', function ($data) {
						$allergens = DB::table('allergens')->select(\DB::raw("GROUP_CONCAT(allergens) as allergens"))->whereIN('id',explode(',',$data->allergens))->first();
						return Str::limit($allergens->allergens,100, ('...'));
					})
					->editColumn('product_tags', function ($data) {
						$product_tags = DB::table('product_tags')->select(\DB::raw("GROUP_CONCAT(tag	) as product_tags"))->whereIN('id',explode(',',$data->product_tags))->first();
						return Str::limit($product_tags->product_tags,100, ('...'));
					})
					// ->editColumn('supplier_description', function ($data) {
					// 	return Str::limit($data->supplier_description,100, ('...'));
					// })
					->editColumn('current_supplier', function ($data) {
						return Str::limit($data->current_supplier,100, ('...'));
					})
                    ->rawColumns(['action','ETIN'])
					->setTotalRecords($total)
					->setFilteredRecords($total)
					->skipPaging()
                    ->make(true);

        }
    }

	public function ViewTrackingDetails($order_number){
		$get_sub_orders = OrderDetail::where('order_number',$order_number)->whereIn('status',[6,13])->groupBy('sub_order_number')->pluck('sub_order_number');
		$result = OrderPackage::whereIn('order_id',$get_sub_orders)->whereNotNull('tracking_number')->groupBy('order_id','package_num')->get();
		return view('orders.view_tracking_details', compact('order_number','result'));
	}

	public function SaveProducts(Request $request){
		$selected_products = $request->selected_products;
		if($selected_products != ''){
			foreach(explode(',',$selected_products) as $row_pro){
				$OrderDetail = new OrderDetail();
				$OrderDetail->order_number = $request->etailer_order_number;
				$OrderDetail->ETIN = $row_pro;
				$OrderDetail->ETIN_flag = 0;
				$result = $OrderDetail->save();
			}
		}

		return response()->json([
			'error' => false,
			'msg' => 'Success',
			'url' => route('orders.edit',$request->order_id)
		]);	
	}

	public function showError(Request $request) {
		$errors = json_decode($request->errors);
		return view('orders.bulk_order_upload_error', compact('errors'));
	}

	public function update_sub_order_status($id,$status){
		$result = OrderDetailsStatus::where('id','!=',2)->get();
		return view('orders.update_sub_order_status',compact('id','status','result'));
	}

	public function update_sub_order_wh($sub_order_number){
		$wh_assigned = $this->getAssignedWarehouses(null, $sub_order_number);
		return view('orders.update_sub_order_wh',compact('sub_order_number', 'wh_assigned'));
	}

	public function UpdateOrderDetailWh(Request $request){
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);

		$so = OrderDetail::where('sub_order_number', $request->sub_order_number)->first();

		OrderDetail::where('sub_order_number', 
			$request->sub_order_number)->update(['warehouse' => $request->wh_assigned_so]);
		$this->setTransitDayAndAssignCarriers(null, $request->sub_order_number, $user_id, false);

		UpdateOrderHistory([
			'sub_order_number' => $request->sub_order_number,
			'order_number' => $so->order_number,
			'title' => 'Update Sub order Status',
			'detail' => "Sub order # $request->sub_order_number status has changed WH to " . $request->wh_assigned_so,
			'user_id' => $user_id,
			'reference' => 'PM',
			'extras' => json_encode($so)
		]);

		return response()->json([
			'error' => 0,
			'msg' => 'Success'
		]);	
	}

	private function setTransitDayAndAssignCarriers($order_number, $sub_order_number, $user, $is_sat = false) {

		if ($order_number != null) {
			$summary = OrderSummary::where('etailer_order_number', $order_number)->first();
			$so = OrderDetail::where('order_number', $order_number)->get();
		} else {
			$so = OrderDetail::where('sub_order_number', $sub_order_number)->get();
			$summary = OrderSummary::where('etailer_order_number', $so[0]->order_number)->first();
		}

		$zip = $summary->ship_to_zip;
		if (!isset($zip)) {
            Log::channel('IncomingOrderProcessing')->info('Invalid ZIP for Order: ' . $summary->etailer_order_number);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: WH Assignment',
                'details' => 'Error: Invalid ZIP Order Number: ' . $summary->etailer_order_number,
                'user' => $user,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            return;
        }

		$zip = substr($zip, 0, 3);
		$transitDays = DB::table('ups_zip_zone_wh')->where('zip_3', $zip)->first();

		if (!isset($transitDays)) {
			Log::channel('IncomingOrderProcessing')->info('Transit Day record not found for ZIP: ' . $zip);
			DB::table('order_history')->insert([
				'mp_order_number' => $summary->channel_order_number,
				'etailer_order_number' => $summary->etailer_order_number,
				'date' => date("Y-m-d H:i:s", strtotime('now')),
				'action' => 'Error: WH Assignment',
				'details' => 'Error: Transit Day record not found for ZIP: ' . $zip . ', Order Number: ' . $summary->etailer_order_number . ' for ETIN ' . $orderDetail->ETIN,
				'user' => $user,
				'created_at' => date("Y-m-d H:i:s"),
				'updated_at' => date("Y-m-d H:i:s")
			]);
			return;
		}

		$td = '';
		if ($so[0]->warehouse === 'WI') { $td = $transitDays->transit_days_WI; }
		if ($so[0]->warehouse === 'PA') { $td = $transitDays->transit_days_PA; }
		if ($so[0]->warehouse === 'NV') { $td = $transitDays->transit_days_NV; }
		if ($so[0]->warehouse === 'OKC') { $td = $transitDays->transit_days_OKC; }

		OrderDetail::where('order_number', $summary->etailer_order_number)->update(['transit_days' => $td]);

		if (!$is_sat) {
			foreach($so as $order) {
				$this->assignCarrierAndAccountAndServiceType($order, $summary, $user);
			}
		}
	}

	private function assignCarrierAndAccountAndServiceType($order, $summary, $user) {

        $customClient = DB::table("carrier_order_account_assignments")
            ->where('client_id', $summary->client_id)->first();

        $warehouse = $order->warehouse;

        if(strtolower($warehouse) == 'okc') { $warehouse = 'ok'; }
        $temp = $this->getTemp($order->fulfilled_by, $order->sub_order_number);
        if (!isset($temp) || $temp === '') {
            Log::channel('IncomingOrderProcessing')->info('Temperature cannot be set. Fulfilled/Sub Order Number is empty or net set. ETIN: ' . $order->ETIN);
            DB::table('order_history')->insert([
                'mp_order_number' => $summary->channel_order_number,
                'etailer_order_number' => $summary->etailer_order_number,
                'date' => date("Y-m-d H:i:s", strtotime('now')),
                'action' => 'Error: Cannot Assign Carrier',
                'details' => 'Temprature cannot be set. Fulfilled/Sub Order Number is empty or net set. ETIN: ' . $order->ETIN,
                'user' => $user,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);
            return;
        }

        Log::channel('IncomingOrderProcessing')->info($order->fulfilled_by . ' ' . $order->sub_order_number . ' ' . $temp);

        $carrierColName = strtolower($temp) . '_' . strtolower($warehouse) . '_carrier_id';
        $accountColName = strtolower($temp) . '_' . strtolower($warehouse) . '_account_id';

        $carrierId = null; $accountId = null;
        
        if (!$customClient) {
            Log::channel('IncomingOrderProcessing')->info('Client custom config not found. Getting default One.');
            $customClient = DB::table("carrier_order_account_assignments")->where('is_default', 1)->first();
            if (!isset($customClient)) {
                Log::channel('IncomingOrderProcessing')->info('Default config not found.');
            }
        }

        $carrierId = $customClient->$carrierColName;
        $accountId = $customClient->$accountColName;

        $carrier = isset($carrierId) ? Carrier::where('id', $carrierId)->first() : null;
        
        $code = null;
        if (!isset($carrier)) {
            Log::channel('IncomingOrderProcessing')->info('Carrier not found. Cannot set service type');
        } else {
            Log::channel('IncomingOrderProcessing')->info('Carrier found. Service type setting will follow.');
            $upgrades = OrderAutomaticUpgrades::where('client_id', $summary->client_id)->first();
            if ($upgrades) {
                Log::channel('IncomingOrderProcessing')->info('Service type Upgrade found.');
                $sst = ShippingServiceType::where('id', $upgrades->service_type_id)->first();
                if (!$sst) {
                    Log::channel('IncomingOrderProcessing')->info('Shipping Service Type Code not found');
                } else {
                    $code = $sst->id;
                }
            } else {
                Log::channel('IncomingOrderProcessing')->info('Service type Upgrade not found. Going for default');
                $default = EtailerService::where('etailer_service_name', 'Ground')->first();
                if (!$default) {
                    Log::channel('IncomingOrderProcessing')->info('Default Shipping Service not found');
                } else {
                    Log::channel('IncomingOrderProcessing')->info('Default Shipping for ' . $carrier->company_name);
                    Log::channel('IncomingOrderProcessing')->info('Code: ' . (strtolower($carrier->company_name) === 'ups' 
                                            ? $default->ups_service_type_id : $default->fedex_service_type_id));
                    $code = strtolower($carrier->company_name) === 'ups' ? $default->ups_service_type_id : $default->fedex_service_type_id;
                }
            }
        }

        $order->carrier_id = $carrierId;
        $order->carrier_account_id = $accountId;
        $order->service_type_id = $code;        
        $order->status = 1;
        $order->save();
    }

	public function UpdateOrderDetailStatus(Request $request){
		$header = $request->header('Authorization');
		$user_id = ExtractToken($header);
		$OrderDetail = new OrderDetail();
		$result = $OrderDetail->UpdateOrderStatus($request->order_id,$request->status_id,$user_id);
		if($result == "Error"){
			return response()->json([
				'error' => 1,
				'msg' => 'Something went wrong'
			]);	
		}else{
			return response()->json([
				'error' => 0,
				'msg' => 'Success'
			]);	
		}
		
	}

	public function cancel_order(Request $request){
		$order_id = $request->order_id;

		if(!$order_id){
			return response()->json([
				'error' => 1,
				'msg' => 'Order ID not found'
			]);
		}

		$orderSummary = OrderSummary::find($order_id);
		if(!$orderSummary){
			return response()->json([
				'error' => 1,
				'msg' => 'Order not found'
			]);
		}

		$orderSummary->order_status = 24;
		$orderSummary->save();
		UpdateOrderHistory([
			'order_number' => $orderSummary->etailer_order_number,
			'detail' => 'Order #: '.$orderSummary->etailer_order_number .' has been Cancelled',
			'title' => 'Order Cancelled',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($orderSummary)
		]);			

		$orderDetails = OrderDetail::where('order_number', $orderSummary->etailer_order_number)->get();
		foreach($orderDetails as $row){
			$row->status = 14;
			$row->save();
		}

		return response()->json([
			'error' => 0,
			'msg' => 'Order Cancelled'
		]);
	}

	public function OrderHistory($order_number){
		$result = OrderHistory::where('etailer_order_number',$order_number)->leftJoin('users',function($join){
			$join->on('users.id','=','order_history.user_id');
		})->select('order_history.*','users.name as user_name')->orderBy('order_history.id','DESC')->get();
		return view('orders.view_order_history', compact('result','order_number'));
	}

	public function delete_sub_order_items($id){
		$orderDetails = OrderDetail::find($id);
		if(in_array($orderDetails->status,[2,3,4,6,10,11,12,13])){
			return redirect()->back()->with('error','Item can not be Deleted');	
		}
		UpdateOrderHistory([
			'order_number' => $orderDetails->order_number,
			'sub_order_number' => $orderDetails->sub_order_number,
			'detail' => 'ETIN '.$orderDetails->ETIN.' Has been deleted',
			'title' => 'Sub order Item deleted',
			'user_id' => auth()->user()->id,
			'reference' => 'PM',
			'extras' => json_encode($orderDetails)
		]);
		$orderDetails->delete();
		return redirect()->back()->with('success','Item Deleted');
	}	
}
