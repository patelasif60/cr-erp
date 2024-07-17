<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\LocationType;

class LocationTypeController extends Controller
{
	public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.locationtype.index');
    }
    public function store(Request $request){
        $input = $request->all();
        $data = [
            'type' => ProperInput($input['type']),
        ];
        $count = LocationType::where('type',$data['type'])->count();
        if($count > 0)
        {
             $data_info = [
                'msg' => 'type already exists',
                'error' => 1
            ];
            return response()->json($data_info);
        }
        $locationType =  LocationType::create($data);
     	$data_info = [
            'msg' => 'Success',
            'error' => 0
        ];
        
        return response()->json($data_info);
    }

    public function update(Request $request){  
        $locationType = $this->updateRepo($request->all());
        if(!$locationType)
        {
           $data_info = [
                'msg' => 'locationType already exists',
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
    public function locationtypesList(Request $request){
        $locationTypes = LocationType::orderBy('id','ASC')->get();
        return Datatables::of($locationTypes)->addColumn('action', function($locationType)
        {
            $btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="openEditModal(\''.$locationType->id.'\',\''.$locationType->type.'\')" class="btn btn-primary"><i class="nav-icon i-Pen-2 "></i></a><a href="javascript:void(0)" onclick="deleteLocationType(\''.$locationType->id.'\')" class="btn btn-danger ml-1"><i class="nav-icon i-Close-Window"></i> </a>
            ';
            return $btn;
            
        })->rawColumns(['action'])->make(true);
    }
    public function destroy(Request $request){
        return LocationType::destroy($request->id);;   
    }
     public function updateRepo($data)
    {
        $count = LocationType::where('type',$data['type'])->where('id','!=',$data['id'])->count();
        if($count > 0)
        {
            return false;
        }
        $locationType = LocationType::find($data['id']);
        $locationType->fill($data);
        $locationType->save();
        return $locationType;
    }

}
