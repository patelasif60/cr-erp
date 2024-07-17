<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductStatus;
use App\Http\Requests\ProductStatusRequest;
use DataTables;

class ProductStatusController extends Controller
{
    public function __construct(ProductStatus $ProductStatus){
        $this->ProductStatus = $ProductStatus;
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
        return view('product_status.index');
    }

    public function productstatusList(Request $request){
        $pro = $this->ProductStatus->orderBy('product_status','ASC')->get();
        
        return DataTables::of($pro)
        // ->addColumn('command',function($pro){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('product_statuses.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
                
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("product_statuses.destroy",$pro['id']).'" method="POST">
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
        return view('product_status.create');
    }

    public function store(ProductStatusRequest $request){
        $input = $request->all();
        $data = [
            'product_status' => ProperInput($input['product_status'])
        ];
        $result = ProductStatus::create($data);
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
        $row = ProductStatus::where('id',$id)->first()->toArray();
        return view('product_status.edit',compact('row'));
    }

    public function update(ProductStatusRequest $request,$id){
        $input = $request->all();
        $data = [
            'product_status' =>  ProperInput($input['product_status'])
        ];
        $result =  ProductStatus::where('id',$id)->update($data);
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
        $result = ProductStatus::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
