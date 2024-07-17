<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UnitSize;
use App\Http\Requests\UnitSizeRequest;
use DataTables;

class UnitSizeController extends Controller
{
    public function __construct(UnitSize $UnitSize){
        $this->UnitSize = $UnitSize;
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
        return view('unit_sizes.index');
    }

    public function UnitSizeList(Request $request){
        $pro = $this->UnitSize->orderBy('unit','ASC')->get();
        
        return DataTables::of($pro)
        // ->addColumn('command',function($pro){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('unit_sizes.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("unit_sizes.destroy",$pro['id']).'" method="POST">
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
        return view('unit_sizes.create');
    }

    public function store(UnitSizeRequest $request){
        $input = $request->all();
        $data = [
            'unit' => ProperInput($input['unit']),
            'abbreviation' => ProperInput($input['abbreviation'])
        ];
        $result = UnitSize::create($data);
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
        $row = UnitSize::where('id',$id)->first()->toArray();
        return view('unit_sizes.edit',compact('row'));
    }

    public function update(UnitSizeRequest $request,$id){
        $input = $request->all();
        $data = [
            'unit' =>  ProperInput($input['unit']),
            'abbreviation' =>  ProperInput($input['abbreviation'])
        ];
        $result =  UnitSize::where('id',$id)->update($data);
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
        $result = UnitSize::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
