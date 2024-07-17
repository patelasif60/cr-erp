<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageTypeStoreRequest;
use App\ImageType;
use DataTables;
use App\MasterProduct;
use App\MasterProductQueue;

class ImageTypeController extends Controller
{
    public function __construct(ImageType $ImageType){
        $this->ImageType = $ImageType;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.imageType.index');
    }

    public function imageTypeList(Request $request){
        $br = $this->ImageType->orderBy('image_type','ASC')->get();
        
        return DataTables::of($br)
        ->addColumn('command',function($br){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('image_type.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
            //     $command.='<div class="col-xl-2"><form class="table_from" action="'.route("image_type.destroy",$br['id']).'" method="POST">
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
        return view('cranium.imageType.create');
    }

    public function store(ImageTypeStoreRequest $request){
        $input = $request->all();
        $data = [
            'image_type' => ProperInput($input['image_type']),
        ];
        $result = $this->ImageType::create($data);
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
        $row = $this->ImageType::where('id',$id)->first()->toArray();
        return view('cranium.imageType.edit',compact('row'));
    }

    public function update(ImageTypeStoreRequest $request,$id){
        $input = $request->all();
        $data = [
            'image_type' =>  ProperInput($input['image_type']),
        ];
        $result =  $this->ImageType::where('id',$id)->update($data);
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
        $result = $this->ImageType::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
