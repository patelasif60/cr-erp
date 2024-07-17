<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use App\SupplierKehe;
use App\MasterProduct;
use Auth;

class SupplierKeheController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->masterProduct = new MasterProduct();
	}
   public function getkeheproducts(Request $request)
    {

        if ($request->ajax()) {
            $data = DB::table('supplier_kehe')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '';
                        if(Auth::user()->role<=2){

							if($row->{'sync_master'} == 0){
                           //$btn = '<a href="javascript:void(0)" onclick="syncdotproduct()" id="syncdotproduct" class="edit btn btn-primary btn-sm">Sync</a>';
                           $btn = '<a href="javascript:void(0)" onclick="syncKeheWithMasterProduct()" id="syncKeheWithMasterProduct" class="edit btn btn-primary btn-sm">Sync Master Product</a>';
							} else {
								$btn = '<a href="javascript:void(0)" onclick="resyncKeheWithMasterProduct()" id="resyncKeheWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
							}
                        }
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function syncKeheWithMasterProduct($id){
        $productData = SupplierKehe::find($id);
        $masterProduct =  new MasterProduct();
        if(!empty($productData) && $productData->ETIN != null){
            $ETIN = $productData->ETIN;
        }
        else{
            $masterProduct = $this->masterProduct;
            $ETIN = $masterProduct->getETIN();
        }
        $masterProduct->ETIN = $ETIN;
        $masterProduct->brand = $productData->supplier_kehe_brand();
        $masterProduct->unit_size = $productData->supplier_kehe_unit_size();
        $masterProduct->pack_form_count = $productData->supplier_kehe_pack_from_count();
        $masterProduct->product_category = $productData->supplier_kehe_category();
        $masterProduct->manufacture_product_number = $productData->supplier_kehe_supplier_prod_number();
        $masterProduct->upc = $productData->supplier_kehe_UPC();
        $masterProduct->current_supplier = "Kehe";
        $masterProduct->save();

		$productData->update(['sync_master' => true,'ETIN' => $ETIN]);
		$productData->save();

        return back()->with('success', 'Product is now Sync with Master Product');
    }
	public function getkehemap(Request $request){

		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_kehe')->get();
			$dataCount = $data->count();
			if($dataCount > 0){
				$json = json_decode(($data[0]->map_data),TRUE);
				$jsonArr = [];
				foreach ($json as $k => $v) {
					if($v != null){
						$jsonArr[$k] = $v;
					}
				}
			} else {
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Kehe.</b>';
			}
		return $jsonArr;
		}
	}
}
