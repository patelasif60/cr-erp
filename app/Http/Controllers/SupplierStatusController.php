<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\SupplierStatus;
use App\Http\Requests\SupplierStatusStoreRequest;


class SupplierStatusController extends Controller
{
    public function __construct(SupplierStatus $SupplierStatus){
        $this->SupplierStatus = $SupplierStatus;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierStatus.index');
    }

    public function supplierStatusList(Request $request){
        $sup = $this->SupplierStatus->orderBy('supplier_status','ASC')->get();
        return DataTables::of($sup)
        ->addColumn('command',function($sup){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('supplier_status.edit',$sup['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
                $command.='</div>';

            return $command;
        })
        ->rawColumns(['command'])
        ->make(true);
    }

    public function create(){
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.supplierStatus.create');
    }

    public function store(SupplierStatusStoreRequest $request){
        $input = $request->all();

        $data = [
            'supplier_status' => ProperInput($input['supplier_status']),
        ];
        $result = $this->SupplierStatus::create($data);
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
        $row = $this->SupplierStatus::where('id',$id)->first()->toArray();
        return view('cranium.supplierStatus.edit',compact('row'));
    }

    public function update(SupplierStatusStoreRequest $request,$id){
        $input = $request->all();
        $data = [
            'supplier_status' =>  ProperInput($input['supplier_status']),
        ];
        $result =  $this->SupplierStatus::where('id',$id)->update($data);
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
        $result = $this->SupplierStatus::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
