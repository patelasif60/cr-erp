<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AllergensStoreRequest;
use App\Allergens;
use App\MasterProduct;
use App\MasterProductQueue;
use DataTables;



class AllergensController extends Controller
{
    public function __construct(Allergens $Allergens){
        $this->Allergens = $Allergens;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.allergens.index');
    }

    public function allergensList(Request $request){
        $br = $this->Allergens->orderBy('allergens','ASC')->get();
        
        return DataTables::of($br)
        ->addColumn('command',function($br){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('allergens.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
            //     $command.='<div class="col-xl-2"><form class="table_from" action="'.route("allergens.destroy",$br['id']).'" method="POST">
            //     '.method_field('DELETE').'
            //     '.csrf_field().'
            // </form></div>';
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
        return view('cranium.allergens.create');
    }

    public function store(AllergensStoreRequest $request){
        $input = $request->all();
        $data = [
            'allergens' => ProperInput($input['allergens']),
        ];
        $result = $this->Allergens::create($data);
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
    //     $row = $this->Allergens::where('id',$id)->first()->toArray();
    //     return view('cranium.allergens.edit',compact('row'));
    // }

    // public function update(AllergensStoreRequest $request,$id){
    //     $input = $request->all();
    //     // $existing_data = Allergens::find($id);
    //     // $master_products = MasterProduct::where('allergens', $existing_data->allergens)->update(['allergens' => $input['allergens']]);
    //     // $master_queue_products = MasterProductQueue::where('allergens', $existing_data->allergens)->update(['allergens' => $input['allergens']]);
        
    //     $data = [
    //         'allergens' => $input['allergens'],
    //     ];
    //     $result =  $this->Allergens::where('id',$id)->update($data);
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
    //     $allergens = $this->Allergens::find($id);
    //     $pro_count = MasterProduct::where('allergens','LIKE','%'.$allergens->allergens.'%')->count();
    //     if($pro_count > 0){
    //         return redirect()->back()->with(['error'=>'This is used in Master Product, so we can not delete this']);
    //     }
    //     $result = $allergens->delete();
    //     if($result){
    //         return redirect()->back()->with(['success'=>'Successfully deleted']);
    //     }else{
    //         return redirect()->back()->with(['error'=>'Something went wrong']);
    //     }
    // }
}
