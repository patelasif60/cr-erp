<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TimeZone;
use App\Http\Requests\TimeZoneRequest;
use DataTables;
use App\MasterProduct;

class TimeZoneController extends Controller
{
    public function __construct(TimeZone $TimeZone){
        $this->TimeZone = $TimeZone;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('time_zones.index');
    }

    public function storeTimeZones(){
        $result = $this->TimeZone->storeTimezones();
        if($result){
            echo "Success";
        }
        else{
            echo "Something Went Wrong!";
        }
    }

    public function TimeZoneList(Request $request){

        $pro = $this->TimeZone->orderBy('name','ASC')->get();
        return DataTables::of($pro)
        ->addColumn('command',function($pro){
            $command = '';
                $command.='<a onClick="GetModel(\''.route('time_zones.edit',$pro['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a> ';
                $command.='<form class="table_from d-inline" action="'.route("time_zones.destroy",$pro['id']).'" method="POST">
                '.method_field('DELETE').'
                '.csrf_field().'
                <button type="submit" class="btn btn-danger btn-sm btn-flat" onClick="return confirm(\'Do your really want to delete this?\')">Delete</button>
            </form>';
            $command.='';

            return $command;
        })
        ->rawColumns(['command'])
        ->make(true);
    }

    public function create(){
        return view('time_zones.create');
    }

    public function store(TimeZoneRequest $request){
        $input = $request->all();
        $data = [
            'name' => ProperInput($input['name']),
        ];
        $result = TimeZone::create($data);
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
        $row = TimeZone::where('id',$id)->first()->toArray();
        return view('time_zones.edit',compact('row'));
    }

    public function update(TimeZoneRequest $request,$id){
        $input = $request->all();
        $data = [
            'name' => $input['name'],
        ];
        $result =  TimeZone::where('id',$id)->update($data);
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
        $time_zones = $this->TimeZone::find($id);
        $result = $time_zones->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
