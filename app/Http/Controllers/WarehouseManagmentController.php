<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use App\WareHouse;
use App\MasterShelf;
use App\SmartFilter;
use App\Client;
use App\User;
use App\CycleCountDetail;
use App\CycleCountSummary;
use Illuminate\Http\Request;
use App\ProductListingFilter;
use Illuminate\Support\Facades\DB;
use App\AisleMaster;
class WarehouseManagmentController extends Controller
{
    public function __construct(SmartFilter $SmartFilter)
    {
        ini_set('max_execution_time', 999999);
        ini_set('memory_limit', '1024M');
        $this->SmartFilter = $SmartFilter;
    }
    public function cycleCountIndex(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('WarehouseManagment.cyclecountindex');
    }

    public function cycleCountList(){
        
        $cycleCountSummaries = CycleCountSummary::all()->sortByDesc('scheduled_date');
        return Datatables::of($cycleCountSummaries)->addColumn('warehouse', function($cycleCountSummary)
        {
            return $cycleCountSummary->warehouse->warehouses;
            
        })
        ->addColumn('action', function($row) {
            $btn = '';
            if ($row->status == 'InProcess') {
                $btn = '<a href="' . route('warehousemanagment.cyclecount.edit', $row->id) . '" class="edit btn btn-primary btn-sm mr-1" >Edit</a>';
                $btn = $btn . '<a href="javascript:void(0)" onclick="deleteSummary(\''.$row->id.'\')" class="btn btn-danger btn-sm"><i class="nav-icon i-Close-Window"></i> </a>';
            }
            else if($row->status == 'AwaitingApproval') {
                $btn = '<a href="' . route('warehousemanagment.cyclecount.approval', $row->id) . '" class="edit btn btn-primary btn-sm mr-1" >Awaiting Approval</a>';
            }
            else if($row->status == 'PartiallyCompleted') {
                $btn = '<a href="' . route('warehousemanagment.cyclecount.approval', $row->id) . '" class="edit btn btn-primary btn-sm mr-1" >Partially Completed</a>';
            }
            else if ($row->status == 'Complete') {
                $btn = '<a href="' . route('warehousemanagment.cyclecount.complatelist', $row->id) . '" class="edit btn btn-primary btn-sm mr-1" >View</a>'; 
            }

            return $btn;
        })
        ->addColumn('c_type', function($row) {
            return strtoupper($row->count_type);
        })
        ->rawColumns(['warehouse', 'action', 'c_type'])->make(true);
    }
    public function cycleCountcreate($id=null){
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
        //dd($smart_filters);
        $warehouses = WareHouse::all();
        $client = Client::get()->pluck('company_name','id')->toArray();
        $users = User::whereIN('role',[5])->where('wh_id',1)->pluck('name','id');
        return view('WarehouseManagment.cyclecountcreate',compact('warehouses','client','product_listing_filter','smart_filters','id','users')); 
    }

    public function cycleCountApproval($row_id){
        return view('WarehouseManagment.cyclecountapproval',compact('row_id'));   
    }
    public function cycleComplatelist($row_id){
        return view('WarehouseManagment.cyclecomplatelist',compact('row_id'));   
    }
    public function awaitapprovedproducts(Request $request) {

        $approved_products = CycleCountDetail::where('cycle_count_summary_id', $request->cc_sum_id)
                                ->join("master_product",'master_product.ETIN', "=", 'cycle__count__detail.ETIN')
                                ->get(['cycle__count__detail.*', 'master_product.*','cycle__count__detail.id as primaryId','cycle__count__detail.status as appovestatus']);
        return Datatables::of($approved_products)->addIndexColumn()
        ->addColumn('approve_check', function ($row) {
            if ($row->appovestatus == 1) {
                $checkbox = '<input checked class="form-check-input newApproveCheckBox2" style="margin-left:-5px" type="checkbox" disabled checked id="new_approve_' . $row->primaryId . '" name="new_approve[]" value="' . $row->primaryId . '">';
            } else {
                $checkbox = '<input class="form-check-input newApproveCheckBox2" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->primaryId . '" name="new_approve[]" value="' . $row->primaryId . '">';
            }
            return $checkbox;
        })
        ->rawColumns(['approve_check'])
        ->make(true);
    }
    public function cyclecomplatelistDatatable(Request $request) {

        $approved_products = CycleCountDetail::where('cycle_count_summary_id', $request->cc_sum_id)
                                ->join("master_product",'master_product.ETIN', "=", 'cycle__count__detail.ETIN')
                                ->get(['cycle__count__detail.*', 'master_product.*','cycle__count__detail.id as primaryId','cycle__count__detail.status as appovestatus']);
        return Datatables::of($approved_products)

        ->make(true);
    }
    public function approveawaitcyclecountproduct(Request $request){
        $summeryData = CycleCountSummary::where('id',$request->summery_id);
        if($request->flag)
        {
            $result = CycleCountDetail::whereIn('id', $request->checked)->update(['status' => 1]);
        }else{
            $dataGet = $summeryData->first();
            $reconutCycleCountSummary = CycleCountSummary::create([
                "warehouse_id" => $dataGet->warehouse_id,
                "scheduled_date" => date('Y-m-d'),
                "status" => 'Scheduled',
                "count_type"=>$dataGet->count_type,
            ]);
            $result = CycleCountDetail::whereIn('id', $request->checked)->update(['cycle_count_summary_id' => $reconutCycleCountSummary->id,'status'=>null,'total_counted'=>null,'total_expired'=>null,'type'=>null,'exp_date'=>null]);
        }
        $count_cyle_detail_data = CycleCountDetail::where('cycle_count_summary_id', $request->summery_id);
        $st = 'PartiallyCompleted';
        if($count_cyle_detail_data->count() == $count_cyle_detail_data->where('status',1)->count())
        {
            $st = 'Complete';
        }
        $summeryData->update(['status' => $st]);
        if ($result) {
            $data = [
                'error' => false,
                'msg' => count($request->checked) . " Product Cycle count created"
            ];
        } else {
            $data = [
                'error' => true,
                'msg' => "Something Went Wrong"
            ];
        }

        return response()->json($data);
    }

    public function cycleCountEdit($row_id, $id=null){
        $cc_sum = CycleCountSummary::find($row_id);
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
        //dd($smart_filters);
        $warehouses = WareHouse::all();
        $client = Client::get()->pluck('company_name','id')->toArray();
        $users = User::whereIN('role',[5])->where('wh_id',1)->pluck('name','id');
        return view('WarehouseManagment.cyclecountedit', 
                    compact('warehouses','product_listing_filter','smart_filters','id', 'row_id', 'cc_sum','client','users'));   
    }

    public function getactivemasterproducts(Request $request){
        
        $cc_sum_details = isset($request->cc_sum_id) && $request->cc_sum_id != null 
                            ? CycleCountDetail::where('cycle_count_summary_id',  $request->cc_sum_id)->select(['ETIN'])->pluck('ETIN')->all()
                            : Array();
        // return response(['cc_sum_details' => $cc_sum_details]);
        //dd($request->warehouse);
        $dataget = DB::table('master_product')
            ->join('master_shelf','master_shelf.ETIN','=','master_product.ETIN')
            ->leftjoin("categories",'categories.id',"=",'master_product.product_category')
            ->leftjoin("clients",\DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"),">",\DB::raw("'0'"))
            ->leftjoin("prop_ingredients",\DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"),"!=",\DB::raw("''"));
            for($i = 1; $i < 10; $i++){
                $dataget->leftjoin("categories as subcat".$i,"subcat".$i.".id","=","master_product.product_subcategory".$i);
            }

            $dataget->leftjoin("supplier_status",'supplier_status.id',"=",'master_product.supplier_status')
            ->where('is_approve', 1)
            ->whereRaw('FIND_IN_SET(\''.$request->warehouse.'\',master_product.warehouses_assigned)');
            if($request->client_id_for_cycle_count > 0)
            {
                $dataget->whereRaw('FIND_IN_SET(\''.$request->client_id_for_cycle_count.'\',master_product.lobs)');
            }
            $dataget->whereNotNull('master_shelf.address')->whereNotNull('master_shelf.ETIN')->whereNotNull('master_product.ETIN')
            ->groupBy('master_product.ETIN')->select(['master_product.*','categories.name as product_category',
                        'subcat1.name as product_subcategory1','subcat2.name as product_subcategory2','subcat2.name as product_subcategory2',
                        'subcat3.name as product_subcategory3','subcat4.name as product_subcategory4','subcat5.name as product_subcategory5',
                        'subcat6.name as product_subcategory6','subcat7.name as product_subcategory7','subcat8.name as product_subcategory8',
                        'subcat9.name as product_subcategory9',\DB::raw("GROUP_CONCAT(clients.company_name) as lobs"),
                        \DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient"),
                        \DB::raw("supplier_status.supplier_status as supplier_status")]);
            if(isset($request->search['value'])){
                //$search_text = $request->search['value'];
                $search_text = trim($request->search['value']);

                if($search_text != ''){
                    $dataget->where(function($query) use($search_text){
                        $query->where('clients.company_name','LIKE','%'.$search_text.'%');
                        $query->Orwhere('master_product.ETIN','LIKE','%'.$search_text.'%');
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
                        $query->Orwhere('clients.company_name','LIKE','%'.$search_text.'%');

                    });
                }

            }
            //dd($dataget->get());
            $total = count($dataget->get());
            $limit = 10;
            if (isset($input['limit'])) $limit = $input['limit'];

            $page = 1;
            if (isset($input['page'])) $page = $input['page'];

            $offset = $request->get('start');
            $limit = $request->get('length');
            $dataget->skip($offset)->take($limit);
            // if(isset($request->search['value'])){
            //     $data = $this->addSelectedToData($dataget->get(), $cc_sum_details, 1);
            // }
            // else{
                $data = $this->addSelectedToData($dataget->get(), $cc_sum_details, 1);
            //}           
            //dd($data);
        return Datatables::of($data)
                ->filter(function ($query) {})
                ->addIndexColumn()
                ->addColumn('approve_check', function ($row) {
                    if (isset($row->selected) && $row->selected == 1) {
                        $checkbox = '<input checked class="form-check-input newApproveCheckBox" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    } else {
                        $checkbox = '<input class="form-check-input newApproveCheckBox" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    }
                    return $checkbox;
                })
                ->rawColumns(['approve_check'])
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->skipPaging()
                ->make(true);
    }

    public function getlocaionwarehousewise(Request $request){
        
        $cc_sum_details = isset($request->cc_sum_id) && $request->cc_sum_id != null 
                            ? CycleCountDetail::where('cycle_count_summary_id',  $request->cc_sum_id)->select(['ETIN'])->pluck('ETIN')->all()
                            : Array();
        // return response(['cc_sum_details' => $cc_sum_details]);
        //dd($request->warehouse);
        $warehouses = WareHouse::where('warehouses',$request->warehouse)->first();
        $dataget =AisleMaster::where('warehouse_id',$warehouses->id)->with('storage_type','warehouse_name');

            $total = count($dataget->get());
            $limit = 10;
            if (isset($input['limit'])) $limit = $input['limit'];

            $page = 1;
            if (isset($input['page'])) $page = $input['page'];

            $offset = $request->get('start');
            $limit = $request->get('length');
            $dataget->skip($offset)->take($limit);            
            $data = $this->addSelectedToData($dataget->get(), $cc_sum_details, 1);
        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('approve_check', function ($row) {
                    if (isset($row->selected) && $row->selected == 1) {
                        $checkbox = '<input checked class="form-check-input newApproveCheckBox2" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    } else {
                        $checkbox = '<input class="form-check-input newApproveCheckBox2" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    }
                    return $checkbox;
                })
                ->rawColumns(['approve_check'])
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->skipPaging()
                ->make(true);
    }

    public function approveNewProducts(Request $request)
    {   

        //$result = MasterShelf::whereIn('id', $request->checked)->get();
        //dd($result);
        $warehouses = WareHouse::where('warehouses',$request->warehouse)->first();
        $cycleCountSummary = null;

        // Delete Old Data
        if (isset($request->cc_sum_id) && $request->cc_sum_id != null) {
            CycleCountDetail::where('cycle_count_summary_id', $request->cc_sum_id)->delete();
            $cycleCountSummary = CycleCountSummary::where('id', $request->cc_sum_id)->first();
            $cycleCountSummary->client_id = $request->client_id_for_cycle_count;
            $cycleCountSummary->user_id = $request->user_id;
            $cycleCountSummary->save();
        } else {            
            $cycleCountSummary = CycleCountSummary::create([
                "warehouse_id" => $warehouses->id,
                "scheduled_date" =>$request->schedule_date,
                "status" => 'Scheduled',
                "count_type"=>$request->countBy,
                "client_id"=>$request->client_id_for_cycle_count,
                "user_id"=>$request->user_id
            ]);
        }

        if($request->countBy == "product"){
            $aisle= AisleMaster::where('warehouse_id',$warehouses->id)->pluck('id');
            $result = DB::table('master_product')->whereIn('id', $request->checked)->get();
            $etin = $result->pluck('ETIN')->toArray();
            $result1 = DB::table('master_product')->whereIn('parent_ETIN',$etin)->get();
            $etin1 = $result1->pluck('ETIN')->toArray();
            $etin = array_unique(array_merge($etin,$etin1));
            $shelfAddress = MasterShelf::whereIn('ETIN',$etin)->whereIn('aisle_id',$aisle)->get();
            //$addresses = $shelfAddress->pluck('address')->toArray();
            //$masterShelf = MasterShelf::whereIn('address',$addresses)->get();
            foreach ($shelfAddress as $key => $value) {
                CycleCountDetail::create([
                    "cycle_count_summary_id" =>$cycleCountSummary->id,
                    "ETIN" =>$value->ETIN,
                    "address"=>$value->address,
                    "total_on_hand" => $value->cur_qty,
                ]);
            }
        }
        else{
            $result = MasterShelf::whereIn('id', $request->checked)->get();
            foreach ($result as $key => $value) {
                CycleCountDetail::create([
                    "cycle_count_summary_id" =>$cycleCountSummary->id,
                    "ETIN" =>$value->ETIN,
                    "address"=>$value->address,
                    "total_on_hand" => $value->cur_qty,
                ]);
            }
        }        

        if ($result) {
            $data = [
                'error' => false,
                'msg' => count($request->checked) . " Product Cycle count created"
            ];
        } else {
            $data = [
                'error' => true,
                'msg' => "Something Went Wrong"
            ];
        }

        return response()->json($data);
    }
    public function getfillterProduct(Request $request){
        $dataget = DB::table('master_product')
        ->join('master_shelf','master_shelf.ETIN','=','master_product.ETIN')
        ->leftjoin("categories",'categories.id',"=",'master_product.product_category')
        ->leftjoin("clients",\DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"),">",\DB::raw("'0'"))
        ->leftjoin("prop_ingredients",\DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"),"!=",\DB::raw("''"));
        for($i = 1; $i < 10; $i++){
            $dataget->leftjoin("categories as subcat".$i,"subcat".$i.".id","=","master_product.product_subcategory".$i);
        }

        $dataget->leftjoin("supplier_status",'supplier_status.id',"=",'master_product.supplier_status')
        ->where('is_approve', 1)
        ->whereRaw('FIND_IN_SET(\''.$request->warehouse.'\',master_product.warehouses_assigned)');
        if($request->client_id_for_cycle_count > 0)
        {
            $dataget->whereRaw('FIND_IN_SET(\''.$request->client_id_for_cycle_count.'\',master_product.lobs)');
        }
        $dataget->whereNotNull('master_shelf.address')
        ->groupBy('master_product.ETIN')
        ->select(['master_product.*','categories.name as product_category','subcat1.name as product_subcategory1','subcat2.name as product_subcategory2','subcat2.name as product_subcategory2','subcat3.name as product_subcategory3','subcat4.name as product_subcategory4','subcat5.name as product_subcategory5','subcat6.name as product_subcategory6','subcat7.name as product_subcategory7','subcat8.name as product_subcategory8','subcat9.name as product_subcategory9',\DB::raw("GROUP_CONCAT(clients.company_name) as lobs"),\DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient"),\DB::raw("supplier_status.supplier_status as supplier_status")]);
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
                        //dd($row_val);
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
                            $key == 'ETIN' ? $dataget->whereNull('master_product.ETIN') : 
                            $dataget->whereNull($key);
                        }

                        if($search_value_key == "is_not_blank"){
                            $key == 'ETIN' ? $dataget->whereNotNull('master_product.ETIN') : 
                            $dataget->whereNotNull($key);
                        }

                        if($search_value_key == "equals" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,$search_value);
                            }else{
                                if($key == 'ETIN'){
                                    $dataget->where('master_product.ETIN',$search_value);
                                }
                                else{
                                    $dataget->where($key,$search_value);
                                }
                            }

                        }
                        if($search_value_key == "multiple" && $search_value != ""){
                            $dataget->whereIn('master_product.ETIN',explode(',',$search_value));
                        }

                        if($search_value_key == "include_only" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->whereIN($select_table.'.'.$select_label_column,explode(',',$search_value));
                            }else{
                                $key == 'ETIN' ? $dataget->whereIN('master_product.ETIN',explode(',',$search_value)) : $dataget->whereIN($key,explode(',',$search_value));
                            }
                        }

                        if($search_value_key == "exclude" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->whereNotIN($select_table.'.'.$select_label_column,explode(',',$search_value));
                            }else{
                                $key == 'ETIN' ? $dataget->whereNotIN('master_product.ETIN',explode(',',$search_value)) : $dataget->whereNotIN($key,explode(',',$search_value));
                            }

                        }
                        if($search_value_key == "does_not_equals" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'!=',$search_value);
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','!=',$search_value) : $dataget->where($key,'!=',$search_value);
                            }
                        }

                        if($search_value_key == "contains" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'%');
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','LIKE','%'.$search_value.'%') :
                                $dataget->where($key,'LIKE','%'.$search_value.'%');
                            }
                        }

                        if($search_value_key == "starts_with" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'LIKE',''.$search_value.'%');
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','LIKE',''.$search_value.'%') :
                                $dataget->where($key,'LIKE',''.$search_value.'%');
                            }
                        }

                        if($search_value_key == "ends_with" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'LIKE','%'.$search_value.'');
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','LIKE','%'.$search_value.'') :
                                $dataget->where($key,'LIKE','%'.$search_value.'');
                            }
                        }

                        if($search_value_key == "does_not_starts_with" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'NOT LIKE',''.$search_value.'%');
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','NOT LIKE',''.$search_value.'%') :
                                $dataget->where($key,'NOT LIKE',''.$search_value.'%');
                            }
                        }

                        if($search_value_key == "does_not_starts_with" && $search_value != ""){
                            if($select_value_column == 'id' && $select_table != ''){
                                $dataget->where($select_table.'.'.$select_label_column,'NOT LIKE','%'.$search_value.'');
                            }else{
                                $key == 'ETIN'? $dataget->where('master_product.ETIN','NOT LIKE','%'.$search_value.'') :
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
                        $query->OrwhereIn('master_product.ETIN','%'.$search_text.'%');
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
            $dataget->skip($offset)->take($limit);


            // $qry = str_replace(array('%', '?'), array('%%', '%s'), $dataget->toSql());
            // $qry = vsprintf($qry, $dataget->getBindings());
            // dd($qry);
            $data = $dataget->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('approve_check', function ($row) {
                    $checkbox = '<input class="form-check-input newApproveCheckBox" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    return $checkbox;
                })
                ->rawColumns(['approve_check'])
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->skipPaging()
                ->make(true);
    }
    public function locationcyclenew(Request $request)
    {
        $cc_sum_details = isset($request->cc_sum_id) && $request->cc_sum_id != null 
                            ? CycleCountDetail::where('cycle_count_summary_id',  $request->cc_sum_id)->get(['ETIN', 'address'])
                            : Array();
        $dataget = MasterShelf::whereIN('aisle_id',$request->aisleName)->whereNotNull('master_shelf.ETIN')->where('cur_qty','>',0);
        //join('master_product', 'master_shelf.ETIN', '=', 'master_product.ETIN')->
        // if($request->client_id_for_cycle_count > 0)
        // {
        //     $dataget->whereRaw('FIND_IN_SET(\''.$request->client_id_for_cycle_count.'\',master_product.lobs)');
        // }
        $total = $dataget->count();
        $limit = 10;
        if (isset($input['limit'])) $limit = $input['limit'];

        $page = 1;
        if (isset($input['page'])) $page = $input['page'];

        $offset = $request->get('start');
        $limit = $request->get('length');
        $dataget->skip($offset)->take($limit);
        $data = $dataget->get();
        $data = $this->addSelectedToData($dataget->get(), $cc_sum_details, 2);
        
        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('approve_check', function ($row) {
                    if (isset($row->selected) && $row->selected == 1) {
                        $checkbox = '<input checked class="form-check-input newApproveCheckBox1" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    } else {
                        $checkbox = '<input class="form-check-input newApproveCheckBox1" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    }
                    return $checkbox;
                })
                ->rawColumns(['approve_check'])
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->skipPaging()
                ->make(true);
    }
    public function locationcycle(Request $request)
    {
        $cc_sum_details = isset($request->cc_sum_id) && $request->cc_sum_id != null 
                            ? CycleCountDetail::where('cycle_count_summary_id',  $request->cc_sum_id)->get(['ETIN', 'address'])
                            : Array();
        $warehouses = WareHouse::where('warehouses',$request->warehouse)->first();
        $aisle = DB::table('master_aisle')->where('aisle_name', $request->aisleName)->where('warehouse_id',$warehouses->id)->first();
        $aisleId = $aisle ? $aisle->id : 0;
        $dataget = MasterShelf::where('aisle_id',$aisleId)->whereNotNull('ETIN');
        $total = $dataget->count();
        $limit = 10;
        if (isset($input['limit'])) $limit = $input['limit'];

        $page = 1;
        if (isset($input['page'])) $page = $input['page'];

        $offset = $request->get('start');
        $limit = $request->get('length');
        $dataget->skip($offset)->take($limit);
        $data = $dataget->get();
        $data = $this->addSelectedToData($dataget->get(), $cc_sum_details, 2);
        
        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('approve_check', function ($row) {
                    if (isset($row->selected) && $row->selected == 1) {
                        $checkbox = '<input checked class="form-check-input newApproveCheckBox1" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    } else {
                        $checkbox = '<input class="form-check-input newApproveCheckBox1" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
                    }
                    return $checkbox;
                })
                ->rawColumns(['approve_check'])
                ->setTotalRecords($total)
                ->setFilteredRecords($total)
                ->skipPaging()
                ->make(true);
    }
    public function getWarehouseSvg(Request $request){
        $html = view('WarehouseManagment.'.$request->warehouse)->render();
        return $html;
    }
    
    private function addSelectedToData($data, $cc_sum_details, $type) {
        
        if (count($cc_sum_details) <= 0) {
            return $data;
        }
        
        $toReturn = [];
		foreach ($data as $datum) {
			if ($type === 1 && in_array($datum->ETIN, $cc_sum_details)) {
                $datum->selected = 1;
            } else if ($type == 2 && $cc_sum_details->contains('ETIN', $datum->ETIN) && $cc_sum_details->contains('address', $datum->address)) {
                $datum->selected = 1;
            } else {
                $datum->selected = 0;
            }
			array_push($toReturn, $datum);
		}
		return $toReturn;
    }

    public function getLocationApprovedProducts(Request $request) {

        $approved_products = CycleCountDetail::where('cycle_count_summary_id', $request->cc_sum_id)
                                ->join("master_product",'master_product.ETIN', "=", 'cycle__count__detail.ETIN')
                                ->get(['cycle__count__detail.*', 'master_product.product_listing_name']);

        if (!isset($approved_products) || count($approved_products) < 0) {
            return Datatables::of([])->addIndexColumn()->make(true);
        }

        $responses = [];
        foreach ($approved_products as $ap) {
            array_push($responses, [
                'ETIN' => $ap->ETIN,
                'product_desc' => $ap->product_listing_name,
                'address' => $ap->address,
                'cur_qty' => $ap->total_on_hand
            ]);
        }
        return Datatables::of($responses)->addIndexColumn()->make(true);
    }

    public function updateSummary(Request $request) {

        $params = $request->all();        
        if (!(isset($params['new_date']) || isset($params['cc_sum_id']))) {
            $data = [
                'error' => true,
                'msg' => "New Date and Summary Id cannot be blank."
            ];
            return response($data);
        }

        CycleCountSummary::where('id', $params['cc_sum_id'])->update(['scheduled_date' => $params['new_date']]);
        $data = [
            'error' => false,
            'msg' => "New Date updated successfully."
        ];
        return response($data);
    }

    public function deleteSummary($id){
        CycleCountSummary::destroy($id);
        return redirect()->route('suppliers.index')->with('success','Deleted successfully');
    }
    public function changeuser(Request $request)
    {
        $warehouses = WareHouse::where('warehouses',$request->w_id)->first();

        $users = User::whereIN('role',[1,2,3,4,5])->where('wh_id',$warehouses->id)->pluck('name','id');
        $data = [
            'error' => false,
            'users' => $users
        ];
        return response($data);
    }
}
