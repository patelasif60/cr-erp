<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterProduct;
use App\SupplierDot;
use App\MappingTables;
use App\CsvHeader;
use DB;
use Schema;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use App\Imports\CsvDataImport;
use App\Http\Requests\CsvImportRequest;
use DataTables;
use Auth;

class SupplierDotController extends Controller
{
	public function __construct()
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
        $this->masterProduct = new MasterProduct();
	}

    public function map_supplier_dot_with_master_product(){
        $supplierDot = Schema::getColumnListing('supplier_dot');

        if (($key = array_search('id', $supplierDot)) !== false) {
            unset($supplierDot[$key]);
        }
        if (($key = array_search('created_at', $supplierDot)) !== false) {
            unset($supplierDot[$key]);
        }
        if (($key = array_search('updated_at', $supplierDot)) !== false) {
            unset($supplierDot[$key]);
        }

        $masterProduct = Schema::getColumnListing('master_product');

        if (($key = array_search('id', $masterProduct)) !== false) {
            unset($masterProduct[$key]);
        }
        if (($key = array_search('created_at', $masterProduct)) !== false) {
            unset($masterProduct[$key]);
        }
        if (($key = array_search('updated_at', $masterProduct)) !== false) {
            unset($masterProduct[$key]);
        }

        return view('supplierMapping.map_supplier_dot_with_master_product', compact('supplierDot', 'masterProduct'));
    }

    public function save_supplier_dot_with_master_product(Request $request){
        $request = $request->except(['_token']);
        $request_arr = (array) $request;

        $mappingTable = MappingTables::where('type', $request_arr['type'])->first();
        if(!$mappingTable){
            $mappingTable = new MappingTables();
        }

        if (($key = array_search('Select', $request_arr)) !== false) {
            unset($request_arr[$key]);
        }

        $mappingTable->type = $request_arr['type'];
        $mappingTable->map_data = json_encode($request_arr);
        $mappingTable->save();

        return redirect()->back();
    }

    public function map_supplier_with_csv()
    {
        if(moduleacess('MapSupplierWithCsv') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        return view('cranium.csvMapping.map_supplier_with_csv');
    }

    public function import_map_supplier_with_csv(CsvImportRequest $request){
        try{
            $supplier_fields = array();
            $header = array();

            $mimes = array('csv');
            $extension = pathinfo($request->file('csv_file')->getClientOriginalName(), PATHINFO_EXTENSION);

            if (in_array($extension, $mimes)) {
                $path = $request->file('csv_file')->getRealPath();
                if ($request->has('header')) {
                    $data = (new HeadingRowImport)->toArray(request()->file('csv_file'));
                } else {
                    $data = array_map('str_getcsv', file($path));
                }

                if (count($data[0]) > 0) {
                    if ($request->has('header')) {
                        $csv_header_fields = [];
                        foreach ($data[0] as $key => $value) {
                            $csv_header_fields[] = $key;
                        }
                    }
                    //$csv_data = $data[0];
                    //$csv_data = array_slice($data[0], 0, 1);
                    $header = $data[0];
                    $supplier_name = $request->input('supplier_name');


                    if($request->input('supplier_name') == 'supplier_dot'){
                        $supplier_fields = Schema::getColumnListing('supplier_dot');
                    }
                    if($request->input('supplier_name') == 'supplier_kehe'){
                        $supplier_fields = Schema::getColumnListing('supplier_kehe');
                    }
                    if($request->input('supplier_name') == 'supplier_dryers'){
                        $supplier_fields = Schema::getColumnListing('supplier_dryers');
                    }
                    if($request->input('supplier_name') == 'supplier_mars'){
                        $supplier_fields = Schema::getColumnListing('supplier_mars');
                    }
                    if($request->input('supplier_name') == 'supplier_miscellaneous'){
                        $supplier_fields = Schema::getColumnListing('supplier_miscellaneous');
                    }
                    if($request->input('supplier_name') == 'supplier_nestle'){
                        $supplier_fields = Schema::getColumnListing('supplier_nestle');
                    }
                    if($request->input('supplier_name') == 'supplier_hersley'){
                        $supplier_fields = Schema::getColumnListing('supplier_hersley');
                    }
                    if($request->input('supplier_name') == 'supplier_3pl'){
                        $supplier_fields = Schema::getColumnListing('3pl_client_product');
                    }


                    // Remove id, timestamp from fieldlist
                    if (($key = array_search('id', $supplier_fields)) !== false) {
                        unset($supplier_fields[$key]);
                    }
                    if (($key = array_search('created_at', $supplier_fields)) !== false) {
                        unset($supplier_fields[$key]);
                    }
                    if (($key = array_search('updated_at', $supplier_fields)) !== false) {
                        unset($supplier_fields[$key]);
                    }

                    if (!isset($header)) {
                        return redirect()->back()->with('error-msg', 'First Column of your CSV file is Blank, Unable to Map your Headers');
                    }


                } else {
                    return redirect()->back();
                }

                return view('csvMapping.import_supplier_csv', compact('header','supplier_fields','supplier_name'));
            } else {
                return redirect()->back()->with('error-msg', 'Please upload CSV formatted file');
            }
        }
        catch (\Throwable $e) {
            //Log::channel('daily')->error($e);
            return redirect()->back()->with('error-msg', 'Please upload correct CSV formatted file');
        }

    }

    public function save_supplier_with_csv(Request $request){
        //dd($request->input('supplier_name'));
        $supplier_name = $request->input('supplier_name');
        $request = $request->except(['_token', 'supplier_name']);
        //$request = $request->except(['supplier_name']);

        $request_arr = (array) $request;

        $mappingTable = CsvHeader::where('map_type', $supplier_name)->first();
        if(!$mappingTable){
            $mappingTable = new CsvHeader();
        }

        if (($key = array_search(null, $request_arr)) !== false) {
            unset($request_arr[$key]);
        }

        $mappingTable->map_type = $supplier_name;
        $mappingTable->map_data = json_encode($request_arr);
        $mappingTable->save();

        return redirect()->to('/map_supplier_with_csv');
    }

	public function getdotproducts(Request $request){
        // ini_set('memory_limit', '-1');
        if ($request->ajax()) {
            $data = SupplierDot::take(1000);

            return Datatables::of($data)
                    ->addColumn('action', function($row){
                        $btn = '';
                        if(Auth::user()->role<=2){
                            if($row->sync_master != 1){
                                $btn = '<a href="javascript:void(0)" onclick="syncdotproduct(\''.$row->id.'\')" id="syncdotproduct" class="btn btn-primary btn-sm" title="Sync. This Product With Master Product Table">Sync With MPT</a>';
                            }
                            else{
                                $btn = '<a href="javascript:void(0)" onclick="resyncdotproduct(\''.$row->id.'\')" id="resyncdotproduct" class="btn btn-success btn-sm" title="reSync. This Product With Master Product Table">Already Synced</a>';
                            }
                        }
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

	public function getdotmap(Request $request){

		if ($request->ajax()) {
            $data = DB::table('csv_header')->where('map_type','supplier_dot')->get();
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
				$jsonArr['Mapping is not available for - '] = '<b>Supplier Dot.</b>';
			}
		return $jsonArr;
		}
	}

    public function syncDotWithMasterProduct($id){
        $ETIN = NULL;
        $productData = SupplierDot::find($id);
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
        $masterProduct->product_listing_name = $productData->product_line;
        $masterProduct->full_product_desc = $productData->item_description;
        $masterProduct->etailer_availability = $productData->getEtailerAvailability();
        $masterProduct->dropship_available = $productData->item_available_via_drop_ship;
        $masterProduct->unit_size = $productData->pack_size;
        $masterProduct->supplier_product_number = $productData->dot_item;
        $masterProduct->upc = $productData->UPC;
        $masterProduct->prop_65_flag = $productData->proprietary;
        $masterProduct->product_tags = str_replace(";",",",$productData->diet_type);
        $masterProduct->hazardous_materials = $productData->hazMat_item;
        $masterProduct->GPC_code = $productData->GPC_code;
        $masterProduct->GPC_class = $productData->GPC_class;
        $masterProduct->weight = $productData->product_weight;
        $masterProduct->length = $productData->length;
        $masterProduct->width = $productData->width;
        $masterProduct->height = $productData->height;
        $masterProduct->current_supplier = "DOT";
        $masterProduct->save();

		$productData->update(['sync_master' => 1,'ETIN' => $ETIN]);
		$result = $productData->save();

        return back()->with('success', 'Product is now Sync with Master Product');
    }

}
