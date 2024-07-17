<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BrandStoreRequest;
use App\Brand;
use App\Manufacturer;
use DataTables;
use App\MasterProduct;
use App\MasterProductQueue;

class BrandController extends Controller
{
    public function __construct(Brand $Brand){
        $this->Brand = $Brand;
    }

    public function index(){
        if(moduleacess('AllSubMenusSelectionfunctions') == false){
            return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
        }
        return view('cranium.brand.index');
    }

    public function brandList(Request $request){

        $br = $this->Brand::select('brand.*','manufacturer_name')->leftJoin('manufacturer',function($join){
            $join->on('manufacturer.id','=','brand.manufacturer_id');
        })->orderBy('brand','ASC')->get();

        return DataTables::of($br)
        // ->addColumn('manufacturer_id',function($br) {
        //     $manufacturer = Manufacturer::where('id',$br['manufacturer_id'])->first();
        //     $manufacture_name="";
        //     if(!empty($manufacturer)){
        //         $manufacture_name = $manufacturer->manufacturer_name;
        //     }
        //     return $manufacture_name;
        // })
        ->addColumn('command',function($br){
            $command = '';
                $command.='<div class="row"><div class="col-xl-2"><a onClick="GetModel(\''.route('brands.edit',$br['id']).'\')" href="#" class="btn btn-sm btn-primary btn-flat">Edit</a></div> ';
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
        $manufacturers = Manufacturer :: pluck('manufacturer_name','id');
        return view('cranium.brand.create',compact('manufacturers'));
    }

    public function store(BrandStoreRequest $request){
        $input = $request->all();
        $exist_brand = Brand::where('brand', ProperInput($input['brand']))->first();
        if($exist_brand){
            $data_info = [
                'msg' => 'Brand is already exist',
                'error' => 1
            ];
        }
        $data = [
            'manufacturer_id' => $input['manufacturer_id'],
            'brand' => $input['brand'],
        ];
        $result = $this->Brand::create($data);
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
        $row = $this->Brand::where('id',$id)->first()->toArray();
        $manufacturers = Manufacturer::orderBy('manufacturer_name','ASC')->pluck('manufacturer_name','id');
        return view('cranium.brand.edit',compact('row','manufacturers'));
    }

    public function update(BrandStoreRequest $request,$id){
        $input = $request->all();
        // $existing_data = Brand::find($id);
        // $existing_manufacture = Manufacturer::find($input['manufacturer_id']);

        // $master_products = MasterProduct::where('brand', $existing_data->brand)->update(['brand' => $input['brand'], 'manufacturer' => $existing_manufacture->manufacture_name]);
        // $master_queue_products = MasterProductQueue::where('brand', $existing_data->brand)->update(['brand' => $input['brand'], 'manufacturer' => $existing_manufacture->manufacture_name]);

        $data = [
            'brand' =>ProperInput($input['brand']),
            'manufacturer_id' => $input['manufacturer_id'],
        ];
        $result =  $this->Brand::where('id',$id)->update($data);
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
        $Brand = $this->Brand::find($id);
        $pro_count = MasterProduct::where('brand','LIKE','%'.$Brand->brand.'%')->count();
        if($pro_count > 0){
            return redirect()->back()->with(['error'=>'This is used in Master Product, so we can not delete this']);
        }

        $result = $this->Brand::where('id',$id)->delete();
        if($result){
            return redirect()->back()->with(['success'=>'Successfully deleted']);
        }else{
            return redirect()->back()->with(['error'=>'Something went wrong']);
        }
    }
}
