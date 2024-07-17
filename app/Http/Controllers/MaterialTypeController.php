<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Services\MaterialTypeService;

class MaterialTypeController extends Controller
{
	public function __construct(MaterialTypeService $service)
	{
        $this->service = $service;
	}
	public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.materialType.index');
    }
    public function store(Request $request){
        $input = $request->all();
        $data = [
            'material_type' => ProperInput($input['material_type']),
        ];
     	$materialType = $this->service->create($data);
     	if(!$materialType)
        {
           $data_info = [
                'msg' => 'Material type already exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }

    public function update(Request $request){  
        $materialType = $this->service->update($request->all());
        if(!$materialType)
        {
           $data_info = [
                'msg' => 'Material type already exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }
    public function materialTypeList(Request $request){
        $materialTypes = $this->service->getAll();
        return Datatables::of($materialTypes)->addColumn('action', function($materialType)
        {
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="openEditModal(\''.$materialType->id.'\',\''.$materialType->material_type.'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a><a href="javascript:void(0)" onclick="deleteMaterialType(\''.$materialType->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>
            ';
            return $btn;
            
        })->rawColumns(['action'])->make(true);
    }
    public function destroy(Request $request){
        return $this->service->destroy($request->id);   
    }

}