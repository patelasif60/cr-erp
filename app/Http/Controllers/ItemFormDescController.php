<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ItemFormDescStoreRequest;
use App\ItemFormDescription;
use DataTables;

class ItemFormDescController extends Controller
{
    public function __construct(ItemFormDescription $ItemFormDescription){
        $this->ItemFormDescription = $ItemFormDescription;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.itemFormDesc.index');
    }

    public function itemFormDescList(Request $request){
        $br = $this->ItemFormDescription->orderBy('item_desc','ASC')->get();
        
        return DataTables::of($br)
        // ->addColumn('command',function($br){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('item_form_description.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("item_form_description.destroy",$br['id']).'" method="POST">
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
        return view('cranium.itemFormDesc.create');
    }

    public function store(ItemFormDescStoreRequest $request){
        $input = $request->all();
        $data = [
            'item_desc' => ProperInput($input['item_desc']),
        ];
        $result = $this->ItemFormDescription::create($data);
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

    // public function edit($id){
    //     $row = $this->ItemFormDescription::where('id',$id)->first()->toArray();
    //     return view('cranium.itemFormDesc.edit',compact('row'));
    // }

    // public function update(ItemFormDescStoreRequest $request,$id){
    //     $input = $request->all();
    //     $data = [
    //         'item_desc' => $input['item_desc'],
    //     ];
    //     $result =  $this->ItemFormDescription::where('id',$id)->update($data);
    //     if($result){
    //         $data_info = [
    //             'msg' => 'Success',
    //             'error' => 0
    //         ];
    //     }else{
    //         $data_info = [
    //             'msg' => 'Something wend wrong',
    //             'error' => 1
    //         ];
    //     }
    //     return response()->json($data_info);
    // }

    // public function destroy($id)
    // {
    //     $result = $this->ItemFormDescription::where('id',$id)->delete();
    //     if($result){
    //         return redirect()->back()->with(['success'=>'Successfully deleted']);
    //     }else{
    //         return redirect()->back()->with(['error'=>'Something went wrong']);
    //     }
    // }
}
