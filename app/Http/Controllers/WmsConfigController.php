<?php

namespace App\Http\Controllers;

use App\Carrier;
use Illuminate\Http\Request;
use DB;
use DataTables;
use App\ProductTemperature;
use App\ProcessingGroups;
use App\PickerConfigration;
use App\User;
use App\PickPackMethod;
use App\Client;
use App\WareHouse;
use App\ShippingWarehouseEligibilities;
use App\OrderDayShippingEligibility;

class WmsConfigController extends Controller
{
    public function index(){
        $warehouses = WareHouse::all();
        $carriers = Carrier::all();
        return view('cranium.wmsconfig.wmsconfig',compact('warehouses', 'carriers'));
    }
    public function update_processing_group(Request $request,$id){        
        $input = $request->all();
        $name = $request->group_name;
        $data = ProcessingGroups::where('id',$id)->update([
            'group_name' =>$name,
            'group_details' => implode(',',$request->group_details)
        ]);
         if($data)
        {
           $data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }
    public function getOrderProcessdata(Request $request)
    {
        $dataget = DB::table('processing_groups')->get();
        //dd($dataget);
        return Datatables::of($dataget)->addColumn('action', function($data)
        {
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="GetModelWMSConfig(\''.route('edit_processing_group',$data->id).'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a>
            ';
            return $btn;
            
        })
        ->addColumn('group_details', function($data){
            $array = explode(',',$data->group_details);
            $proName = ProductTemperature::whereIn('id',$array)->pluck('product_temperature')->toArray();
                    return implode(',',$proName);
        })
        ->rawColumns(['action'])->make(true);
    }

    public function getpickPackData(Request $request){
        $dataget = DB::table('pick_pack_method')->leftJoin('clients',function($join){
            $join->on('clients.id','=','pick_pack_method.client_id');
        })->select('pick_pack_method.*',DB::raw('CASE WHEN clients.company_name IS NULL THEN "DEFAULT" ELSE clients.company_name END as client_name'))->get();
        return Datatables::of($dataget)->addColumn('action', function($data)
        {
            if($data->client_id >0){
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="GetModelWMSConfig(\''.route('edit_pick_pack_method',$data->id).'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a>
            ';

            $btn .= '<a  href="javascript:void(0);" onClick="DeletePickPackMethod(\''.route('delete_pick_pack_method',$data->id).'\')" class="btn btn-danger"><i class="nav-icon i-Close-Window "></i></a>
            ';

            
            return $btn;
            }
        })->rawColumns(['action'])->make(true);
    }

    public function edit_processing_group($id){
        $get_all_added_temp = ProcessingGroups::where('id','!=',$id)->get();
        $all_temp = [];
        foreach($get_all_added_temp as $row_temp){
            if($row_temp->group_details != ''){
                foreach(explode(',',$row_temp->group_details) as $temp){
                    $all_temp[] = $temp;
                }
            }
        }
        $row = ProcessingGroups::find($id);
        $temp = ProductTemperature::whereNotIn('id',$all_temp)->get()->pluck('product_temperature','id')->toArray();
        return view('cranium.wmsconfig.edit_order_processing_group',compact('row','temp'));
    }

    public function getPickerConf(Request $request){
        //dd($request['werid']);
        $dataget = User::leftJoin('picker_configrations', 
                function ($join) { 
                    $join->on('users.id', '=', 'picker_configrations.user_id');
                })->select(['*','users.id as uid'])->whereIn('role',[4, 5])->where('wh_id',$request['werid'])->get();
        return Datatables::of($dataget)->addColumn('action', function($data)
        {
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="GetModelpicker(\''.route('edit_picker_conf',$data->uid).'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a>
            ';
            return $btn;
            
        })->addColumn('orderProcessname', function($data){
                $array = explode(',',$data->order_processing_id);
                $proName = ProcessingGroups::whereIn('id',$array)->pluck('group_name')->toArray();
                        return implode(',',$proName);
            })->rawColumns(['action'])->make(true);
    }

    public function editPickerConf($id){
        $data = PickerConfigration::where('user_id',$id)->first();
        
        $data ? $orderProcessing  = explode(',',$data->order_processing_id) : $orderProcessing = [];
        
        $orderProcess = ProcessingGroups::all();

        return view('cranium.wmsconfig.edit_picker_conf',compact('data','orderProcessing','orderProcess','id'));   
    }

    public function updatePickerConf(Request $request,$id){
        $input = $request->all();
        //dd($request->all());
        $user = PickerConfigration::where('user_id',$id)->first();
        if($user)
        {
            $user->update([
                'order_processing_id' => implode(',',$request->order_processing_ids),
                'batch_max_until_2pm' =>$request->batch_max_until_2pm,
                'batch_max_2pm_to_4pm' =>$request->batch_max_2pm_to_4pm,
                'batch_max_after_4pm' =>$request->batch_max_after_4pm,
            ]);
        }
        else{
            PickerConfigration::create([
                'order_processing_id' => implode(',',$request->order_processing_ids),
                'batch_max_until_2pm' =>$request->batch_max_until_2pm,
                'batch_max_2pm_to_4pm' =>$request->batch_max_2pm_to_4pm,
                'batch_max_after_4pm' =>$request->batch_max_after_4pm,
                'user_id' => $id,
            ]);
        } 
        
       $data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
        return response()->json($data_info);
    }

    public function add_pick_pack_method(){
        $client = Client::get()->pluck('company_name','id')->toArray();
        $row = PickPackMethod::where('client_id',0)->first();
        return view('cranium.wmsconfig.add_pick_pack_method',compact('client','row'));
    }

    public function store_pick_pack_method(Request $request){
        $PPM = new PickPackMethod();
        $PPM->client_id = $request->client_id;
        $PPM->frozen_pick = $request->frozen_pick;
        $PPM->frozen_pack = $request->frozen_pack;
        $PPM->dry_pick = $request->dry_pick;
        $PPM->dry_pack = $request->dry_pack;
        $PPM->refrigerated_pick = $request->refrigerated_pick;
        $PPM->refrigerated_pack = $request->refrigerated_pack;
        $PPM->save();

        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function edit_pick_pack_method($id){
        $client = Client::get()->pluck('company_name','id')->toArray();
        $row = PickPackMethod::find($id);
        return view('cranium.wmsconfig.edit_pick_pack_method',compact('client','row'));
    }

    public function update_pick_pack_method(Request $request,$id){
        $PPM = PickPackMethod::find($id);
        $PPM->client_id = $request->client_id;
        $PPM->frozen_pick = $request->frozen_pick;
        $PPM->frozen_pack = $request->frozen_pack;
        $PPM->dry_pick = $request->dry_pick;
        $PPM->dry_pack = $request->dry_pack;
        $PPM->refrigerated_pick = $request->refrigerated_pick;
        $PPM->refrigerated_pack = $request->refrigerated_pack;
        $PPM->save();

        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function shipping_eligibility(Request $request){
        //dd($request->all());
        $result = OrderDayShippingEligibility::all()->pluck('description','id')->toArray();
        $shippingEligibility =  ShippingWarehouseEligibilities::where('warehouse_id',$request->warehouse)->get();
        return view('cranium.wmsconfig.shipping_detail',compact('shippingEligibility','result'));
    }
    public function updateShippingEligiblity(Request $request){
        
        $monday = array_filter(array_column($request->shipp,'monday'));
        $tuesday = array_filter(array_column($request->shipp,'tuesday'));
        $wednesday = array_filter(array_column($request->shipp,'wednesday'));
        $thursday = array_filter(array_column($request->shipp,'thursday'));
        $friday = array_filter(array_column($request->shipp,'friday'));

        if(count($monday) != count(array_unique($monday))){
            return response()->json([
                'error' => 1,
                'msg' => 'In monday column duplicate value taken'
            ]);
        }
        if(count($tuesday) != count(array_unique($tuesday))){
            return response()->json([
                'error' => 1,
                'msg' => 'In tuesday column duplicate value taken'
            ]);
        }
        if(count($wednesday) != count(array_unique($wednesday))){
            return response()->json([
                'error' => 1,
                'msg' => 'In wednesday column duplicate value taken'
            ]);
        }
        if(count($thursday) != count(array_unique($thursday))){
            return response()->json([
                'error' => 1,
                'msg' => 'In thursday column duplicate value taken'
            ]);
        }
        if(count($friday) != count(array_unique($friday))){
            return response()->json([
                'error' => 1,
                'msg' => 'In friday column duplicate value taken'
            ]);
        }

        foreach($request->shipp as $key=>$val){
            ShippingWarehouseEligibilities::find($key)->update([
                'monday'=>$val['monday'],
                'tuesday'=>$val['tuesday'],
                'wednesday'=>$val['wednesday'],
                'thursday'=>$val['thursday'],
                'friday'=>$val['friday'],
            ]);
        }
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function delete_pick_pack_method($id){
        $PPM = PickPackMethod::find($id);  
        $PPM->delete();

        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);      
    }

    public function getSupplierData(Request $request){
        $dataget = DB::table('suppliers')->select('id','name','exp_lot')->get();
        return Datatables::of($dataget)->addColumn('action', function($data)
        {
            $btn = '';
            $btn .= '<select class="form-control" onChange="UpdateExpStatus(this,\''.route('update_supplier_exp_lot',$data->id).'\')">
                <option value="NO" '.($data->exp_lot == 'NO' ? 'selected':'').'>NO</option>
                <option value="YES" '.($data->exp_lot == 'YES' ? 'selected':'').'>YES</option>
            </select>';

            
            return $btn;
            
        })->rawColumns(['action'])->make(true);
    }

    public function getClientData(Request $request){
        $dataget = DB::table('clients')->select('id','company_name','exp_lot')->where('business_relationship', 'Fulfillment')->get();
        return Datatables::of($dataget)->addColumn('action', function($data)
        {
            $btn = '';
            $btn .= '<select class="form-control" onChange="UpdateClientExpStatus(this,\''.route('update_client_exp_lot',$data->id).'\')">
                <option value="NO" '.($data->exp_lot == 'NO' ? 'selected':'').'>NO</option>
                <option value="YES" '.($data->exp_lot == 'YES' ? 'selected':'').'>YES</option>
            </select>';

            
            return $btn;
            
        })->rawColumns(['action'])->make(true);
    }

    public function update_supplier_exp_lot(Request $request,$id){
        DB::table('suppliers')->where('id',$id)->update([
            'exp_lot' => $request->status
        ]);
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }

    public function update_client_exp_lot(Request $request,$id){
        DB::table('clients')->where('id',$id)->update([
            'exp_lot' => $request->status
        ]);
        return response()->json([
            'error' => 0,
            'msg' => 'Success'
        ]);
    }


}
