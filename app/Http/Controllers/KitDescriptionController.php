<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\KitDescriptionStoreRequest;
use App\KitDescription;
use App\MasterProduct;
use App\MasterProductQueue;
use DataTables;

class KitDescriptionController extends Controller
{
    public function __construct(KitDescription $KitDescription){
        $this->KitDescription = $KitDescription;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        
        return view('cranium.kit_description.index');
    }

    public function KitDescriptionList(Request $request){
        $br = $this->KitDescription->orderBy('kit_description','ASC')->get();
        
        return DataTables::of($br)
        // ->addColumn('command',function($br){
        //     $command = '';
        //         $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('kit_description.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
        //         $command.='<div class="col-xl-2"><form class="table_from" action="'.route("kit_description.destroy",$br['id']).'" method="POST">
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
        return view('cranium.kit_description.create');
    }

    public function store(KitDescriptionStoreRequest $request){
        $input = $request->all();
        $data = [
            'kit_description' => $input['kit_description'],
        ];
        $result = $this->KitDescription::create($data);
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
        $row = $this->KitDescription::where('id',$id)->first()->toArray();
        return view('cranium.kit_description.edit',compact('row'));
    }

    public function update(KitDescriptionStoreRequest $request,$id){
        $input = $request->all();
       
        $data = [
            'kit_description' =>  ProperInput($input['kit_description']),
        ];
        $result =  $this->KitDescription::where('id',$id)->update($data);
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
        $kit_description = $this->KitDescription::find($id);
        $pro_count = MasterProduct::where('kit_description','LIKE','%'.$kit_description->kit_description.'%')->count();
        if($pro_count > 0){
            return redirect()->back()->with(['error'=>'This is used in Master Product, so we can not delete this']);
        }
        $result = $kit_description->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
