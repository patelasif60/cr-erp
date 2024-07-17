<?php

namespace App\Http\Controllers;

use Yajra\DataTables\DataTables;
use App\MiscCostValue;
use Illuminate\Http\Request;

class MiscCostValuesController extends Controller
{

    public function getmisccostvalues()
    {
        $data = MiscCostValue::all();
        return Datatables::of($data)
        ->addIndexColumn()
            ->addColumn('action', function($contact){
                    $btn = '';
                    $url = route('misc_cost.edit',$contact->id);
                    $btn .= '<a href="javascript:void(0);" onclick="getModal(\''.$url.'\')"  class="edit btn btn-primary btn-sm mr-2">Edit</a>';
                    // $btn .= '<a  href="javascript:void(0);" onclick="deleteCost(\''.route('misc_cost.deleteCost',$contact->id).'\')"  class="delete btn btn-danger btn-sm">Delete</a>';
                    return $btn;
            })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function index()
    {
        return view('cranium.misc_cost_values.index');
    }

    public function create()
    {
        return view('cranium.misc_cost_values.create');
    }

    public function store(Request $request){

        $result = MiscCostValue::create([
            'data_point' => $request->data_point,
            'column_name' => strtolower(str_replace([' ','&','-'],['_','and','_'],$request->data_point)),
            'details' => $request->details,
            'value' => $request->value,
        ]);
        if($result){
            return response()->json(['msg' => 'Success' ,'error' => false]);
        }
        else{
            return response()->json(['msg' => 'Something went wrong.' ,'error' => true]);
        }
    }

    public function edit($id){
        $row = MiscCostValue::find($id);
        return view('cranium.misc_cost_values.edit',compact('row'));
    }

    public function update(Request $request, $id)
    {
        $row = MiscCostValue::find($id);
        $row->data_point = $request->data_point;
        $row->details = $request->details;
        $row->value = $request->value;
        $result = $row->save();

        if($result){
            return response()->json(['msg' => 'Success' ,'error' => false]);
        }
        else{
            return response()->json(['msg' => 'Something went wrong.' ,'error' => true]);
        }
    }

    public function deleteCost($id){
        $row = MiscCostValue::find($id);
        if (!empty($row)) {
             $row->delete();
             return response()->json([
                 'error' => 0,
                 'msg' => 'Success'
             ]);
        }
        else{
             return response()->json([
                 'error' => 1,
                 'msg' => 'Something went wrong!'
             ]);
        }
     }
}
