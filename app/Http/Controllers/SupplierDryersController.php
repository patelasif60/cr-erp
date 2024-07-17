<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DataTables\SupplierDryersDataTable;
use App\SupplierDryers;
use App\MasterProduct;
use DataTables;
use Auth;

class SupplierDryersController extends Controller
{
		public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->masterProduct = new MasterProduct();
	}
    public function getdryerproducts(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('supplier_dryers')->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
						$btn = '';
						if(Auth::user()->role<=2){
                            if($row->sync_master != 1){
                                $btn = '<a href="javascript:void(0)" onclick="syncDryersWithMasterProduct(\''.$row->id.'\')" id="syncDryersWithMasterProduct" class="edit btn btn-primary btn-sm">Sync with MPT</a>';
                            }
                            else{
                                $btn = '<a href="javascript:void(0)" onclick="resyncDryersWithMasterProduct(\''.$row->id.'\')" id="resyncdotproduct" class="btn btn-success btn-sm" title="reSync. This Product With Master Product Table">Already Synced</a>';
                            }
                            return $btn;
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        //return view('productedit');
    }
	public function dryerproductlist(Request $request){

		$productid = $request->id;
		$productdetails = DB::table('supplier_dryers')->find($productid);

		return view('supplierProdListing.producteditDryer', ['productdetails' =>$productdetails]);
	}

	public function syncDryersWithMasterProduct($id){
        $ETIN = NULL;
		$productData = SupplierDryers::find($id);

		$productData = SupplierDryers::find($id);
        $masterProduct =  new MasterProduct();
        if(!empty($productData) && $productData->ETIN != null){
            $ETIN = $productData->ETIN;
        }
        else{
            $masterProduct = $this->masterProduct;
            $ETIN = $masterProduct->getETIN($productData->temprature);
        }

        $masterProduct->ETIN = $ETIN;
		$masterProduct->full_product_desc = $productData->supplier_dryers_product_desc();
		$masterProduct->about_this_item = $productData->supplier_dryers_about();
		$masterProduct->manufacturer = $productData->supplier_dryers_manufacture();
		$masterProduct->flavor = $productData->supplier_dryers_flavor();
		$masterProduct->product_type = $productData->supplier_dryers_prod_type();
		$masterProduct->unit_size = $productData->supplier_dryers_unit_size();
		$masterProduct->pack_form_count = $productData->supplier_dryers_pack_from_count();
		$masterProduct->product_category = $productData->supplier_dryers_category();
		$masterProduct->product_subcategory1 = $productData->supplier_dryers_subcategory1();
		$masterProduct->key_product_attributes_diet = $productData->supplier_dryers_key_prod_attr();
		$masterProduct->product_tags = $productData->supplier_dryers_product_tags();
		$masterProduct->ingredients = $productData->supplier_dryers_ingredients();
		$masterProduct->product_temperature = $productData->supplier_dryers_product_temp();
		$masterProduct->upc = $productData->supplier_dryers_upc();
		$masterProduct->unit_in_pack = $productData->supplier_dryers_units_in_pack();
		$masterProduct->current_supplier = "Dryers";
		$masterProduct->save();

		$productData->update(['sync_master' => 1,'ETIN' => $ETIN]);
		$result = $productData->save();

        return back()->with('success', 'Product is now Synced with Master Product.');
	}

	public function getdryerssmap(Request $request){

		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_dryers')->get();
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
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Dryers.</b>';
			}
		return $jsonArr;
		}
	}
}
