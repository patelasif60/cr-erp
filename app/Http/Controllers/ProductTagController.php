<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductTags;
use App\Http\Requests\ProductTagsRequest;
use DataTables;

class ProductTagController extends Controller
{
    public function __construct(ProductTags $ProductTags){
        $this->ProductTags = $ProductTags;
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
        return view('product_tags.index');
    }

    public function ProductTagsList(Request $request){
        $pro = $this->ProductTags->orderBy('tag','ASC')->get()->toArray();
        
        return DataTables::of($pro)
        ->addColumn('command',function($pro){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('product_tags.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
                $command.='</div>';
            
            return $command;
        })
        ->addColumn('status',function($pro){
            $internal_external = $pro['internal_external_flag'];
           if($internal_external == 0){
                $internal_external = 'External';
           }else{
            $internal_external = 'Internal';
           }
            return $internal_external;
        })
        ->rawColumns(['command','status'])
        ->make(true);
    }

    public function create(){
        if(ReadWriteAccess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('product_tags.create');
    }

    public function store(ProductTagsRequest $request){
        $input = $request->all();
        $data = [
            'tag' => ProperInput($input['tag']),
            'internal_external_flag' => $input['internal_external_flag']
        ];
        $result = ProductTags::create($data);
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
        $row = ProductTags::where('id',$id)->first()->toArray();
        return view('product_tags.edit',compact('row'));
    }

    public function update(ProductTagsRequest $request,$id){
        $input = $request->all();
        $data = [
            'tag' =>  ProperInput($input['tag']),
            'internal_external_flag' => $input['internal_external_flag']
        ];
        $result =  ProductTags::where('id',$id)->update($data);
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
        $result = ProductTags::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
