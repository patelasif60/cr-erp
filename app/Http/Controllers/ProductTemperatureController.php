<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductTemperature;
use App\Http\Requests\ProductTemperatureRequest;
use DataTables;

class ProductTemperatureController extends Controller
{
    public function __construct(ProductTemperature $ProductTemperature){
        $this->ProductTemperature = $ProductTemperature;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('product_temperature.index');
    }

    public function ProductTemperatureList(Request $request){
        $pro = $this->ProductTemperature->get();
        
        return DataTables::of($pro)
        // ->addColumn('command',function($pro){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('product_temperature.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='</div>';
            
        //     return $command;
        // })
        // ->rawColumns(['command'])
        ->make(true);
    }

    public function create(){
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('product_temperature.create');
    }

    public function store(ProductTemperatureRequest $request){
        $input = $request->all();
        $data = [
            'product_temperature' => ProperInput($input['product_temperature']),
        ];
        $result = ProductTemperature::create($data);
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
        $row = $this->ProductTemperature::where('id',$id)->first()->toArray();
        return view('product_temperature.edit',compact('row'));
    }

    public function update(ProductTemperatureRequest $request,$id){
        $input = $request->all();
        $data = [
            'product_temperature' =>  ProperInput($input['product_temperature']),
        ];
        $result =  $this->ProductTemperature::where('id',$id)->update($data);
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
        $result = $this->ProductTemperature::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
