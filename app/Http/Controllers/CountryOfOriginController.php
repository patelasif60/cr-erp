<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CountryOfOrigin;
use App\Http\Requests\CountryOfOriginRequest;
use DataTables;
use App\MasterProduct;

class CountryOfOriginController extends Controller
{
    public function __construct(CountryOfOrigin $CountryOfOrigin){
        $this->CountryOfOrigin = $CountryOfOrigin;
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
        return view('country_of_origin.index');
    }

    public function CountryList(Request $request){
        $pro = $this->CountryOfOrigin->orderBy('country_of_origin','ASC')->get();
        
        return DataTables::of($pro)
        ->addColumn('command',function($pro){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('country.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
            //     $command.='<div class="col-xl-2"><form class="table_from" action="'.route("country.destroy",$pro['id']).'" method="POST">
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
        return view('country_of_origin.create');
    }

    public function store(CountryOfOriginRequest $request){
        $input = $request->all();
        $data = [
            'country_of_origin' => ProperInput($input['country_of_origin']),
        ];
        $result = CountryOfOrigin::create($data);
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
    //     $row = CountryOfOrigin::where('id',$id)->first()->toArray();
    //     return view('country_of_origin.edit',compact('row'));
    // }

    // public function update(CountryOfOriginRequest $request,$id){
    //     $input = $request->all();
    //     $data = [
    //         'country_of_origin' => $input['country_of_origin'],
    //     ];
    //     $result =  CountryOfOrigin::where('id',$id)->update($data);
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
    //     $CountryOfOrigin = $this->CountryOfOrigin::find($id);
    //     $pro_count = MasterProduct::where('country_of_origin','LIKE','%'.$CountryOfOrigin->country_of_origin.'%')->count();
    //     if($pro_count > 0){
    //         return redirect()->back()->with(['error'=>'This is used in Master Product, so we can not delete this']);
    //     }

    //     $result = $CountryOfOrigin->delete();
    //     if($result){
    //         return redirect()->back()->with(['success'=>'Successfully deleted']);
    //     }else{
    //         return redirect()->back()->with(['error'=>'Something went wrong']);
    //     }
    // }
}
