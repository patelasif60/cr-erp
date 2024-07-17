<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DataTables;
use App\SupplierMars;
use App\MasterProduct;
use Auth;

class SupplierMarsController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->masterProduct = new MasterProduct();
	}
	public function getmarsproducts(Request $request)
    {

        if ($request->ajax()) {
            $data = DB::table('supplier_mars')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                        $btn = '';
							if(Auth::user()->role<=2){
                                if($row->{'sync_master'} == 0){
                                    $btn = '<a href="javascript:void(0)" onclick="syncMarsWithMasterProduct()" id="syncMarsWithMasterProduct" class="edit btn btn-primary btn-sm">Sync Master Product</a>';
                                    //$btn = '<a href="javascript:void(0)" onclick="syncwithmaster()" id="syncwithmaster" class="edit btn btn-primary btn-sm">Sync</a>';
                                } else {
                                    $btn = '<a href="javascript:void(0)" onclick="resyncMarsWithMasterProduct()" id="resyncMarsWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                                }
                            }
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function syncMarsWithMasterProduct($id){
        $productData = SupplierMars::find($id);
        $masterProduct =  new MasterProduct();
        if(!empty($productData) && $productData->ETIN != null){
            $ETIN = $productData->ETIN;
        }
        else{
            $ETIN = $masterProduct->getETIN();
        }
        
        $masterProduct->ETIN = $ETIN;
        $masterProduct->brand = $productData->supplier_mars_brand();
        $masterProduct->manufacturer = $productData->supplier_mars_manufacture();
        $masterProduct->flavor = $productData->supplier_mars_flavor();
        $masterProduct->unit_size = $productData->unit_weight;
        $masterProduct->unit_description = $productData->supplier_mars_unit_desc();
        $masterProduct->MFG_shelf_life = $productData->supplier_mars_MFG_shelf_life();
        $masterProduct->supplier_product_number = $productData->supplier_mars_product_number();
        $masterProduct->pack_form_count = $productData->supplier_mars_pack_from_count();
        $masterProduct->unit_in_pack = $productData->supplier_mars_unit_in_pack();
        $masterProduct->upc = $productData->supplier_mars_upc();
        $masterProduct->gtin = $productData->supplier_mars_GTIN();
        $masterProduct->weight = $productData->supplier_mars_weight();
        $masterProduct->length = $productData->supplier_mars_length();
        $masterProduct->width = $productData->supplier_mars_width();
        $masterProduct->height = $productData->supplier_mars_height();
        $masterProduct->cost = $productData->supplier_mars_cost();
        $masterProduct->country_of_origin = $productData->supplier_mars_country_of_origin();
        $masterProduct->save();

		$productData->update(['sync_master' => true,'ETIN' => $ETIN]);
		$productData->save();

        return redirect()->back();
    }

	public function getmarsmap(Request $request){

		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_mars')->get();
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
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Mars.</b>';
			}
		return $jsonArr;
		}
	}
}
