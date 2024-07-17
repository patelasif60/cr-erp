<?php

namespace App\Http\Controllers;
use App;
use PDF;
use Excel;
use Schema;
use App\User;
use App\Client;
use DataTables;
use App\SmartFilter;
use Aws\S3\S3Client;
use App\ProductImage;
use App\MasterProduct;
use App\UploadHistory;
use App\SupplierStatus;
use App\MasterProductQueue;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\ProductListingFilter;
use App\ThreeplClientProduct;
use Aws\Exception\AwsException;
use App\RequestProductSelection;
use App\SupplementalMptDataQueue;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\DB;
use App\Imports\ThreePLClientImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\MasterProductExcelExport;
use App\Imports\ThreePLClientInsertImport;
use App\Http\Requests\MasterProductRequest;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\MasterProductApproveRequest;
use App\Notifications\ApproveRejectProductNotification;
use App\Supplier;

class MasterProductsOptimizedController extends Controller
{
	public function __construct(MasterProduct $masterProduct, SmartFilter $SmartFilter ,ProductListingFilter $ProductListingFilter)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		// ini_set('max_input_vars', 2000);
		$this->masterProduct = $masterProduct;
		$this->SmartFilter = $SmartFilter;
		$this->ProductListingFilter = $ProductListingFilter;
	}


	public function allOptimizedMasterproducts($id = NULL){
		if(moduleacess('ListAllMasterProduct') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
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
		return view('cranium.optimize_product_list',['product_listing_filter' => $product_listing_filter, 'smart_filters' => $smart_filters, 'selected_smart_filter' => $selected_smart_filter, 'id' => $id, 'hidden_cols' => $hidden_cols, 'visible_columns' => $visible_columns, 'visible_filters' => $visible_filters,'smart_filter' => $smart_filter,'main_filter' => $main_filter, 'hidden_cols_arr' => $hidden_cols_arr ]);
	}
    // allergens
    public function getOptimizedMasterproducts(Request $request)
    {
		if ($request->ajax()) {

            $dataget = DB::table('master_product')->where('is_approve', 1)
			->leftjoin("categories",'categories.id',"=",'master_product.product_category')
			->select(['master_product.id','master_product.ETIN','master_product.product_listing_name',
				'master_product.product_type','master_product.upc','master_product.gtin','master_product.status',
				'categories.name as product_category','master_product.item_form_description','master_product.is_approve',
				'full_product_desc','about_this_item','ingredients','allergens','product_tags','current_supplier',
				'master_product.lobs','master_product.client_supplier_id','master_product.supplier_type']);

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
								$dataget->whereIN($select_table.'.'.$select_label_column,explode(',',$search_value));
							}else{
								$dataget->whereIN($key,explode(',',$search_value));
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
                    ->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
							$btn = '';
							if($row->item_form_description == 'Kit'){
								$btn = '<a href="'.route('kits.edit',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
							}else{
								if($ActiveProductListingsEditProduct){
									$btn = '<a href="'.route('editmasterproduct',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
								}
							}

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

						$cl_sup_id = $data->client_supplier_id;
						$type = $data->supplier_type;
						$cl_sup = $data->supplier_type;

						if ($type === 'client') {
							$cl = Client::where('id', $cl_sup_id)->first();
							if (isset($cl)) { $cl_sup = $cl->company_name; }
						} else if ($cl_sup === 'supplier') {
							$sup = Supplier::where('id', $cl_sup_id)->first();
							if (isset($sup)) { $cl_sup = $sup->name; }
						}

						return Str::limit($cl_sup, 100, ('...'));
					})
					->editColumn('lobs', function ($data) {
						if (!isset($data->lobs) || $data->lobs == '') { return ''; }
						else if (str_contains($data->lobs, ',')) { 
							$ids = explode(',', $data->lobs);
							$cl = Client::whereIn('id', $ids)->select('company_name')->get()->pluck('company_name')->toArray();
							if (!isset($cl) && count($cl) <= 0) { return ''; }
							else return implode(' || ', $cl);
						}
						else {
							$cl = Client::where('id', $data->lobs)->first();
							if (!isset($cl)) { return ''; }
							else return $cl->company_name;
						}
					})
                    ->rawColumns(['action','ETIN'])
					->setTotalRecords($total)
					->setFilteredRecords($total)
					->skipPaging()
                    ->make(true);

        }
    }

	public function masterproductsFilter1(Request $request)
    {
		if ($request->ajax()) {

            $dataget = DB::table('master_product')->where('is_approve', 1)->leftJoin('users',function($join){
				$join->on('users.id','=','master_product.updated_by');
			})
			->leftjoin("clients",\DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"),">",\DB::raw("'0'"))
			->leftjoin("prop_ingredients",\DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"),"!=",\DB::raw("''"))
			->leftjoin("categories",'categories.id',"=",'master_product.product_category');

			for($i = 1; $i < 10; $i++){
				$dataget->leftjoin("categories as subcat".$i,"subcat".$i.".id","=","master_product.product_subcategory".$i);
			}

			$dataget->leftjoin("supplier_status",'supplier_status.id',"=",'master_product.supplier_status')
			->select(['master_product.*','categories.name as product_category',
					'subcat1.name as product_subcategory1','subcat2.name as product_subcategory2',
					'subcat2.name as product_subcategory2','subcat3.name as product_subcategory3',
					'subcat4.name as product_subcategory4','subcat5.name as product_subcategory5',
					'subcat6.name as product_subcategory6','subcat7.name as product_subcategory7',
					'subcat8.name as product_subcategory8','subcat9.name as product_subcategory9',
					'users.name as username',\DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient")
					,\DB::raw("supplier_status.supplier_status as supplier_status")]);

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
								$dataget->whereIN($select_table.'.'.$select_label_column,explode(',',$search_value));
							}else{
								$dataget->whereIN($key,explode(',',$search_value));
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
						$excluded_array = ['product_tags','warehouses_assigned','product_listing_ETIN','alternate_ETINs','ETIN','prop_65_ingredient','ingredients','parent_ETIN','prop_65_flag','lobs'];

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

			if($limit >= 0){
				$dataget->skip($offset)->take($limit);
			}

			$data = $dataget->get();
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
                    ->addColumn('action', function($row) use($ActiveProductListingsEditProduct){
							$btn = '';
							if($row->item_form_description == 'Kit'){
								$btn = '<a href="'.route('kits.edit',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
							}else{
								if($ActiveProductListingsEditProduct){
									$btn = '<a href="'.route('editmasterproduct',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
								}
							}

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

						$cl_sup_id = $data->client_supplier_id;
						$type = $data->supplier_type;
						$cl_sup = 'Hello';

						if ($type === 'client') {
							$cl = Client::where('id', $cl_sup_id)->first();
							if (isset($cl)) { $cl_sup = $cl->company_name; }
						} else if ($cl_sup === 'supplier') {
							$sup = Supplier::where('id', $cl_sup_id)->first();
							if (isset($sup)) { $cl_sup = $sup->name; }
						}

						return Str::limit($cl_sup, 100, ('...'));
					})
					->editColumn('lobs', function ($data) {
						if (!isset($data->lobs) || $data->lobs == '') { return ''; }
						else if (str_contains($data->lobs, ',')) { 
							$ids = explode(',', $data->lobs);
							$cl = Client::whereIn('id', $ids)->select('company_name')->get()->pluck('company_name')->toArray();
							if (!isset($cl) && count($cl) <= 0) { return ''; }
							else return implode(' || ', $cl);
						}
						else {
							$cl = Client::where('id', $data->lobs)->first();
							if (!isset($cl)) { return ''; }
							else return $cl->company_name;
						}
					})
                    ->rawColumns(['action','ETIN'])
					->setTotalRecords($total)
					->setFilteredRecords($total)
					->skipPaging()
                    ->make(true);

        }
    }
}
