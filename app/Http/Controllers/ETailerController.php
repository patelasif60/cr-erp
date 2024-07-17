<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ETailerStoreRequest;
use App\ETailer_availability;
use DataTables;
use App\MasterProduct;

class ETailerController extends Controller
{
    public function __construct(ETailer_availability $ETailer_availability){
        $this->ETailer_availability = $ETailer_availability;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.eTailer.index');
    }

    public function etailerList(Request $request){
        $br = $this->ETailer_availability->orderBy('etailer_availability','ASC')->get();
        
        return DataTables::of($br)
        ->addColumn('command',function($br){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('etailer_availability.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
            //     $command.='<div class="col-xl-2"><form class="table_from" action="'.route("etailer_availability.destroy",$br['id']).'" method="POST">
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
        return view('cranium.eTailer.create');
    }

    public function store(ETailerStoreRequest $request){
        $input = $request->all();
        $data = [
            'etailer_availability' => ProperInput($input['etailer_availability']),
        ];
        $result = $this->ETailer_availability::create($data);
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
    //     $row = $this->ETailer_availability::where('id',$id)->first()->toArray();
    //     return view('cranium.eTailer.edit',compact('row'));
    // }

    // public function update(ETailerStoreRequest $request,$id){
    //     $input = $request->all();
    //     $data = [
    //         'etailer_availability' => $input['etailer_availability'],
    //     ];
    //     $result =  $this->ETailer_availability::where('id',$id)->update($data);
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
    //     $ETailer_availability = $this->ETailer_availability::find($id);
    //     $pro_count = MasterProduct::where('etailer_availability','LIKE','%'.$ETailer_availability->etailer_availability.'%')->count();
    //     if($pro_count > 0){
    //         return redirect()->back()->with(['error'=>'This is used in Master Product, so we can not delete this']);
    //     }

    //     $result = $ETailer_availability->delete();

    //     $result = $this->ETailer_availability::where('id',$id)->delete();
    //     if($result){
    //         return redirect()->back()->with(['success'=>'Successfully deleted']);
    //     }else{
    //         return redirect()->back()->with(['error'=>'Something went wrong']);
    //     }
    // }
}
