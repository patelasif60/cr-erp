<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GelPacktService;
use App\WareHouse;
class GelPackController extends Controller
{
    public function __construct(GelPacktService $service)
    {
        $this->service = $service;
    }
    public function index(){
        $result = $this->service->getGelPackTemplate();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->get();//pluck('warehouses','id')->toArray();
        $resultArray = array_flip($this->service->getPackagematirial());
        return view('cranium.gelpack.index',compact('result','warehouse','resultArray'));
    }
    public function create(){
        $result = $this->service->getPackagematirial();
        return view('cranium.gelpack.create',compact('result'));
    }
    public function store(Request $request){
        $this->service->store($request);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('gelpack.index')
        ]);
    }
    public function edit($id){

        $result = $this->service->getPackagematirial();
        $gelPackTemplate = $this->service->edit($id);
        $gelPackChartArray = $gelPackTemplate->gelSubPack()->pluck('packaging_materials_id')->toArray();
        $diffArray=  array_diff($result,$gelPackChartArray);
        $result = array_flip($result);
        $gelEditArray = $gelPackTemplate->gelSubPack()->orderBy('id')->get();
        dd($gelEditArray);
        return view('cranium.gelpack.edit',compact('result','gelPackTemplate','gelPackChartArray','diffArray','gelEditArray'));
    }
    public function update(Request $request,$id){
        $this->service->update($request,$id);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('gelpack.index')
        ]);
    }
    public function updateWarehouseTemplate(Request $request){
        $ware = WareHouse::find($request->id);
        //dd($ware);
        $ware->gelPackTemplate()->sync($request->templateId);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('gelpack.index')
        ]);
    }

}
