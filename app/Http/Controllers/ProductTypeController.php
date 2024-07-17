<?php

namespace App\Http\Controllers;
use App\Http\Requests\ProductTypeStoreRequest;
use App\ProductType;
use DataTables;

use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function __construct(ProductType $ProductType){
        $this->ProductType = $ProductType;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.productType.index');
    }

    public function productTypeList(Request $request){
        $sup = $this->ProductType->orderBy('product_type','ASC')->get();
        return DataTables::of($sup)
        // ->addColumn('command',function($sup){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('product_type.edit',$sup['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
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
        return view('cranium.productType.create');
    }

    public function store(ProductTypeStoreRequest $request){
        $input = $request->all();
        
        $data = [
            'product_type' => ProperInput($input['product_type']),
        ];
        $result = $this->ProductType::create($data);
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
        $row = $this->ProductType::where('id',$id)->first()->toArray();
        return view('cranium.productType.edit',compact('row'));
    }

    public function update(ProductTypeStoreRequest $request,$id){
        $input = $request->all();
        $data = [
            'product_type' =>  ProperInput($input['product_type']),
        ];
        $result =  $this->ProductType::where('id',$id)->update($data);
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
        $result = $this->ProductType::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
