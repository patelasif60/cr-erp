<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SmartFilterStoreRequest;
use App\SmartFilter;
use Auth;

class SmartFiltersController extends Controller
{
    public function __construct(SmartFilter $SmartFilter){
        $this->SmartFilter = $SmartFilter;
    }
    public function store(SmartFilterStoreRequest $request){

        $visible_filters = '';
        $visible_columns = '';
        $filter_preferences = '';
        $main_filter = '';
        $id = 0;

        if($request->id != NULL){
            $id = $request->id;
        }

        if(isset($request->filters) && !empty($request->filters)){
            $visible_filters = implode(',',$request->filters);
        }
        if(isset($request->columns) && !empty($request->columns)){
            $visible_columns = implode(',',$request->columns);
        }
        if(isset($request->filter_val) && !empty($request->filter_val)){
            $filter_preferences = json_encode($request->filter_val);
        }

        if(isset($request->main_filter) && !empty($request->main_filter)){
            $main_filter = json_encode($request->main_filter);
        }
        

        
        $SmartFilter = new SmartFilter;
        $SmartFilter->filter_name = $request->filter_name;
        $SmartFilter->visible_filters = $visible_filters;
        $SmartFilter->visible_columns = $visible_columns;
        $SmartFilter->filter_preferences = $filter_preferences;
        $SmartFilter->main_filter = $main_filter;
        $SmartFilter->type = $request->type;
        $SmartFilter->created_by = Auth::user()->id;
        if(isset($request->column_orders)){
            $SmartFilter->column_orders = $request->column_orders;
        }
        $SmartFilter->save();


        if($SmartFilter){
            $url = url('/masterparoducts_approved').'/'.$SmartFilter->id;
            if($request->type === 'order'){
                $url = route('orders.index',$SmartFilter->id);
            }
            $return_data = [
                'error' => false,
                'msg' => 'Success',
                'url' => $url
            ];
        }else{
            $return_data = [
                'error' => true,
                'msg' => 'Something Went Wrong'
            ];
        }

        return response()->json($return_data);
    }

    public function update_smart_filter(Request $request){
        $visible_filters = '';
        $visible_columns = '';
        $filter_preferences = '';
        $main_filter = '';
        $id = 0;

        if($request->id != NULL){
            $id = $request->id;
        }

        if(isset($request->filters) && !empty($request->filters)){
            $visible_filters = implode(',',$request->filters);
        }
        if(isset($request->columns) && !empty($request->columns)){
            $visible_columns = implode(',',$request->columns);
        }
        if(isset($request->filter_val) && !empty($request->filter_val)){
            $filter_preferences = json_encode($request->filter_val);
        }

        if(isset($request->main_filter) && !empty($request->main_filter)){
            $main_filter = json_encode($request->main_filter);
        }
        

        
        $SmartFilter = $this->SmartFilter::find($id);
        $SmartFilter->visible_filters = $visible_filters;
        $SmartFilter->visible_columns = $visible_columns;
        $SmartFilter->filter_preferences = $filter_preferences;
        $SmartFilter->main_filter = $main_filter;
        $SmartFilter->type = $request->type;
        if(isset($request->column_orders)){
            $SmartFilter->column_orders = $request->column_orders;
        }
        $update = $SmartFilter->save();
        // $update = $this->SmartFilter::where('id',$id)->update([
        //     'visible_filters' => $visible_filters,
        //     'visible_columns' => $visible_columns,
        //     'filter_preferences' => json_encode($filter_preferences),
        //     'main_filter' => $main_filter,
        // ]);

        if($update){
            $return_data = [
                'error' => false,
                'msg' => 'Success'
            ];
        }else{
            $return_data = [
                'error' => true,
                'msg' => 'Something Went Wrong'
            ];
        }

        return response()->json($return_data);
    }
}
