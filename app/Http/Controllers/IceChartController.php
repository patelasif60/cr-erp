<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IceChartService;
use App\WareHouse;
use PDF;
use Excel;
use App\Exports\IceChartTemplateExport;
class IceChartController extends Controller
{
    public function __construct(IceChartService $service)
    {
        $this->service = $service;
    }
    public function index(){
        $result = $this->service->getIceChartTemplate();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->get();//pluck('warehouses','id')->toArray();
        $resultArray = array_flip($this->service->getPackagematirial());
        return view('cranium.icechart.index',compact('result','warehouse','resultArray'));
    }
    public function create(){
        $result = $this->service->getPackagematirial();
        return view('cranium.icechart.create',compact('result'));
    }
    public function store(Request $request){
        $this->service->store($request);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('icechart.index')
        ]);
    }
    public function edit($id){

        $result = $this->service->getPackagematirial();
        $iceChartTemplate = $this->service->edit($id);
        $iceSubChartArray = $iceChartTemplate->iceSubChart()->pluck('packaging_materials_id')->toArray();
        $diffArray=  array_diff($result,$iceSubChartArray);
        $result = array_flip($result);
        $iceEditArray = $iceChartTemplate->iceSubChart()->orderBy('id')->get();
        return view('cranium.icechart.edit',compact('result','iceChartTemplate','iceSubChartArray','diffArray','iceEditArray'));
    }
    public function update(Request $request,$id){
        $this->service->update($request,$id);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('icechart.index')
        ]);
    }
    public function updateWarehouseTemplate(Request $request){
        $ware = WareHouse::find($request->id);
        //dd($ware);
        $ware->iceChartTemplate()->sync($request->templateId);
        return response()->json([
            'error' => false,
            'msg' => 'Success',
            'url' => route('icechart.index')
        ]);
    }
    public function  exportWarehouseTemplate(Request $request,$id,$type){
        $result = $this->service->getPackagematirial();
        $iceChartTemplate = $this->service->edit($request->id);
        $result = array_flip($result);
        $iceEditArray = $iceChartTemplate->iceSubChart()->orderBy('id')->get();
        if($type=='pdf'){
            $pdf = PDF::loadView('cranium.icechart.master_template_pdf_export', compact('iceChartTemplate','result','iceEditArray'));
            if (!file_exists(public_path('pdf/'))) {
                mkdir(public_path('pdf/'), 0777, true);
            }
            $path = public_path('pdf/');
            // $fileName =  time().'.'. 'pdf' ;
            $fileName =  $iceChartTemplate->template_name.'.'. 'pdf' ;
            return $pdf->download($fileName);
            // $pdf->download($path . '/' . $fileName);
            // $pdf = public_path('pdf/'.$fileName);
            // return response()->download($pdf);
        }else{
            
            return  Excel::download(new IceChartTemplateExport($iceChartTemplate,$result,$iceEditArray), $iceChartTemplate->template_name.'.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
        }
        
    }

}
