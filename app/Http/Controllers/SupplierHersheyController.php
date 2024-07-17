<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;
use App\MasterProduct;
use App\SupplierHershey;
use Illuminate\Http\Request;

class SupplierHersheyController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->masterProduct = new MasterProduct();
	}

    public function gethersheyproducts(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('supplier_hersley')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
						$btn = '';
                        if($row->sync_master == 0){
                            $btn = '<a href="javascript:void(0)" onclick="syncHarsheyWithMasterProduct(\''.$row->id.'\')" id="syncHarsheyWithMasterProduct" class="edit btn btn-primary btn-sm">Sync Master Product</a>';
                        } else {
                            $btn = '<a href="javascript:void(0)" onclick="resyncHarsheyWithMasterProduct(\''.$row->id.'\')" id="resyncHarsheyWithMasterProduct" class="btn btn-raised btn-raised-success m-1">Already SYNC</a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

    public function syncHarsheyWithMasterProduct($id){
        $ETIN = NULL;
		$productData = SupplierHershey::find($id);
        
        $masterProduct =  new MasterProduct();
        if(!empty($productData) && $productData->ETIN != null){
            $ETIN = $productData->ETIN;
        }
        else{
            $ETIN = $masterProduct->getETIN($productData->temprature);
        }

        $masterProduct->ETIN = $ETIN;
		$masterProduct->manufacturer = "Hershey";
        $masterProduct->brand = $productData->getBrand();
        $masterProduct->product_type = $productData->promoted_product_groups;
        $masterProduct->unit_size = $productData->getUnitSize();
        $masterProduct->pack_form_count = $productData->total_each_qty;
        $masterProduct->product_category = $productData->getCategory();
        $masterProduct->product_subcategory1 = $productData->getCategory();
        $masterProduct->product_tags = $productData->getProductTags();
        $masterProduct->product_temperature = $productData->gettemperature();
        $masterProduct->supplier_product_number = $productData->item_no;
        $masterProduct->upc = isset($productData->expanded_upc) ?? $productData->UPC;
        $masterProduct->weight = $productData->getWeight();
        $masterProduct->length = $productData->dim_L_or_D;
        $masterProduct->cost = $productData->price_sch_2_1000_5_999_lbs;
		$masterProduct->current_supplier = "Hershey";
		$masterProduct->save();

		$productData->update(['sync_master' => 1,'ETIN' => $ETIN]);
		$result = $productData->save();

        return back()->with('success', 'Product is now Synced with Master Product.');
    }

	public function getharsleymap(Request $request){

		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_hersley')->get();
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
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Hersley.</b>';
			}
		return $jsonArr;
		}
	}
}
