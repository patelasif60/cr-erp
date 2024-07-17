<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductListingFilter;
use DataTables;
use DB;

class ProductListingFilterController extends Controller
{
    public function __construct(ProductListingFilter $ProductListingFilter){
        $this->ProductListingFilter = $ProductListingFilter;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = 'product')
    {
        $result = $this->ProductListingFilter::where('type',$type)->orderBy('sorting_order','ASC')->get();
        return view('product_listing_filters.index',compact('result','type'));
    }


    public function create($type = 'product'){
        return view('product_listing_filters.create',compact('type'));
    }

    public function store(Request $request){
        $PLF = new ProductListingFilter();
        $PLF->label_name = $request->label_name;
        $PLF->column_name = $request->column_name;
        $PLF->text_or_select = $request->text_or_select;
        $PLF->select_table = $request->select_table;
        $PLF->select_value_column = $request->select_value_column;
        $PLF->select_label_column = $request->select_label_column;
        // $PLF->row_conditions = $request->row_conditions;
        $PLF->is_default = $request->is_default;
        $PLF->custom_select_options = $request->custom_select_options;
        $PLF->type = $request->type;
        $result = $PLF->save();
        if($result){
            $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }else{
            $data_info = [
                'msg' => 'Something wend wrong',
                'error' => 1
            ];
        }
        return response()->json($data_info);
    }

    public function edit($id){
        $row = ProductListingFilter::find($id);
        return view('product_listing_filters.edit',compact('row'));
    }

    public function update(Request $request,$id){
        $PLF = ProductListingFilter::find($id);
        $PLF->label_name = $request->label_name;
        $PLF->column_name = $request->column_name;
        $PLF->text_or_select = $request->text_or_select;
        $PLF->select_table = $request->select_table;
        $PLF->select_value_column = $request->select_value_column;
        $PLF->select_label_column = $request->select_label_column;
        $PLF->is_default = $request->is_default;
        $PLF->custom_select_options = $request->custom_select_options;
        // $PLF->row_conditions = $request->row_conditions;
        $result = $PLF->save();
        if($result){
            $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }else{
            $data_info = [
                'msg' => 'Something wend wrong',
                'error' => 1
            ];
        }
        return response()->json($data_info);
    }

    public function destroy($id)
    {
        $result = ProductListingFilter::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }

    public function save_listing_order(Request $request){
        $roles_permissions_menus = DB::table('product_listing_filters')->where('type',$request->type)->orderBy('sorting_order')->get();
        $order = explode(",",$request->order);
        if(!empty($order)){
            foreach($roles_permissions_menus as $row){
                $key = array_search($row->id,$order);
                DB::table('product_listing_filters')->where('id',$row->id)->update(['sorting_order'=>$key]);
            }
        }
        return response()->json(['msg'=>"Table Sorted"]);
    }

        
}

