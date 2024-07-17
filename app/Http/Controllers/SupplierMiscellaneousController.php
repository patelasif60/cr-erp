<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use DataTables;
use App\MasterProduct;
use Illuminate\Http\Request;
use App\SupplierMiscellaneous;

class SupplierMiscellaneousController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->supplierMisc = new SupplierMiscellaneous();
	}
	public function getmiscproducts(Request $request)
    {

        if ($request->ajax()) {
            $data = DB::table('supplier_miscellaneous')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
						$btn = '';
                        if(Auth::user()->role<=2){

                            if($row->sync_master != 1){
                                $btn = '<a href="javascript:void(0)" onclick="syncMiscWithMasterProduct(\''.$row->id.'\')" id="syncdotproduct" class="edit btn btn-primary btn-sm">Sync</a>';
                            }
                            else{
                                $btn = '<a href="javascript:void(0)" onclick="resyncMiscWithMasterProduct(\''.$row->id.'\')" id="resyncdotproduct" class="btn btn-success btn-sm" title="reSync. This Product With Master Product Table">Already Synced</a>';
                            }
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
	public function getmiscmap(Request $request){

		 if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_miscellaneous')->get();
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
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Miscellaneous.</b>';
			}
		return $jsonArr;
		}
	}

    public function syncMiscWithMasterProduct($id){
        $ETIN = NULL;
        $productData = SupplierMiscellaneous::find($id);
        $result = MasterProduct::where('upc', $productData->UPC)->first();

        if($result){
            $masterProduct = $result;
        }else{
             $masterProduct = new MasterProduct();
        }

        if($productData->ETIN != ''){
            $ETIN = $productData->ETIN;
        }elseif(isset($result->ETIN) &&  $result->ETIN != '' && $productData->ETIN == ''){
            $ETIN = $result->ETIN;
        }else{
            $ETIN = $this->masterProduct->getETIN($productData->temprature);
        }

        $masterProduct->ETIN = $ETIN;
        $masterProduct->brand = $productData->brand;
        $masterProduct->brand = $productData->getManufacturer($productData->manufacturer);
        $masterProduct->product_listing_name = $productData->getBrand($productData->brand);
        $masterProduct->unit_size = $productData->supplier_miscellaneous_unitsize($productData->unit_size);
        $masterProduct->unit_description = $productData->supplier_miscellaneous_unit_desc($productData->unit_description);
        $masterProduct->pack_form_count = $productData->supplier_miscellaneous_pack_count();
        $masterProduct->product_category = $productData->supplier_miscellaneous_category();
        $masterProduct->product_subcategory1 = $productData->supplier_miscellaneous_category();
        $masterProduct->product_subcategory2 = $productData->supplier_miscellaneous_category();
        $masterProduct->product_subcategory3 = $productData->supplier_miscellaneous_category();
        $masterProduct->key_product_attributes_diet = $productData->key_product_attributes_diet;
        $masterProduct->MFG_shelf_life = $productData->MFG_shelf_life;
        $masterProduct->hazardous_materials = $productData->hazardous_materials;
        $masterProduct->storage = $productData->storage;
        $masterProduct->ingredients = $productData->ingredients;
        $masterProduct->allergens = $productData->allergens;
        $masterProduct->product_temperature = $productData->product_temperature;
        $masterProduct->supplier_product_number = $productData->supplier_product_number;
        $masterProduct->manufacture_product_number = $productData->manufacturer_product_number;
        $masterProduct->upc = $productData->UPC;
        $masterProduct->gtin = $productData->GTIN;
        $masterProduct->weight = $productData->weight;
        $masterProduct->length = $productData->length;
        $masterProduct->width = $productData->width;
        $masterProduct->height = $productData->height;
        $masterProduct->country_of_origin = $productData->supplier_miscellaneous_country_of_origin($productData->country_of_origin);
        $masterProduct->package_information = $productData->package_information;
        $masterProduct->supplier_status = $productData->supplier_status;
        $masterProduct->cost = $productData->cost;
        $masterProduct->new_cost = $productData->new_cost;
        $masterProduct->new_cost_date = $productData->new_cost_date;
        $masterProduct->save();

		$productData->update(['sync_master' => 1,'ETIN' => $ETIN]);
		$result = $productData->save();

        return back()->with('success', 'Product is now Sync with Master Product');
    }
}
