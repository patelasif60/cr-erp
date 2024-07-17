<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Manufacturer;
use App\Http\Requests\ManufacturerStoreRequest;
use DataTables;
use App\MasterProduct;
use App\MasterProductQueue;

class ManufacturerController extends Controller
{
    public function __construct(Manufacturer $Manufacturer){
        $this->Manufacturer = $Manufacturer;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.manufacturer.index');
    }

    public function manufacturerList(Request $request){
        $sup = $this->Manufacturer->orderBy('manufacturer_name','ASC')->get();
        return DataTables::of($sup)
        // ->addColumn('command',function($sup){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('manufacturer.edit',$sup['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("manufacturer.destroy",$sup['id']).'" method="POST">
        //         '.method_field('DELETE').'
        //         '.csrf_field().'
        //     </form></div></div>';
            
        //     return $command;
        // })
        // ->rawColumns(['command'])
        ->make(true);
    }

    public function create(){
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.manufacturer.create');
    }

    public function store(ManufacturerStoreRequest $request){
        $input = $request->all();
        
        $data = [
            'manufacturer_name' => ProperInput($input['manufacturer_name']),
        ];
        $result = Manufacturer::create($data);
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
        $row = $this->Manufacturer::where('id',$id)->first()->toArray();
        return view('cranium.manufacturer.edit',compact('row'));
    }

    public function update(ManufacturerStoreRequest $request,$id){
        $input = $request->all();

        // $existing_data = Manufacturer::find($id);
        // $master_products = MasterProduct::where('manufacturer', $existing_data->manufacturer_name)->update(['manufacturer' => $input['manufacturer_name']]);
        // $master_queue_products = MasterProductQueue::where('manufacturer', $existing_data->manufacturer_name)->update(['manufacturer' => $input['manufacturer_name']]);

        $data = [
            'manufacturer_name' =>  ProperInput($input['manufacturer_name']),
        ];
        $result =  $this->Manufacturer::where('id',$id)->update($data);
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
        $result = $this->Manufacturer::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
