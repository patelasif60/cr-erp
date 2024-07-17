<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UnitDescription;
use App\Http\Requests\UnitDescriptionRequest;
use DataTables;

class UnitDescriptionController extends Controller
{
    public function __construct(UnitDescription $UnitDescription){
        $this->UnitDescription = $UnitDescription;
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
        return view('unit_description.index');
    }

    public function UnitDescriptionList(Request $request){
        $pro = $this->UnitDescription->orderBy('unit_description','ASC')->get();
        
        return DataTables::of($pro)
        // ->addColumn('command',function($pro){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('unit_description.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("unit_description.destroy",$pro['id']).'" method="POST">
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
        return view('unit_description.create');
    }

    public function store(UnitDescriptionRequest $request){
        $input = $request->all();
        $data = [
            'unit_description' => ProperInput($input['unit_description']),
        ];
        $result = UnitDescription::create($data);
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
        $row = UnitDescription::where('id',$id)->first()->toArray();
        return view('unit_description.edit',compact('row'));
    }

    public function update(UnitDescriptionRequest $request,$id){
        $input = $request->all();
        $data = [
            'unit_description' =>  ProperInput($input['unit_description']),
        ];
        $result =  UnitDescription::where('id',$id)->update($data);
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
        $result = UnitDescription::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
