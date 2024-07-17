<?php

namespace App\Http\Controllers;

use DB;
use App\Client;
use DataTables;
use App\Carrier;
use App\MasterCost;
use Illuminate\Http\Request;
use App\Services\PriceGroupService;

class PriceGroupController extends Controller
{
 	public function __construct(PriceGroupService $service)
    {
        $this->service = $service;
    }
	public function drag(){
        return view('price_group.drag');
    }
    public function index(){
        return view('price_group.index');
    }
    public function getPriceGroups(Request $request){
    	$results = $this->service->getPriceGroups($request);
    	return Datatables::of($results)->addColumn('action', function($result)
        {
            $btn = '';
            $btn .= '<a href="'.route('pricegroup.edit',$result->id).'" class="btn btn-primary">Edit</a>';
            $btn .= ' <a onClick="deletePriceGroup(\''.route('pricegroup.delete',$result->id).'\')" href="#" class="btn btn-danger">Delete</a>';
            return $btn;

        })->rawColumns(['action'])->make(true);
    }
    public function create(Request $request)
    {
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
		$masterCost = MasterCost::all();
        $carriers = Carrier::pluck('company_name','id')->toArray();
		return view('price_group.create',compact('client','carriers','masterCost'));
    }
    public function store(Request $request){
        $masterCost = MasterCost::all()->pluck('id')->toArray();
        $results = $this->service->store($request,$masterCost);
        if(!$results)
        {
            return response()->json([
                'error' => 1,
                'msg' => 'Select one block from each cost',
            ]);
        }
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('pricegroup.index')
        ]);
    }
    public function edit($id){
        $data =$this->service->edit($id);
        $result =  $data['priceGroup'];
        // dd($result);
        $subCostData =  $data['subCost'];
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $masterCost = MasterCost::all();
        $carriers = Carrier::pluck('company_name','id')->toArray();
        return view('price_group.edit',compact('client','masterCost','result','subCostData','id','carriers'));

    }
    public function update(Request $request,$id){
        $masterCost = MasterCost::all()->pluck('id')->toArray();
        $results = $this->service->update($request,$id,$masterCost);
        if(!$results)
        {
            return response()->json([
                'error' => 1,
                'msg' => 'Select one block from each cost',
            ]);
        }
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('pricegroup.index')
        ]);
    }

    public function delete($id){
        $results = $this->service->delete($id);
        return response()->json([
            'error' => false,
            'msg' => 'Success'
        ]);
    }
}
