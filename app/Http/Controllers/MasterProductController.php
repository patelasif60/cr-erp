<?php

namespace App\Http\Controllers;

use App;
use PDF;
use Excel;
use Schema;
use App\User;
use App\Brand;
use App\Client;
use DataTables;
use App\Supplier;
use App\PriceGroup;
use App\AisleMaster;
use App\MasterShelf;
use App\ProductType;
use App\OrderDetail;
use App\SmartFilter;
use Aws\S3\S3Client;
use App\ProductImage;
use App\MasterProduct;
use App\UploadHistory;
use App\SupplierStatus;
use App\MasterProductQueue;
use Illuminate\Support\Str;
use App\ItemFormDescription;
use Illuminate\Http\Request;
use App\ProductListingFilter;
use App\ThreeplClientProduct;
use Aws\Exception\AwsException;
use App\RequestProductSelection;
use App\SupplementalMptDataQueue;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\DB;
use App\Services\PriceGroupService;
use Illuminate\Support\Facades\Log;
use App\Imports\ThreePLClientImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;

use App\Exports\MasterProductExcelExport;
use App\Imports\ThreePLClientInsertImport;
use App\Http\Requests\MasterProductRequest;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Notification;
use App\Http\Requests\MasterProductApproveRequest;
use App\Notifications\ApproveRejectProductNotification;

class MasterProductController extends Controller
{
	public function __construct(MasterProduct $masterProduct, SmartFilter $SmartFilter, ProductListingFilter $ProductListingFilter, PriceGroupService $priceGroupService, NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->masterProduct = $masterProduct;
		$this->SmartFilter = $SmartFilter;
		$this->ProductListingFilter = $ProductListingFilter;
		$this->priceGroupService = $priceGroupService;
		$this->NotificationRepository = $NotificationRepository;
	}

	public function allmasterproductlsts()
	{
		if (moduleacess('ListAllMasterProduct') == false) {
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
		//Getting all Brand list
		$brand = [];
		$getbrand = DB::table('brand')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		//Getting all Manufacturer Name list
		$manufacturer = [];
		$getmanufacturer = DB::table('manufacturer')->get();
		foreach ($getmanufacturer as $manufacturers) {
			$manufacturer[] = $manufacturers->manufacturer_name;
		}

		//Getting all Suppliers list
		$suppliers = [];
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->get();
		foreach ($getsuppliers as $supplier) {
			$suppliers[] = $supplier->name;
		}
		//Getting all product list
		$products = [];
		$getproducts = DB::table('product_type')->get();
		foreach ($getproducts as $getproduct) {
			$products[] = $getproduct->product_type;
		}
		//Getting all Unit Description list
		$unitdesc = [];
		$getunitdesc = DB::table('unit_desc')->get();
		foreach ($getunitdesc as $unitdescs) {
			$unitdesc[] = $unitdescs->unit_description;
		}

		$item_form_desc = [];
		$getitem_form_desc = DB::table('item_from_description')->get();
		foreach ($getitem_form_desc as $ifd) {
			$item_form_desc[] = $ifd->item_desc;
		}



		//2
		$getet2 = [];
		$upcs2 = [];
		$getupcs2 = DB::table('master_product')->where('is_approve', 0)->whereNotNull('upc')->select('upc', 'ETIN')->get();
		foreach ($getupcs2 as $getupc2) {
			$upcs2[] = $getupc2->upc;
			$getet2[] = $getupc2->ETIN;
		}
		$listing_name2 = [];
		$getlisting_name2 = DB::table('master_product')->where('is_approve', 0)->whereNotNull('product_listing_name')->select('product_listing_name')->distinct()->get();
		foreach ($getlisting_name2 as $listing) {
			$listing_name2[] = $listing->product_listing_name;
		}

		//3
		$getet3 = [];
		$upcs3 = [];
		$getupcs3 = DB::table('master_product_queue')->where('queue_status', 'e')->whereNotNull('upc')->select('upc', 'ETIN')->get();
		foreach ($getupcs3 as $getupc3) {
			$upcs3[] = $getupc3->upc;
			$getet3[] = $getupc3->ETIN;
		}
		$listing_name3 = [];
		$getlisting_name3 = DB::table('master_product_queue')->where('queue_status', 'e')->whereNotNull('product_listing_name')->select('product_listing_name')->distinct()->get();
		foreach ($getlisting_name3 as $listing) {
			$listing_name3[] = $listing->product_listing_name;
		}


		//4
		$getet4 = [];
		$upcs4 = [];
		$getupcs4 = DB::table('master_product_queue')->where('queue_status', 'd')->whereNotNull('upc')->select('upc', 'ETIN')->get();
		foreach ($getupcs4 as $getupc4) {
			$upcs4[] = $getupc4->upc;
			$getet4[] = $getupc4->ETIN;
		}
		$listing_name4 = [];
		$getlisting_name4 = DB::table('master_product_queue')->where('queue_status', 'd')->whereNotNull('product_listing_name')->select('product_listing_name')->distinct()->get();
		foreach ($getlisting_name4 as $listing) {
			$listing_name4[] = $listing->product_listing_name;
		}

		return view('cranium.allmasterproductlists', [
			'brand' => $brand, 'manufacturer' => $manufacturer, 'suppliers' => $suppliers, 'unitdesc' => $unitdesc, 'products' => $products, 'getet2' => $getet2, 'upcs2' => $upcs2, 'getet3' => $getet3, 'upcs3' => $upcs3, 'getet4' => $getet4, 'upcs4' => $upcs4, 'listing_name2' => $listing_name2, 'listing_name3' => $listing_name3, 'listing_name4' => $listing_name4, 'item_form_desc' => $item_form_desc
		]);
	}

	public function allmasterproductlsts2($id = NULL)
	{
		if (moduleacess('ListAllMasterProduct') == false) {
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}

		$selected_smart_filter = [];
		$visible_filters = [];
		$hidden_cols = '';
		$visible_columns = '';
		$smart_filter = [];
		$hidden_cols_arr = [];
		$not_default_columns = [];
		$main_filter = [];
		$max_chars_columns = '';
		$product_listing_filter = ProductListingFilter::where('type','product')->orderBy('sorting_order')->get();
		$smart_filters = $this->SmartFilter::where('created_by', Auth::user()->id)->where('type','product')->get();
		// $max_chars_columns_arr = $this->ProductListingFilter::whereIn('column_name',['full_product_desc','about_this_item','ingredients','allergens','supplier_description','product_listing_name'])->pluck('sorting_order','id')->toArray();

		// if(!empty($max_chars_columns_arr)){
		// 	$max_chars_columns = implode(',',$max_chars_columns_arr);
		// }
		// dd($max_chars_columns);

		if ($id != NULL) {
			$all_columns = $this->ProductListingFilter::where('type','product')->pluck('sorting_order', 'id')->toArray();
			$smart_filter = $this->SmartFilter->find($id);

			$selected_smart_filter = json_decode($smart_filter->filter_preferences, true);
			$visible_columns = explode(',', $smart_filter->visible_columns);
			$visible_filters = explode(',', $smart_filter->visible_filters);
			if (!empty($all_columns)) {
				$hidden_cols_arr = array_diff($all_columns, $visible_columns);
				$hidden_cols = implode(',', $hidden_cols_arr);
			}
			$main_filter = json_decode($smart_filter->main_filter, true);
		} else {
			$not_default_columns = $this->ProductListingFilter::where('type','product')->where('is_default', '!=', 1)->pluck('sorting_order', 'id')->toArray();
			if (!empty($not_default_columns)) {
				$hidden_cols = implode(',', $not_default_columns);
			}
		}
		return view('cranium.allmasterproductlists2', ['product_listing_filter' => $product_listing_filter, 'smart_filters' => $smart_filters, 'selected_smart_filter' => $selected_smart_filter, 'id' => $id, 'hidden_cols' => $hidden_cols, 'visible_columns' => $visible_columns, 'visible_filters' => $visible_filters, 'smart_filter' => $smart_filter, 'main_filter' => $main_filter, 'hidden_cols_arr' => $hidden_cols_arr]);
	}


	public function addnewmasterview(Request $request)
	{

		if (ReadWriteAccess('AddNewParentProduct') == false) {
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}

		$req = $request->all();
		$newetin = $this->masterProduct->getETIN();

		// Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand')->groupBy('brand')->get();
		// foreach ($getbrand as $brands) {
		// 	$brand[] = $brands->brand;
		// }

		//Getting all Manufacturer Name list
		$getmanufacturer = DB::table('manufacturer')->get();
		// foreach ($getmanufacturer as $manufacturers) {
		// 	$manufacturer[] = $manufacturers->manufacturer_name;
		// }

		$categories = DB::table('categories')->where('level', 0)->get();


		//Getting all Product Type list
		$getproducttype = DB::table('product_type')->orderBy('product_type', 'ASC')->get();
		// foreach ($getproducttype as $producttypes) {
		// 	$producttype[] = $producttypes->product_type;
		// }

		$getunitsizes = DB::table('unit_sizes')->orderBy('unit', 'ASC')->get();
		


		$brand = $getbrand->pluck('brand')->toArray();
		$manufacturer = $getmanufacturer->pluck('manufacturer_name')->toArray();
		$producttype = $getproducttype->pluck('product_type')->toArray();
		$unitsize = $getunitsizes->pluck('unit','abbreviation')->toArray();

		//Getting all Unit Size list
		// $getunitsizes = DB::table('unit_sizes')->orderBy('unit', 'ASC')->get();
		// foreach ($getunitsizes as $unitsizes) {
		// 	$unitname[] = $unitsizes->unit;
		// 	$unitabb[] = $unitsizes->abbreviation;
		// 	$unitsize = array_combine($unitabb, $unitname);
		// }

		//Getting all Unit Description list
		$getunitdesc = DB::table('unit_desc')->orderBy('unit_description', 'ASC')->get();

		$unitdesc = $getunitdesc->pluck('unit_description')->toArray();
		
		//Getting all Product Tags list
		$getproducttags = DB::table('product_tags')->orderBy('tag', 'ASC')->get();

		$producttag = $getproducttags->pluck('tag','id')->toArray();

		//Getting all Product Temparaure list
		$getproducttemp = DB::table('product_temp')->orderBy('product_temperature', 'ASC')->get();
		$producttemp = $getproducttemp->pluck('product_temperature')->toArray();

		//Getting all Suppliers list
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name', 'ASC')->get();
		foreach ($getsuppliers as $suppliers) {
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine($supplier_id, $supplier_name);
		}
		//$supplier = $getunitsizes->pluck('id','name')->toArray();
		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin', 'ASC')->get();

		$country = $getcountries->pluck('country_of_origin','id')->toArray();
		//Getting all Item From Description list
		$getitemsdescs = DB::table('item_from_description')->orderBy('item_desc', 'ASC')->get();

		$itemsdesc = $getitemsdescs->pluck('item_desc')->toArray();

		//Getting all Clients list
		$getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();

		$client = $getclients->pluck('company_name','id')->toArray();
		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->orderBy('etailer_availability', 'ASC')->get();

		$etailers = $getetailers->pluck('etailer_availability','id')->toArray();
		//Getting all Warehouse list
		$getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();

		$warehouse = $getwarehouses->pluck('warehouses')->toArray();
		//Getting all Supplier Status
		$supplier_status = SupplierStatus::all();

		//Getting all prop_ingredients list
		$prop_ingredients = [];
		$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients', 'ASC')->get();
		
		$prop_ingredients = $getprop_ingredients->pluck('prop_ingredients','id')->toArray();
		//Getting all allergens list
		$allergens = [];
		
		$getallergens = DB::table('allergens')->orderBy('allergens', 'ASC')->get();
		
		$allergens = $getallergens->pluck('allergens','id')->toArray();
		
		$image_type = DB::table('image_type')->get();
		$image_type_count = DB::table('image_type')->count();
		$product_status = DB::table('product_statuses')->get();

		return view('cranium.supplierProdListing.addnewmasterproduct', ['image_types' => $image_type, 'brand' => $brand, 'manufacturer' => $manufacturer, 'categories' => $categories, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'supplier' => $supplier, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'newetin' => $newetin, 'etailers' => $etailers, 'warehouse' => $warehouse, 'supplier_status' => $supplier_status, 'prop_ingredients' => $prop_ingredients, 'allergens' => $allergens, 'image_type_count' => $image_type_count, 'req' => $req, 'product_status' => $product_status]);
	}

	public function insertnewmaster(MasterProductRequest $request)
	{

		$insertmasterproduct = [];
		$insertimgs = [];
		$insertmpt = [];

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Images Validated');

		if (isset($request->upc_present) && !isset($request->upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "UPC is missing"
			]);
		}

		if (isset($request->gtin_present) && !isset($request->gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "GTIN is missing"
			]);
		}

		if (isset($request->unit_upc_present) && !isset($request->unit_upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit UPC is missing"
			]);
		}

		if (isset($request->unit_gtin_present) && !isset($request->unit_gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit GTIN is missing"
			]);
		}

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1) {
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if ($request->get('product_temperature') == "Frozen") {
				$insertmasterproduct['ETIN'] = 'ETFZ-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$insertmasterproduct['ETIN'] = 'ETDS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$insertmasterproduct['ETIN'] = 'ETRF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$insertmasterproduct['ETIN'] = 'ETBV-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$insertmasterproduct['ETIN'] = 'ETDP-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$insertmasterproduct['ETIN'] = 'ETDF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$insertmasterproduct['ETIN'] = 'ETTS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			}
		}

		//Etin ready for ProductImage table
		$insertimgs['ETIN'] = $insertmasterproduct['ETIN'];

		$id = $request->get('id');

		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertmasterproduct['product_listing_name'] = $request->get('product_listing_name');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#', $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
		$insertmasterproduct['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$insertmasterproduct['unit_description'] = $request->get('unit_description');
		$insertmasterproduct['pack_form_count'] = $request->get('pack_form_count');
		$insertmasterproduct['item_form_description'] = $request->get('item_form_description');
		$insertmasterproduct['total_ounces'] = $request->get('total_ounces');
		$insertmasterproduct['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$insertmasterproduct['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$insertmasterproduct['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$insertmasterproduct['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$insertmasterproduct['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$insertmasterproduct['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$insertmasterproduct['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$insertmasterproduct['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$insertmasterproduct['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$insertmasterproduct['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		// $insertmasterproduct['product_tags'] = implode(',' , $request->get('product_tags'));
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = $request->get('storage');
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['supplier_status'] = $request->get('supplier_status');
		$insertmasterproduct['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
		$insertmasterproduct['gtin'] = isset($request->gtin) ? ProperInput($request->get('gtin')) : '';
		$insertmasterproduct['asin'] = ProperInput($request->get('asin'));
		$insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['GPC_code'] = ProperInput($request->get('GPC_code'));
		$insertmasterproduct['GPC_class'] = ProperInput($request->get('GPC_class'));
		$insertmasterproduct['HS_code'] = ProperInput($request->get('HS_code'));
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = ProperInput($request->get('package_information'));
		$insertmasterproduct['cost'] = $request->get('cost');
		$insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['dropship_available'] = $request->get('dropship_available');
		$insertmasterproduct['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		// $updatemaster['warehouses_assigned'] = $request->get('warehouses_assigned');
		$insertmasterproduct['status_date'] = $request->get('status_date');
		// $insertmasterproduct['lobs'] = implode(',' , $request->get('lobs'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['chanel_ids'] = $request->get('chanel_ids');
		$insertmasterproduct['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertmasterproduct['client_supplier_id'] = $request->get('current_supplier');
		$insertmasterproduct['alternate_ETINs'] = $request->get('alternate_ETINs');
		$insertmasterproduct['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertmasterproduct['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$insertmasterproduct['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['week_worth_qty'] = $request->get('week_worth_qty') != null || $request->get('week_worth_qty') > 0 ? $request->get('week_worth_qty') : 0;
		$insertmasterproduct['min_order_qty'] = $request->get('min_order_qty') != null || $request->get('min_order_qty') > 0 ? $request->get('min_order_qty') : 0;
		$insertmasterproduct['lead_time'] = $request->get('lead_time');
		$insertmasterproduct['inserted_by'] = Auth::user()->id;
		if (moduleacess('auto_approval_for_edit')) {
			$insertmasterproduct['is_approve'] = 1;
			$insertmasterproduct['approved_date'] = date('Y-m-d H:i:s');
		}


		$insertmasterproduct['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

		$insertmasterproductinventory['ETIN'] = $insertmasterproduct['ETIN'];

		// check UPC
		$existsupc = isset($request->upc) ? DB::table('master_product')->where('UPC', $request->get('upc'))->first() : false;
		$existsgtin = isset($request->gtin) ? DB::table('master_product')->where('GTIN', $request->get('gtin'))->first() : false;

		if (!$existsupc && !$existsgtin) {

			$master_product_id = DB::table('master_product')->insertGetId($insertmasterproduct);
			if ($master_product_id) {
				$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Master Product Inserted.');
				UserLogs([
					'user_id' => Auth::user()->id,
					'action' => 'Click',
					'task' => 'Add Product',
					'details' => 'Item '.$insertmasterproduct['ETIN'].' product created .',
					'type' => 'CWMS'
				]);
			}
			$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Product History Inserted.');

			$this->masterProduct->sendApproveRejectNotificationForAdd($master_product_id, $insertmasterproduct['ETIN'], $insertmasterproduct['product_listing_name']);
			$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Master Product Added Notification Sent.');

			$check_supplemental_mpt_data = DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->first();
			if (!$check_supplemental_mpt_data) {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['master_product_id'] = $master_product_id;
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['created_at'] = date('Y-m-d H:i:s');
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data')->insert($insertmpt);
				$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Supplimental MPT Data Inserted.');
			} else {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->update($insertmpt);
				$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Supplimental MPT Data Updated.');
			}
			$insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'], $request->all());
			$this->masterProduct->insertProcessLog('InsertMasterProduct', 'Product Images Inserted');
			$this->masterProduct->MakeProductHistory([
				'response' => Auth::user()->name . ' created Product: ' . $insertmasterproduct['ETIN'],
				'master_product_id' => $master_product_id,
				'action' => 'Add'
			]);
		} else {

			if ($existsupc && $existsgtin) {
				$upcetin = $existsupc->ETIN;
				$gtinetin = $existsgtin->ETIN;
				if ($upcetin == $gtinetin) {

					$msg = "UPC " . $request->get('upc') . " and GTIN " . $request->get('gtin') . " already exists for ETIN # " . $upcetin . " and cannot be added.";
				} else {

					$msg = "UPC " . $request->get('upc') . " is already exists for ETIN # " . $upcetin . " , GTIN " . $request->get('gtin') . " is exists for ETIN # " . $gtinetin . " and cannot be added.";
				}
			} else {
				if ($existsupc) {
					$upcetin = $existsupc->ETIN;
					$msg = "UPC " . $request->get('upc') . " already exists for ETIN # " . $upcetin . " and cannot be added.";
				} else if ($existsgtin) {
					$gtinetin = $existsgtin->ETIN;
					$msg = "GTIN " . $request->get('gtin') . " already exists for ETIN # " . $gtinetin . " and cannot be added.";
				}
			}


			return response()->json([
				'error' => 1,
				'msg' => $msg
			]);
		}

		/* Notify other admins */		
		if(auth()->user()){
			$user = auth()->user();
			$note = $insertmasterproduct['ETIN'] . " Master Product Added by ".$user->name;
			$url_id = '';

			$product = DB::table('master_product')->where('ETIN', $insertmasterproduct['ETIN'])->first();
			if($product){
				$url_id = $product->id;
			}
			$url = '/editmasterproduct/'.$url_id.'/tab_comments';
			$type = "New Master Product";

			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);

				
		}

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
	}
	public function getmasterproducts(Request $request)
	{
		/*var_dump($request);
		die();*/

		if ($request->ajax()) {

			$dataget = DB::table('master_product')->where('is_approve', 1)
				->leftJoin('users', function ($join) {
					$join->on('users.id', '=', 'master_product.updated_by');
				})->select(['master_product.id', 'master_product.ETIN', 'master_product.product_listing_name', 'master_product.brand', 'master_product.current_supplier', 'master_product.upc', 'master_product.item_form_description', 'master_product.allergens', 'master_product.is_approve', 'users.name as username']);

			if ($request->etin_filter != '') {
				if (isset($request->etin_filter)) {
					$dataget->whereIn('ETIN', $request->etin_filter);
				}
			}

			if ($request->listing_name_filter != '') {
				if (isset($request->listing_name_filter)) {
					$dataget->whereIn('product_listing_name', $request->listing_name_filter);
				}
			}
			if ($request->brand_filter != '') {
				if (isset($request->brand_filter)) {
					$dataget->whereIn('brand', $request->brand_filter);
				}
			}
			if ($request->manufacturer_filter != '') {
				if (isset($request->manufacturer_filter)) {
					$dataget->whereIn('manufacturer', $request->manufacturer_filter);
				}
			}

			if ($request->supplier_filter != '') {
				if (isset($request->supplier_filter)) {
					$dataget->whereIn('current_supplier', $request->supplier_filter);
				}
			}

			if ($request->unit_description_filter != '') {
				if (isset($request->unit_description_filter)) {
					$dataget->whereIn('unit_description', $request->unit_description_filter);
				}
			}

			if ($request->product_filter != '') {
				if (isset($request->product_filter)) {
					$dataget->whereIn('product_type', $request->product_filter);
				}
			}

			if ($request->upc_filter != '') {
				if (isset($request->upc_filter)) {
					$dataget->whereIn('upc', $request->upc_filter);
				}
			}
			if ($request->item_form_desc_filter != '') {
				if (isset($request->item_form_desc_filter)) {
					$dataget->whereIn('item_form_description', $request->item_form_desc_filter);
				}
			}
			if ($request->allergens_main_filter != '') {
				if (isset($request->allergens_main_filter)) {
					$dataget->whereIn('allergens', $request->allergens_main_filter);
				}
			}
			if ($request->warehouse != '') {
				if (isset($request->warehouse)) {
					$dataget->whereRaw('FIND_IN_SET(\''.$request->warehouse.'\',master_product.warehouses_assigned)');
				}
			}


			$dataget->groupBy("id");

			$total = $dataget->count();
			$limit = 12;
			if (isset($input['limit'])) $limit = $input['limit'];

			$page = 1;
			if (isset($input['page'])) $page = $input['page'];

			$offset = $request->get('start');
			$limit = $request->get('length');
			$dataget->skip($offset)->take($limit);
			$data = $dataget->get();

			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_approve', function ($data) {
					return ($data->is_approve == '0') ? "No" : "Yes";
				})
				->addIndexColumn()
				->addColumn('action', function ($row) {
					$btn = '';
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
					} else {
						if (ReadWriteAccess('ActiveProductListingsEditProduct')) {
							$btn = '<a href="' . route('editmasterproduct', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
						}
					}

					return $btn;
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	public function getmasterproducts2(Request $request)
	{
		// ini_set('max_input_vars', '1500');

		/*var_dump($request);
		die();*/
		// dd($request->all());
		// $select_cat = '';
		if ($request->ajax()) {

			$dataget = DB::table('master_product')->where('is_approve', 1)->leftJoin('users', function ($join) {
				$join->on('users.id', '=', 'master_product.updated_by');
			})
				->leftjoin("clients", DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"), ">", DB::raw("'0'"))
				->leftjoin("prop_ingredients", DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"), "!=", DB::raw("''"))
				->leftjoin("categories", 'categories.id', "=", 'master_product.product_category');

			for ($i = 1; $i < 10; $i++) {
				$dataget->leftjoin("categories as subcat" . $i, "subcat" . $i . ".id", "=", "master_product.product_subcategory" . $i);
			}

			$dataget->leftjoin("supplier_status", 'supplier_status.id', "=", 'master_product.supplier_status')
				->select(['master_product.*', 'categories.name as product_category', 'subcat1.name as product_subcategory1', 'subcat2.name as product_subcategory2', 'subcat2.name as product_subcategory2', 'subcat3.name as product_subcategory3', 'subcat4.name as product_subcategory4', 'subcat5.name as product_subcategory5', 'subcat6.name as product_subcategory6', 'subcat7.name as product_subcategory7', 'subcat8.name as product_subcategory8', 'subcat9.name as product_subcategory9', 'users.name as username', DB::raw("GROUP_CONCAT(clients.company_name) as lobs"), DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient"), DB::raw("supplier_status.supplier_status as supplier_status")]);

			//--------------------Main Filters--------------------------------------
			$filter_val = $request->filter_val;

			if (isset($filter_val)) {
				foreach ($filter_val as $key => $row_val) {
					if (isset($row_val[$key])) {


						// dump($key);
						// dump($row_val);
						$search_value_key = $row_val[$key];
						$search_value = '';
						if (isset($row_val[$search_value_key])) {
							$search_value = $row_val[$search_value_key];
						}

						$filter_info = json_decode($row_val['info'], true);
						$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
						$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
						$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
						$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
						$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

						for ($i = 1; $i < 10; $i++) {
							if ($column_name == 'product_subcategory' . $i) {
								$select_table = 'subcat' . $i;
							}
						}

						// $search_value = '';
						// if(isset($row_val[$search_value_key])){
						// 	$search_value = $row_val[$search_value_key];
						// }

						if ($search_value_key == "is_blank") {
							$dataget->whereNull($key);
						}

						if ($search_value_key == "is_not_blank") {
							$dataget->whereNotNull($key);
						}

						if ($search_value_key == "equals" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, $search_value);
							} else {
								$dataget->where($key, $search_value);
							}
						}
						if ($search_value_key == "include_only" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->whereIN($select_table . '.' . $select_label_column, explode(',', $search_value));
							} else {
								$dataget->whereIN($key, explode(',', $search_value));
							}
						}


						// if($request->brand_main_filter != '' ){
						// 	if(isset($request->brand_main_filter)){
						// 		$dataget->whereIn('brand', $request->brand_main_filter);
						// 	}
						// }

						// if($request->supp_main_filter != '' ){
						// 	if(isset($request->supp_main_filter)){
						// 		$dataget->whereIn('current_supplier', $request->supp_main_filter);
						// 	}
						// }


						if ($search_value_key == "exclude" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->whereNotIN($select_table . '.' . $select_label_column, explode(',', $search_value));
							} else {
								$dataget->whereNotIN($key, explode(',', $search_value));
							}
						}
						if ($search_value_key == "does_not_equals" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, '!=', $search_value);
							} else {
								$dataget->where($key, '!=', $search_value);
							}
						}

						if ($search_value_key == "contains" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '%' . $search_value . '%');
							} else {
								$dataget->where($key, 'LIKE', '%' . $search_value . '%');
							}
						}

						if ($search_value_key == "starts_with" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '' . $search_value . '%');
							} else {
								$dataget->where($key, 'LIKE', '' . $search_value . '%');
							}
						}

						if ($search_value_key == "ends_with" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '%' . $search_value . '');
							} else {
								$dataget->where($key, 'LIKE', '%' . $search_value . '');
							}
						}

						if ($search_value_key == "does_not_starts_with" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, 'NOT LIKE', '' . $search_value . '%');
							} else {
								$dataget->where($key, 'NOT LIKE', '' . $search_value . '%');
							}
						}

						if ($search_value_key == "does_not_starts_with" && $search_value != "") {
							if ($select_value_column == 'id' && $select_table != '') {
								$dataget->where($select_table . '.' . $select_label_column, 'NOT LIKE', '%' . $search_value . '');
							} else {
								$dataget->where($key, 'NOT LIKE', '%' . $search_value . '');
							}
						}
					}
				}
			}

			$main_filter = $request->main_filter;
			if (isset($main_filter)) {
				foreach ($main_filter as $key_main => $row_main_filter) {
					if ($row_main_filter != '') {
						$unique_value = array_unique(explode(',', $row_main_filter));

						$dataget->where(function ($q) use ($unique_value, $key_main) {
							if ($unique_value) {
								foreach ($unique_value as $row_un_val) {
									if ($key_main == 'allergens') {
										$q->orWhereRaw('FIND_IN_SET(\'' . $row_un_val . '\',master_product.' . $key_main . ') > "0"');
									} else {
										$q->orWhereRaw('FIND_IN_SET(\'' . $row_un_val . '\',' . $key_main . ') > "0"');
									}
								}
							}
						});
					}
				}
			}


			$boolean_filters = $request->boolean_filters;
			if (!empty($boolean_filters)) {
				foreach ($boolean_filters as $key => $value) {
					if ($value != '')
						$dataget->where($key, $value);
				}
			}

			$dataget->groupBy("master_product.id");

			$total = $dataget->count();
			$limit = 10;
			if (isset($input['limit'])) $limit = $input['limit'];
			$page = 1;
			if (isset($input['page'])) $page = $input['page'];

			$offset = $request->get('start');
			$limit = $request->get('length');
			$dataget->skip($offset)->take($limit);
			$data = $dataget->get();

			// dd($data);
			$ActiveProductListingsEditProduct = ReadWriteAccess('ActiveProductListingsEditProduct');
			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_approve', function ($data) {
					return ($data->is_approve == '0') ? "No" : "Yes";
				})
				->addIndexColumn()
				->addColumn('action', function ($row) use ($ActiveProductListingsEditProduct) {
					$btn = '';
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
					} else {
						if ($ActiveProductListingsEditProduct) {
							$btn = '<a href="' . route('editmasterproduct', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
						}
					}

					return $btn;
				})
				->editColumn('product_listing_name', function ($data) {
					return Str::limit($data->product_listing_name, 100, ('...'));
				})
				->editColumn('full_product_desc', function ($data) {
					return Str::limit($data->full_product_desc, 100, ('...'));
				})
				->editColumn('about_this_item', function ($data) {
					return Str::limit($data->about_this_item, 100, ('...'));
				})
				->editColumn('ingredients', function ($data) {
					return Str::limit($data->ingredients, 100, ('...'));
				})
				->editColumn('allergens', function ($data) {
					$allergens = DB::table('allergens')->select(DB::raw("GROUP_CONCAT(allergens) as allergens"))->whereIN('id', explode(',', $data->allergens))->first();
					return Str::limit($allergens->allergens, 100, ('...'));
				})
				->editColumn('product_tags', function ($data) {
					$product_tags = DB::table('product_tags')->select(DB::raw("GROUP_CONCAT(tag	) as product_tags"))->whereIN('id', explode(',', $data->product_tags))->first();
					return Str::limit($product_tags->product_tags, 100, ('...'));
				})
				// ->editColumn('supplier_description', function ($data) {
				// 	return Str::limit($data->supplier_description,100, ('...'));
				// })
				->editColumn('current_supplier', function ($data) {
					return Str::limit($data->current_supplier, 100, ('...'));
				})
				->rawColumns(['action'])
				->make(true);
		}
	}

	public function exportMasterProducts(Request $request)
	{
		// dd($request->all());
		ini_set('max_execution_time', '0');
		ini_set('max_input_time', '0');
		ini_set('memory_limit', '-1');
		set_time_limit(0);
		$id = isset($request->id) ? $request->id : NULL;
		$visible_columns = [];
		$smart_filter = [];
		if ($id != NULL) {
			$smart_filter = $this->SmartFilter->find($id);
			$visible_columns = explode(',', $smart_filter->visible_columns);
		}

		$dataget = DB::table('master_product')->where('is_approve', 1)->leftJoin('users', function ($join) {
			$join->on('users.id', '=', 'master_product.updated_by');
		})
			->leftjoin("clients", DB::raw("FIND_IN_SET(clients.id,master_product.lobs)"), ">", DB::raw("'0'"))
			->leftjoin("prop_ingredients", DB::raw("FIND_IN_SET(prop_ingredients.prop_ingredients,master_product.prop_65_ingredient)"), "!=", DB::raw("''"))
			->leftjoin("categories", 'categories.id', "=", 'master_product.product_category');

		for ($i = 1; $i < 10; $i++) {
			$dataget->leftjoin("categories as subcat" . $i, "subcat" . $i . ".id", "=", "master_product.product_subcategory" . $i);
		}

		$dataget->leftjoin("supplier_status", 'supplier_status.id', "=", 'master_product.supplier_status')
			->select(['master_product.*', 'categories.name as product_category', 'subcat1.name as product_subcategory1', 'subcat2.name as product_subcategory2', 'subcat2.name as product_subcategory2', 'subcat3.name as product_subcategory3', 'subcat4.name as product_subcategory4', 'subcat5.name as product_subcategory5', 'subcat6.name as product_subcategory6', 'subcat7.name as product_subcategory7', 'subcat8.name as product_subcategory8', 'subcat9.name as product_subcategory9', 'users.name as username', DB::raw("GROUP_CONCAT(clients.company_name) as lobs"), DB::raw("GROUP_CONCAT(prop_ingredients.prop_ingredients) as prop_65_ingredient"), DB::raw("supplier_status.supplier_status as supplier_status")]);
		//--------------------Main Filters--------------------------------------
		$filter_val = $request->filter_val;
		// dd($filter_val);
		if (isset($filter_val)) {
			foreach ($filter_val as $key => $row_val) {
				$search_value_key = $row_val[$key];
				$search_value = '';
				if (isset($row_val[$search_value_key])) {
					$search_value = $row_val[$search_value_key];
				}

				$filter_info = json_decode($row_val['info'], true);
				$text_or_select = (isset($filter_info['text_or_select']) ? $filter_info['text_or_select'] : '');
				$select_value_column = (isset($filter_info['select_value_column']) ? $filter_info['select_value_column'] : '');
				$select_label_column = (isset($filter_info['select_label_column']) ? $filter_info['select_label_column'] : '');
				$column_name = (isset($filter_info['column_name']) ? $filter_info['column_name'] : '');
				$select_table = (isset($filter_info['select_table']) ? $filter_info['select_table'] : '');

				for ($i = 1; $i < 10; $i++) {
					if ($column_name == 'product_subcategory' . $i) {
						$select_table = 'subcat' . $i;
					}
				}

				// $search_value = '';
				// if(isset($row_val[$search_value_key])){
				// 	$search_value = $row_val[$search_value_key];
				// }

				if ($search_value_key == "is_blank") {
					$dataget->whereNull($key);
				}

				if ($search_value_key == "is_not_blank") {
					$dataget->whereNotNull($key);
				}

				if ($search_value_key == "equals" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, $search_value);
					} else {
						$dataget->where($key, $search_value);
					}
				}
				if ($search_value_key == "include_only" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->whereIN($select_table . '.' . $select_label_column, explode(',', $search_value));
					} else {
						$dataget->whereIN($key, explode(',', $search_value));
					}
				}


				// if($request->brand_main_filter != '' ){
				// 	if(isset($request->brand_main_filter)){
				// 		$dataget->whereIn('brand', $request->brand_main_filter);
				// 	}
				// }

				// if($request->supp_main_filter != '' ){
				// 	if(isset($request->supp_main_filter)){
				// 		$dataget->whereIn('current_supplier', $request->supp_main_filter);
				// 	}
				// }


				if ($search_value_key == "exclude" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->whereNotIN($select_table . '.' . $select_label_column, explode(',', $search_value));
					} else {
						$dataget->whereNotIN($key, explode(',', $search_value));
					}
				}
				if ($search_value_key == "does_not_equals" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, '!=', $search_value);
					} else {
						$dataget->where($key, '!=', $search_value);
					}
				}

				if ($search_value_key == "contains" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '%' . $search_value . '%');
					} else {
						$dataget->where($key, 'LIKE', '%' . $search_value . '%');
					}
				}

				if ($search_value_key == "starts_with" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '' . $search_value . '%');
					} else {
						$dataget->where($key, 'LIKE', '' . $search_value . '%');
					}
				}

				if ($search_value_key == "ends_with" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, 'LIKE', '%' . $search_value . '');
					} else {
						$dataget->where($key, 'LIKE', '%' . $search_value . '');
					}
				}

				if ($search_value_key == "does_not_starts_with" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, 'NOT LIKE', '' . $search_value . '%');
					} else {
						$dataget->where($key, 'NOT LIKE', '' . $search_value . '%');
					}
				}

				if ($search_value_key == "does_not_starts_with" && $search_value != "") {
					if ($select_value_column == 'id' && $select_table != '') {
						$dataget->where($select_table . '.' . $select_label_column, 'NOT LIKE', '%' . $search_value . '');
					} else {
						$dataget->where($key, 'NOT LIKE', '%' . $search_value . '');
					}
				}
			}
		}

		$main_filter = $request->main_filter;
		if (isset($main_filter)) {
			foreach ($main_filter as $key_main => $row_main_filter) {
				if ($row_main_filter != '') {
					$unique_value = array_unique(explode(',', $row_main_filter));

					$dataget->where(function ($q) use ($unique_value, $key_main) {
						if ($unique_value) {
							foreach ($unique_value as $row_un_val) {
								if ($key_main == 'allergens') {
									$q->orWhereRaw('FIND_IN_SET(\'' . $row_un_val . '\',master_product.' . $key_main . ') > "0"');
								} else {
									$q->orWhereRaw('FIND_IN_SET(\'' . $row_un_val . '\',' . $key_main . ') > "0"');
								}
							}
						}
					});
				}
			}
		}

		$dataget->groupBy("master_product.id");

		$product_data = $dataget->get();

		$mastrpro = new MasterProduct();

		if ($request->type == 'excel') {
			return  Excel::download(new MasterProductExcelExport($product_data, $filter_val, $visible_columns, $mastrpro), 'Cranium_' . date('Ymd') . '.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
		} else {
			$pdf = PDF::loadView('cranium.exports.master_product_pdf_export', compact('product_data', 'filter_val', 'visible_columns', 'mastrpro'));
			if (!file_exists(public_path('pdf/'))) {
				mkdir(public_path('pdf/'), 0777, true);
			}
			$path = public_path('pdf/');
			$fileName =  time() . '.' . 'pdf';
			$pdf->save($path . '/' . $fileName);
			$pdf = public_path('pdf/' . $fileName);

			return response()->download($pdf);
		}
	}

	public function deletepdf()
	{
		$folder_path = public_path('pdf');
		$files = glob($folder_path . '/*');
		foreach ($files as $file) {
			if (is_file($file))
				unlink($file);
		}
		return response()->json(['status' => true, 'msg' => 'Files Deleted']);
	}

	public function getnotapprovedmasterproducts(Request $request)
	{

		if ($request->ajax()) {
			//$data = DB::table('master_product')->where(['is_approve' => 0, 'is_edit' => 0])->get();
			$dataget = MasterProduct::with(['users'])->where(['is_approve' => 0])
				->leftJoin('users', function ($join) {
					$join->on('users.id', '=', 'master_product.inserted_by');
				})->select(['master_product.id', 'master_product.ETIN', 'master_product.product_listing_name', 'master_product.brand', 'master_product.current_supplier', 'master_product.upc', 'master_product.item_form_description', 'master_product.is_approve', 'master_product.inserted_by', 'master_product.updated_by as updated_by', 'users.name as username']);

			if (Auth::user()->role == '3') {
				$dataget->where('master_product.inserted_by', Auth::user()->id);
			}
			if ($request->etin_filter2 != '') {
				if (isset($request->etin_filter2)) {
					$dataget->whereIn('ETIN', $request->etin_filter2);
				}
			}
			if ($request->listing_name_filter2 != '') {
				if (isset($request->listing_name_filter2)) {
					$dataget->whereIn('product_listing_name', $request->listing_name_filter2);
				}
			}
			if ($request->brand_filter2 != '') {
				if (isset($request->brand_filter2)) {
					$dataget->whereIn('brand', $request->brand_filter2);
				}
			}
			if ($request->manufacturer_filter2 != '') {
				if (isset($request->manufacturer_filter2)) {
					$dataget->whereIn('manufacturer', $request->manufacturer_filter2);
				}
			}

			if ($request->supplier_filter2 != '') {
				if (isset($request->supplier_filter2)) {
					$dataget->whereIn('current_supplier', $request->supplier_filter2);
				}
			}

			if ($request->unit_description_filter2 != '') {
				if (isset($request->unit_description_filter2)) {
					$dataget->whereIn('unit_description', $request->unit_description_filter2);
				}
			}

			if ($request->product_filter2 != '') {
				if (isset($request->product_filter2)) {
					$dataget->whereIn('product_type', $request->product_filter2);
				}
			}

			if ($request->upc_filter2 != '') {
				if (isset($request->upc_filter2)) {
					$dataget->whereIn('upc', $request->upc_filter2);
				}
			}

			if ($request->item_form_desc_filter2 != '') {
				if (isset($request->item_form_desc_filter2)) {
					$dataget->whereIn('item_form_description', $request->item_form_desc_filter2);
				}
			}

			$dataget->orderBy('master_product.created_at', 'desc');

			$total = $dataget->count();
			$limit = 10;
			if (isset($input['limit'])) $limit = $input['limit'];

			$page = 1;
			if (isset($input['page'])) $page = $input['page'];

			$offset = $request->get('start');
			$limit = $request->get('length');
			$dataget->skip($offset)->take($limit);
			$data = $dataget->get();

			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_approve', function ($data) {
					return ($data->is_approve == '0') ? "No" : "Yes";
				})
				->addColumn('inserted_by', function ($data) {
					$inserted_by = '';
					if ($data->inserted_by != NULL) {
						$pro = DB::table('users')->select('name')->where('id', $data->inserted_by)->first();
						if ($pro) {
							$inserted_by = $pro->name;
						}
					}
					return $inserted_by;
				})
				->addIndexColumn()
				->addColumn('action', function ($row) {
					$btn = '';
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
					} else {
						if (ReadWriteAccess('NewProductsPendingApprovalEditProduct')) {
							$btn = '<a href="' . route('editmasterproduct', $row->id) . '" class="edit btn btn-primary btn-sm">Edit Product</a>';
						}
					}
					return $btn;
				})
				->addColumn('approve_check', function ($row) {
					$checkbox = '<input class="form-check-input newApproveCheckBox" style="margin-left:-5px" type="checkbox" id="new_approve_' . $row->id . '" name="new_approve[]" value="' . $row->id . '">';
					return $checkbox;
				})
				->rawColumns(['action', 'approve_check', 'inserted_by'])
				->setTotalRecords($total)
				->setFilteredRecords($total)
				->skipPaging()
				->make(true);
		}
	}

	public function getmasterproductsbyclient(Request $request, $id)
	{
		if ($request->ajax()) {
			$client = Client::find($id);
			$dataget = DB::table('master_product')->where('is_approve', 1)->whereRaw('FIND_IN_SET(' . $id . ',lobs)')
				->select('master_product.*');
			if ($request->etin_filter != '') {
				if (isset($request->etin_filter)) {
					$dataget->whereIn('id', $request->etin_filter);
				}
			}
			if ($request->listing_name_filter != '') {
				if (isset($request->listing_name_filter)) {
					$dataget->whereIn('product_listing_name', $request->listing_name_filter);
				}
			}
			if ($request->brand_filter != '') {
				if (isset($request->brand_filter)) {
					$dataget->whereIn('brand', $request->brand_filter);
				}
			}
			if ($request->manufacturer_filter != '') {
				if (isset($request->manufacturer_filter)) {
					$dataget->whereIn('manufacturer', $request->manufacturer_filter);
				}
			}

			if ($request->supplier_filter != '') {
				if (isset($request->supplier_filter)) {
					$dataget->whereIn('current_supplier', $request->supplier_filter);
				}
			}

			if ($request->unit_description_filter != '') {
				if (isset($request->unit_description_filter)) {
					$dataget->whereIn('unit_description', $request->unit_description_filter);
				}
			}

			if ($request->product_filter != '') {
				if (isset($request->product_filter)) {
					$dataget->whereIn('product_type', $request->product_filter);
				}
			}

			if ($request->upc_filter != '') {
				if (isset($request->upc_filter)) {
					$dataget->whereIn('upc', $request->upc_filter);
				}
			}
			if ($request->status_filter != '') {
				if (isset($request->status_filter)) {
					$dataget->whereIn('status', $request->status_filter);
				}
			}
			$data = $dataget->get();
			$formula = null;
			$groupFormula = null;
			$formulaCalculation = null;
			$priceGroup = null;
			$carrierId = null;
			$miscValue = DB::table('misc_cost_values')->pluck('value', 'column_name')->toArray();

			if ($request->price_group) {
				$priceGroup = PriceGroup::find($request->price_group);

				if(isset($priceGroup->priceGroupCostBlock->cost_block) && $priceGroup->priceGroupCostBlock->cost_block != '[]' ){
					$formula = json_decode($priceGroup->priceGroupCostBlock->cost_block);
				}
				
				$groupFormula = $priceGroup->group_formulas;
				$formulaCalculation = $groupFormula->pluck('group_formula', 'formula_for');
				$carrierId = $priceGroup->carrier_id;
			}
			$query1 = DB::table('carrier_standard_fees')->where('carrier_id', $carrierId)->select('weight_gt_50_lbs_3', 'residential_surcharge_ground', 'continental_us_ground', 'residential_extended_ground', 'residential_ground')->first();
			// $residentialSurchargeGround = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_surcharge_ground')->first();
			// $continentalUsGround = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('continental_us_ground')->first();
			// $residentialExtendedGround = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_extended_ground')->first();
			// $query1 = DB::table('carrier_standard_fees')->where('carrier_id',$carrierId)->select('residential_ground')->first();
			$query = DB::table('carrier_peak_surchrges')->where('carrier_id',$carrierId)->select('ground_residential','additional_handling')->where('effective_date', '<', date('Y-m-d'))->where('end_date', '>', date('Y-m-d'))->first();

			$query2 = DB::table('carrier_dynamic_fees')->where('carrier_id',$carrierId)->where('effective_date', '<', date('Y-m-d'))->select('ground')->orderBy('effective_date','DESC')->first();

            return Datatables::of($data)
					->addIndexColumn()
					->editColumn('is_approve', function ($data) {
								return  ($data->is_approve == '0')?"No":"Yes";
							})
					->addIndexColumn()
                    ->addColumn('group_price', function($row) use ($formula,$groupFormula,$formulaCalculation,$carrierId,$priceGroup,$miscValue,$query1,$query,$query2){
                    	if($formula){
                    		$fuelSurcharge = 0;
                    		$priceBlock=0;
	                    	$costPrice = 0;
							$weight = 0;
							$warehouseAssigned = count(explode(',',$row->warehouses_assigned));

							Log::channel('pricegroup')->info('ETIN: '.$row->ETIN);

	                        foreach($formula as $formulaKey => $formulaVal){
								

	                        	if($formulaKey == 'total_product_cost')
	                        	{
									Log::channel('pricegroup')->info('Before total_product_cost price: '.$costPrice);
	                        		
									$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);
									
									Log::channel('pricegroup')->info('After total_product_cost price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'acquisition_cost')
	                        	{
									Log::channel('pricegroup')->info('Before acquisition_cost price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

									Log::channel('pricegroup')->info('After acquisition_cost price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'product_cost')
	                        	{
									Log::channel('pricegroup')->info('Before product_cost price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

									Log::channel('pricegroup')->info('After product_cost price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'coolant_cost')
	                        	{
	                        		
	                        		Log::channel('pricegroup')->info('Before coolant_cost price: '.$costPrice);
	                        		
	                        		$costPrice += $this->priceGroupService->packagingCoolantCostOpt($row,$formulaVal,$groupFormula,$miscValue);

	                        		Log::channel('pricegroup')->info('After coolant_cost price: '.$costPrice);
	                        		
	                        	}
								if($formulaKey == 'packaging_and_material')
	                        	{
	                        		
	                        		Log::channel('pricegroup')->info('Before packaging_and_material price: '.$costPrice);
	                        		
	                        		$costPrice += $this->priceGroupService->packagingMateriaCost($row,$formulaVal,$groupFormula,$miscValue);

	                        		Log::channel('pricegroup')->info('After packaging_and_material  price: '.$costPrice);
	                        		
	                        	}
	                        	if($formulaKey == 'additional_handling')
	                        	{
									Log::channel('pricegroup')->info('Before additional_handling price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

									Log::channel('pricegroup')->info('After additional_handling price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'residential_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before residential_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

									Log::channel('pricegroup')->info('After residential_surcharge price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'remote_area_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before remote_area_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('After remote_area_surcharge price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'delivery_area_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before delivery_area_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('After delivery_area_surcharge price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'extended_delivery_area_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before extended_delivery_area_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('After price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'peak_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before peak_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->peakSurchargeCostOpt($query);

									Log::channel('pricegroup')->info('After peak_surcharge price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'peak_additional_surcharge')
	                        	{
									Log::channel('pricegroup')->info('Before peak_additional_surcharge price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);

									Log::channel('pricegroup')->info('After peak_additional_surcharge price: '.$costPrice);
	                        	}
								
	                        	if($formulaKey == 'base_shipping_cost')
	                        	{
									Log::channel('pricegroup')->info('Before base_shipping_cost price: '.$costPrice);

	                        		$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
	                        		
	                        		$costPrice += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

									Log::channel('pricegroup')->info('After base_shipping_cost price: '.$costPrice);
	                        	}
	                        	if($formulaKey == 'total_shipping')
	                        	{
									Log::channel('pricegroup')->info('Before total_shipping price: '.$costPrice);


	                        		$costPrice += $this->priceGroupService->totalShippingCostOpt($row,$miscValue,$query,$query1,$formulaCalculation,$query2);

									Log::channel('pricegroup')->info('After total_shipping price: '.$costPrice);
	                        	}
	                        	if($formulaKey == "misc_fees_and_Charges")
	                        	{
									Log::channel('pricegroup')->info('Before misc_fees_and_Charges price: '.$costPrice);

	                        		$costPrice += $this->priceGroupService->miscFeesAndChargesOpt($row,$miscValue);	

									Log::channel('pricegroup')->info('After misc_fees_and_Charges price: '.$costPrice);
	                        	}
	                        	if($formulaKey == "credit_card_fees")
	                        	{
									Log::channel('pricegroup')->info('Before credit_card_fees price: '.$costPrice);

	                        		$costPrice += ($priceGroup->credit_card_fees * $row->cost)/100;

									Log::channel('pricegroup')->info('After credit_card_fees price: '.$costPrice);
	                        	}
	                        	if($formulaKey == "marketplace_fees")
	                        	{
									Log::channel('pricegroup')->info('Before marketplace_fees price: '.$costPrice);

	                        		$costPrice += ($priceGroup->marketplace_fees * $row->cost)/100;

									Log::channel('pricegroup')->info('After marketplace_fees price: '.$costPrice);
	                        	}
	                        	if($formulaKey == "weight_multiplier")
	                        	{
									Log::channel('pricegroup')->info('Before weight_multiplier price: '.$costPrice);

	                        		$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
	                        		$costPrice += $priceGroup->weight_multiplier * $weight;

									Log::channel('pricegroup')->info('After weight_multiplier price: '.$costPrice);
	                        	}
	          				}

	          				foreach($formula as $formulaKey => $formulaVal){
	          					if($formulaKey == "fuel_surcharge"){

									Log::channel('pricegroup')->info('Before fuel_surcharge price: '.$costPrice);

	          						$fuelSurcharge += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

									Log::channel('pricegroup')->info('baseShippingCost fuel_surcharge price: '.$fuelSurcharge);

	          						$fuelSurcharge += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

									Log::channel('pricegroup')->info('additionalHandlingCostOpt fuel_surcharge price: '.$fuelSurcharge);

	          						$fuelSurcharge += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

									Log::channel('pricegroup')->info('residentialSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$fuelSurcharge += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('remoteAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$fuelSurcharge += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('deliveryAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$fuelSurcharge += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									Log::channel('pricegroup')->info('extendedDeliveryAreaSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$fuelSurcharge += $this->priceGroupService->peakSurchargeCostOpt($query);

									Log::channel('pricegroup')->info('peakSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$fuelSurcharge += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);
									
									Log::channel('pricegroup')->info('peakAdditionalSurchargeCostOpt fuel_surcharge price: '.$fuelSurcharge);
	          						
	          						$costPrice +=  $this->priceGroupService->fuelSurchargeOpt($fuelSurcharge,$query2);

									Log::channel('pricegroup')->info('After fuel_surcharge price: '.$costPrice);
	          					}
	          				}

							//Total Cost
							foreach($formula as $formulaKey => $formulaVal){
								if($formulaKey == "total_cost"){
									Log::channel('pricegroup')->info('Before total_cost price: '.$costPrice);
									$costPrice += $this->priceGroupService->masterProductCost($row,$formulaVal);

									$costPrice += $this->priceGroupService->packagingCoolantCostOpt($row,$formulaVal,$groupFormula,$miscValue);

									$costPrice += $this->priceGroupService->packagingMateriaCost($row,$formulaVal,$groupFormula,$miscValue);

									$costPrice += $this->priceGroupService->additionalHandlingCostOpt($miscValue,$query1);

									$costPrice += $this->priceGroupService->residentialSurchargeCostOpt($formulaCalculation,$query1);

									$costPrice += $this->priceGroupService->remoteAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									$costPrice += $this->priceGroupService->deliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									$costPrice += $this->priceGroupService->extendedDeliveryAreaSurchargeCostOpt($miscValue,$formulaCalculation,$query1);

									$costPrice += $this->priceGroupService->peakSurchargeCostOpt($query);

									$costPrice += $this->priceGroupService->peakAdditionalSurchargeCostOpt($miscValue,$query);

									$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
									$warehouseAssigned = count(explode(',',$row->warehouses_assigned));

									$costPrice += $this->priceGroupService->baseShippingCostOpt($row,$weight,$warehouseAssigned,$miscValue);

									$costPrice += $this->priceGroupService->miscFeesAndChargesOpt($row,$miscValue);

									$costPrice += ($priceGroup->credit_card_fees * $row->cost)/100;

									$costPrice += ($priceGroup->marketplace_fees * $row->cost)/100;

									$weight = $row->weight > $row->dimensional_weight ? ceil($row->weight) : ceil($row->dimensional_weight);
									$costPrice += $priceGroup->weight_multiplier * $weight;

									Log::channel('pricegroup')->info('After total_cost price: '.$costPrice);
								}
							}

							$priceBlock = $costPrice;

	          				foreach($formula as $formulaKey => $formulaVal){
	          					if($formulaKey == "markup_price_group")
	                        	{
									Log::channel('pricegroup')->info('Before price block markup_price_group: '.$priceBlock);

	                        		$priceBlock += (floatval($priceGroup->markup_price_group) * $costPrice)/100;

									Log::channel('pricegroup')->info('After price block markup_price_group: '.$priceBlock);
	                        	}
	                        	if($formulaKey == "markup_total_cost")
	                        	{
									Log::channel('pricegroup')->info('Before price block markup_total_cost: '.$priceBlock);

	                        		$priceBlock += (floatval($priceGroup->markup_total_cost) * $costPrice) / 100;

									Log::channel('pricegroup')->info('After price block markup_total_cost: '.$priceBlock);
	                        	}
	                        	if($formulaKey == "markup_product_materials_cost")
	                        	{

									Log::channel('pricegroup')->info('Before price block markup_product_materials_cost: '.$priceBlock);

	                        		$priceBlock += (floatval($priceGroup->markup_product_materials_cost) * $costPrice)/100;

									Log::channel('pricegroup')->info('After price block markup_product_materials_cost: '.$priceBlock);
	                        	}	
	          				}

							  Log::channel('pricegroup')->info('Final Price Cost: '.$priceBlock);

							Log::channel('pricegroup')->info('*********************************************');
	          				
							return round($priceBlock,2);
                    	}
                    	return $row->cost;
                    })
                    ->addColumn('channel', function($row){
                        return '-';
                    })
                    ->addColumn('inventory', function($row){
                        return '-';
                    })
                    ->addColumn('action', function($row){
							$btn = '';
							if($row->item_form_description == 'Kit'){
								$btn = '<a href="'.route('kits.edit',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
							}else{
								if(ReadWriteAccess('ActiveProductListingsEditProduct')){
									$btn = '<a href="'.route('editmasterproduct',$row->id).'"  class="edit btn btn-primary btn-sm">Edit Product</a>';
								}
							}
							
					return $row->cost;
				})
				->addColumn('channel', function ($row) {
					return '-';
				})
				->addColumn('inventory', function ($row) {
					return '-';
				})
				->editColumn('ETIN',function($data){
					return '<a href="javascript:void(0)" onClick="getNoteCreateModal(\''.route('clients.get_product_warehouse_qty',$data->ETIN).'\')">'.$data->ETIN.'</a>';
				})
				->addColumn('action', function ($row) {
					$btn = '';
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
					} else {
						if (ReadWriteAccess('ActiveProductListingsEditProduct')) {
							$btn = '<a href="' . route('editmasterproduct', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Product</a>';
						}
					}

					return $btn;
				})
				->rawColumns(['channel', 'inventory', 'action','ETIN'])
				->make(true);
		}
	}

	public function geteditedmasterproducts(Request $request)
	{
		if ($request->ajax()) {
			$dataget = DB::table('master_product_queue')->where(
				'queue_status',
				'e'
			)->leftJoin('users', function ($join) {
				$join->on('users.id', '=', 'master_product_queue.updated_by');
			})->select(['master_product_queue.id', 'master_product_queue.ETIN', 'master_product_queue.product_listing_name', 'master_product_queue.brand', 'master_product_queue.current_supplier', 'master_product_queue.inserted_by', 'master_product_queue.upc', 'master_product_queue.item_form_description', 'master_product_queue.is_edit', 'users.name as username']);

			if (Auth::user()->role == '3') {
				$dataget->where('master_product_queue.inserted_by', Auth::user()->id);
			}

			if ($request->etin_filter3 != '') {
				if (isset($request->etin_filter3)) {
					$dataget->whereIn('ETIN', $request->etin_filter3);
				}
			}
			if ($request->listing_name_filter3 != '') {
				if (isset($request->listing_name_filter3)) {
					$dataget->whereIn('product_listing_name', $request->listing_name_filter3);
				}
			}
			if ($request->brand_filter3 != '') {
				if (isset($request->brand_filter3)) {
					$dataget->whereIn('brand', $request->brand_filter3);
				}
			}
			if ($request->manufacturer_filter3 != '') {
				if (isset($request->manufacturer_filter3)) {
					$dataget->whereIn('manufacturer', $request->manufacturer_filter3);
				}
			}
			if ($request->supplier_filter3 != '') {
				if (isset($request->supplier_filter3)) {
					$dataget->whereIn('current_supplier', $request->supplier_filter3);
				}
			}
			if ($request->unit_description_filter3 != '') {
				if (isset($request->unit_description_filter3)) {
					$dataget->whereIn('unit_description', $request->unit_description_filter3);
				}
			}
			if ($request->product_filter3 != '') {
				if (isset($request->product_filter3)) {
					$dataget->whereIn('product_type', $request->product_filter3);
				}
			}
			if ($request->upc_filter3 != '') {
				if (isset($request->upc_filter3)) {
					$dataget->whereIn('upc', $request->upc_filter3);
				}
			}
			if ($request->item_form_desc_filter3 != '') {
				if (isset($request->item_form_desc_filter3)) {
					$dataget->whereIn('item_form_description', $request->item_form_desc_filter3);
				}
			}
			$data = $dataget->orderBy('master_product_queue.updated_at', 'desc')->get();
			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_edit', function ($data) {
					return ($data->is_edit == '0') ? "No" : "Yes";
				})
				->addColumn('inserted_by', function ($data) {
					$inserted_by = '';
					if ($data->inserted_by != NULL) {
						$pro = DB::table('users')->select('name')->where('id', $data->inserted_by)->first();
						if ($pro) {
							$inserted_by = $pro->name;
						}
					}
					return $inserted_by;
				})
				->addIndexColumn()
				->addColumn('action', function ($row) {
					$btn = '';
					// if(Auth::user()->role<=2){
					if ($row->item_form_description == 'Kit') {
						$btn = '<a href="' . route('kits.edit_request', $row->id) . '"  class="edit btn btn-primary btn-sm">Edit Kit Request</a>';
					} else {
						if (ReadWriteAccess('ProductEditsPendingApprovalProductEditRequest')) {
							$btn = '<a href="' . route('editmasterrequestview', $row->id) . '" class="edit btn btn-primary btn-sm">Product Edit Request</a>';
						}
					}
					//$btn = '<a href="javascript:void(0)" onclick="syncwithmaster()" id="syncwithmaster" class="edit btn btn-primary btn-sm">Sync</a>';

					// }

					return $btn;
				})
				->addColumn('approve_check', function ($row) {
					$checkbox = '<input class="form-check-input editApproveCheckBox" style="margin-left:-5px" type="checkbox" id="edit_approve_' . $row->id . '" name="edit_approve[]" value="' . $row->id . '">';
					return $checkbox;
				})
				->rawColumns(['action', 'approve_check', 'inserted_by'])
				// ->rawColumns(['action'])
				->make(true);
		}
	}

	public function getaddedmasterproducts(Request $request)
	{
		if ($request->ajax()) {
			$dataget = MasterProductQueue::where('queue_status', 'd')->leftJoin('users', function ($join) {
				$join->on('users.id', '=', 'master_product_queue.updated_by');
			})->select(['master_product_queue.id', 'master_product_queue.ETIN', 'master_product_queue.product_listing_name', 'master_product_queue.brand', 'master_product_queue.current_supplier', 'master_product_queue.inserted_by', 'master_product_queue.upc', 'master_product_queue.item_form_description', 'master_product_queue.is_edit', 'users.name as username']);

			if (Auth::user()->role == '3') {
				$dataget->where('master_product_queue.inserted_by', Auth::user()->id);
			}

			if ($request->etin_filter4 != '') {
				if (isset($request->etin_filter4)) {
					$dataget->whereIn('ETIN', $request->etin_filter4);
				}
			}
			if ($request->listing_name_filter4 != '') {
				if (isset($request->listing_name_filter4)) {
					$dataget->whereIn('product_listing_name', $request->listing_name_filter4);
				}
			}
			if ($request->brand_filter4 != '') {
				if (isset($request->brand_filter4)) {
					$dataget->whereIn('brand', $request->brand_filter4);
				}
			}
			if ($request->manufacturer_filter4 != '') {
				if (isset($request->manufacturer_filter4)) {
					$dataget->whereIn('manufacturer', $request->manufacturer_filter4);
				}
			}

			if ($request->supplier_filter4 != '') {
				if (isset($request->supplier_filter4)) {
					$dataget->whereIn('current_supplier', $request->supplier_filter4);
				}
			}

			if ($request->unit_description_filter4 != '') {
				if (isset($request->unit_description_filter4)) {
					$dataget->whereIn('unit_description', $request->unit_description_filter4);
				}
			}
			if ($request->product_filter4 != '') {
				if (isset($request->product_filter4)) {
					$dataget->whereIn('product_type', $request->product_filter4);
				}
			}

			if ($request->upc_filter4 != '') {
				if (isset($request->upc_filter4)) {
					$dataget->whereIn('upc', $request->upc_filter4);
				}
			}

			if ($request->item_form_desc_filter4 != '') {
				if (isset($request->item_form_desc_filter4)) {
					$dataget->whereIn('item_form_description', $request->item_form_desc_filter4);
				}
			}

			$total = $dataget->count();
			$limit = 10;
			if (isset($input['limit'])) $limit = $input['limit'];
			$page = 1;
			if (isset($input['page'])) $page = $input['page'];

			$offset = $request->get('start');
			$limit = $request->get('length');
			$dataget->skip($offset)->take($limit);
			$data = $dataget->orderBy('master_product_queue.updated_at', 'desc')->get();
			return Datatables::of($data)
				->addIndexColumn()
				->editColumn('is_edit', function ($data) {
					return ($data->is_edit == '0') ? "No" : "Yes";
				})

				->addColumn('inserted_by', function ($data) {
					$inserted_by = '';
					if ($data->inserted_by != NULL) {
						$pro = DB::table('users')->select('name')->where('id', $data->inserted_by)->first();
						if ($pro) {
							$inserted_by = $pro->name;
						}
					}
					return $inserted_by;
				})

				->addIndexColumn()
				->addColumn('action', function ($row) {
					$btn = '';
					// if(Auth::user()->role<=2){
					if (ReadWriteAccess('ProductDraftsEditProduct')) {
						$btn = '<a href="' . route('editmasterrequestview', $row->id) . '"  _onclick="addedmasterproduct()" id="addedmasterproduct" class="edit btn btn-primary btn-sm">Product Edit Request</a>';
					}

					$btn .= ' <a href="' . route('deletemasterproductdraft', $row->id) . '" onclick="return confirm(\'are you sure?\')" id="deleteProductFromQueue" class="delete btn btn-danger btn-sm">Delete</a>';

					//$btn = '<a href="javascript:void(0)" onclick="syncwithmaster()" id="syncwithmaster" class="edit btn btn-primary btn-sm">Sync</a>';

					// }
					return $btn;
				})
				->rawColumns(['action', 'inserted_by'])
				->setTotalRecords($total)
				->setFilteredRecords($total)
				->skipPaging()
				->make(true);
		}
	}

	public function editmasterview(Request $request,$id,$tab=null)
	{
		$productimg = array();
		$productimgurlarray = array();
		$productimgtxtarray = array();
		$producthistory = array();

		$productid = $request->id;
		$productdetails = DB::table('master_product')->find($productid);
		if ($productdetails->is_approve == 1) {
			if (ReadWriteAccess('ActiveProductListingsEditProduct') == false) {
				return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
			}
		}
		if ($productdetails->is_approve == 0) {
			if (ReadWriteAccess('NewProductsPendingApprovalEditProduct') == false) {
				return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
			}
		}

		$nextProductId = $this->masterProduct->getMasterProductNextId($productid);
		$prevProductId = $this->masterProduct->getMasterProductPrevId($productid);

		$producthistory = DB::table('master_product_history')->where('ETIN', $productdetails->ETIN)->orderBy('id', 'DESC')->first();
		$product_request_details = DB::table('master_product_queue')->where('master_product_id', $productid)->first();
		if (isset($productdetails->product_temperature)) {
			$explodearray = explode('-', $productdetails->ETIN);
			if (count($explodearray) > 1) {
				$productdetails->ETIN = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($productdetails->product_temperature == "Frozen") {
				$productdetails->ETIN = 'ETFZ-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Strong") {
				$productdetails->ETIN = 'ETDS-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Refrigerated") {
				$productdetails->ETIN = 'ETRF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Beverages") {
				$productdetails->ETIN = 'ETBV-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Perishable") {
				$productdetails->ETIN = 'ETDP-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Fragile") {
				$productdetails->ETIN = 'ETDF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Thaw & Serv") {
				$productdetails->ETIN = 'ETTS-' . $etinmid . '-' . $productdetails->ETIN;
			} else {
				$productdetails->ETIN = 'ETOT-' . $etinmid . '-' . $productdetails->ETIN;
			}
		}
		//$productdetails->lobs = explode(',',$productdetails->lobs);


		$productdetails->current_supplier_name = DB::table('suppliers')->where('id', $productdetails->current_supplier)->value('name');
		$productdetails->unit_list = '';
		$productdetails->unit_num = '';
		if ($productdetails->unit_size) {
			$unitarray = explode('-', $productdetails->unit_size);
			if (!empty($unitarray)) {
				$productdetails->unit_num = isset($unitarray[0]) ? $unitarray[0] : '';
				$productdetails->unit_list = isset($unitarray[1]) ? $unitarray[1] : '';
			}
		}
		$productetin = $productdetails->ETIN;
		$productimgurl = DB::table('product_images')->select('Image_URL1_Primary', 'Image_URL2_Front', 'Image_URL3_Back', 'Image_URL4_Left', 'Image_URL5_Right', 'Image_URL6_Top', 'Image_URL7_Bottom', 'Image_URL8', 'Image_URL9', 'Image_URL10', 'Nutritional_Image_URL1', 'Nutritional_Image_URL2')->where('ETIN', $productetin)->get();

		$productimgtxt = DB::table('product_images')->select('Image_URL1_Alt_Text', 'Image_URL2_Alt_Text', 'Image_URL3_Alt_Text', 'Image_URL4_Alt_Text', 'Image_URL5_Alt_Text', 'Image_URL6_Alt_Text', 'Image_URL7_Alt_Text', 'Image_URL8_Alt_Text', 'Image_URL9_Alt_Text', 'Image_URL10_Alt_Text', 'Nutritional_Image_URL1_Alt_Text', 'Nutritional_Image_URL2_Alt_Text')->where('ETIN', $productetin)->get();


		foreach ($productimgurl as $key => $value) {
			foreach ($value as $field => $url) {
				$productimgurlarray[] = $url;
			}
		}
		foreach ($productimgtxt as $key => $value) {
			foreach ($value as $field => $text) {
				$productimgtxtarray[] = $text;
			}
		}
		if ($productimgurlarray && $productimgtxtarray) {
			$productimg = array_combine($productimgurlarray, $productimgtxtarray);
		}
		$productinventory = DB::table('product_inventory')->where('ETIN', $productetin)->first();

		//parent etin
		$productetin = $productdetails->ETIN;
		$productChild = DB::table('master_product')->where('parent_ETIN', $productetin)->get();

		//suplimental_data
		$suplimental_data = DB::table('supplemental_mpt_data')->where('master_product_id', $productid)->first();


		//Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand', 'ASC')->groupBy('brand')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		//Getting all Manufacturer Name list
		$getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name', 'ASC')->get();
		foreach ($getmanufacturer as $manufacturers) {
			$manufacturer[] = $manufacturers->manufacturer_name;
		}

		//Getting all Category list
		$categories = DB::table('categories')->where('level', 0)->orderBy('name', 'ASC')->get();


		//Getting all Product Type list
		$getproducttype = DB::table('product_type')->orderBy('product_type', 'ASC')->get();
		foreach ($getproducttype as $producttypes) {
			$producttype[] = $producttypes->product_type;
		}

		//Getting all Unit Size list
		$getunitsizes = DB::table('unit_sizes')->orderBy('unit', 'ASC')->get();
		foreach ($getunitsizes as $unitsizes) {
			$unitname[] = $unitsizes->unit;
			$unitabb[] = $unitsizes->abbreviation;
			$unitsize = array_combine($unitabb, $unitname);
		}

		//Getting all Unit Description list
		$getunitdesc = DB::table('unit_desc')->orderBy('unit_description', 'ASC')->get();
		foreach ($getunitdesc as $unitdescs) {
			$unitdesc[] = $unitdescs->unit_description;
		}

		//Getting all Product Tags list
		$getproducttags = DB::table('product_tags')->orderBy('tag')->get();
		foreach ($getproducttags as $producttags) {
			$producttag[$producttags->id] = $producttags->tag;
		}

		//Getting all Product Temparaure list
		$getproducttemp = DB::table('product_temp')->orderBy('product_temperature', 'ASC')->get();
		foreach ($getproducttemp as $producttemps) {
			$producttemp[] = $producttemps->product_temperature;
		}

		//Getting all Suppliers list
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name', 'ASC')->get();
		foreach ($getsuppliers as $suppliers) {
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine($supplier_id, $supplier_name);
		}

		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin')->get();
		foreach ($getcountries as $countries) {
			$country[$countries->id] = $countries->country_of_origin;
		}

		//Getting all Item From Description list
		$getitemsdescs = DB::table('item_from_description')->orderBy('item_desc', 'ASC')->get();
		foreach ($getitemsdescs as $itemsdescs) {
			$itemsdesc[] = $itemsdescs->item_desc;
		}

		//Getting all Clients list
		$getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();
		foreach ($getclients as $clients) {
			$client[$clients->id] = $clients->company_name;
		}

		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->orderBy('etailer_availability', 'ASC')->get();
		foreach ($getetailers as $etailerlist) {
			$etailers[$etailerlist->id] = $etailerlist->etailer_availability;
		}
		//Getting all Warehouse list
		$getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();
		
		$onHandQty = [];
		$AvailQty = [];
		foreach ($getwarehouses as $warehouselist) {
			$count = 0;
			$AisleMaster = AisleMaster::where('warehouse_id',$warehouselist->id)->pluck('id')->toArray();
        	$masterShelfSum = MasterShelf::where('ETIN',$productdetails->ETIN )
				// ->whereIn('location_type_id', [1, 2])
				->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
			if (isset($productdetails->parent_ETIN) && $productdetails->item_form_description !== 'Kit') {		
				$etin = $productdetails->parent_ETIN;
				$units_in_pack_child = $productdetails->unit_in_pack;
	
				$parent = MasterProduct::where('ETIN', $etin)->first();
				if($parent){
					$units_in_pack_parent = $parent->unit_in_pack;

					$masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
					if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
						$count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
					}
					$masterShelfSum = $masterShelfSum + $count;
				}                    
			}
			$OD = new OrderDetail;
        	$GetAvailableQty = $OD->GetAvailableQty($productdetails->ETIN, $warehouselist->warehouses);
			$AVQ = $masterShelfSum - $GetAvailableQty;
			$onHandQty[$warehouselist->id] =  $masterShelfSum;
			$warehouse[$warehouselist->id] = $warehouselist->warehouses;
			$AvailQty[$warehouselist->id] =  $AVQ < 0 ? 0 : $AVQ;
		}

		//Getting all Supplier Status
		$supplier_status = SupplierStatus::orderBy('supplier_status', 'ASC')->get();

		//Getting all prop_ingredients list
		$prop_ingredients = [];
		$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients', 'ASC')->get();
		foreach ($getprop_ingredients as $productprops) {
			$prop_ingredients[$productprops->id] = $productprops->prop_ingredients;
		}

		//Getting all allergens list
		$allergens = [];
		$getallergens = DB::table('allergens')->orderBy('allergens')->get();
		foreach ($getallergens as $row_allergens) {
			$allergens[$row_allergens->id] = $row_allergens->allergens;
		}

		//Get Images
		$product_images = DB::table('master_product_images')->where('ETIN', $productetin)->get();
		$image_type = DB::table('image_type')->get();
		$product_status = DB::table('product_statuses')->get();
		return view('cranium.supplierProdListing.masterproductedit', 
			[
				'productdetails' => $productdetails, 
				'suplimental_data' => $suplimental_data, 
				'productChild' => $productChild, 
				'producthistory' => $producthistory, 
				'brand' => $brand, 
				'manufacturer' => $manufacturer, 
				'categories' => $categories, 
				'productimg' => $productimg, 
				'productinventory' => $productinventory, 
				'producttype' => $producttype, 
				'unitsize' => $unitsize, 
				'unitdesc' => $unitdesc, 
				'producttag' => $producttag, 
				'producttemp' => $producttemp, 
				'supplier' => $supplier, 
				'country' => $country, 
				'itemsdesc' => $itemsdesc, 
				'client' => $client, 
				'etailers' => $etailers, 
				'warehouse' => $warehouse, 
				'supplier_status' => $supplier_status, 
				'product_request_details' => $product_request_details, 
				'allergens' => $allergens, 
				'prop_ingredients' => $prop_ingredients, 
				'product_images' => $product_images, 
				'image_types' => $image_type, 
				'nextProductId' => $nextProductId, 
				'prevProductId' => $prevProductId, 
				'product_status' => $product_status,
				'onHandQty'=>$onHandQty, 
				'AvailQty'=>$AvailQty, 
				'tab' => $tab]);
	}

	public function editmasterrequestview(Request $request)
	{
		$productimg = array();
		$productimgurlarray = array();
		$productimgtxtarray = array();
		$producthistory = array();

		$productid = $request->id;
		$productdetails = DB::table('master_product_queue')->find($productid);
		if ($productdetails->queue_status == 'e') {
			if (ReadWriteAccess('ProductEditsPendingApprovalProductEditRequest') == false) {
				return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
			}
		}
		if ($productdetails->queue_status == 'd') {
			if (ReadWriteAccess('ProductDraftsEditProduct') == false) {
				return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
			}
		}

		$nextProductId = $this->masterProduct->getMasterProductQueueNextId($productid);
		$prevProductId = $this->masterProduct->getMasterProductQueuePrevId($productid);

		$nextDraftProductId = $this->masterProduct->getDraftProductNextId($productid);
		$prevDraftProductId = $this->masterProduct->getDraftProductPrevId($productid);

		//suplimental_data
		$suplimental_data = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $productdetails->id)->first();

		$producthistory = DB::table('master_product_history')->where('ETIN', $productdetails->ETIN)->orderBy('id', 'DESC')->first();

		if (isset($productdetails->product_temperature)) {
			$explodearray = explode('-', $productdetails->ETIN);
			if (count($explodearray) > 1) {
				$productdetails->ETIN = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($productdetails->product_temperature == "Frozen") {
				$productdetails->ETIN = 'ETFZ-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Strong") {
				$productdetails->ETIN = 'ETDS-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Refrigerated") {
				$productdetails->ETIN = 'ETRF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Beverages") {
				$productdetails->ETIN = 'ETBV-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Perishable") {
				$productdetails->ETIN = 'ETDP-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Fragile") {
				$productdetails->ETIN = 'ETDF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Thaw & Serv") {
				$productdetails->ETIN = 'ETTS-' . $etinmid . '-' . $productdetails->ETIN;
			} else {
				$productdetails->ETIN = 'ETOT-' . $etinmid . '-' . $productdetails->ETIN;
			}
		}
		//$productdetails->lobs = explode(',',$productdetails->lobs);


		$productdetails->current_supplier_name = DB::table('suppliers')->where('id', $productdetails->current_supplier)->value('name');
		$productdetails->unit_list = '';
		$productdetails->unit_num = '';
		if ($productdetails->unit_size) {
			$unitarray = explode('-', $productdetails->unit_size);
			if (!empty($unitarray)) {
				$productdetails->unit_num = isset($unitarray[0]) ? $unitarray[0] : '';
				$productdetails->unit_list = isset($unitarray[1]) ? $unitarray[1] : '';
			}
		}

		$productetin = $productdetails->ETIN;
		$productimgurl = DB::table('product_images_queue')->select('Image_URL1_Primary', 'Image_URL2_Front', 'Image_URL3_Back', 'Image_URL4_Left', 'Image_URL5_Right', 'Image_URL6_Top', 'Image_URL7_Bottom', 'Image_URL8', 'Image_URL9', 'Image_URL10', 'Nutritional_Image_URL1', 'Nutritional_Image_URL2')->where('ETIN', $productetin)->get();

		$productimgtxt = DB::table('product_images_queue')->select('Image_URL1_Alt_Text', 'Image_URL2_Alt_Text', 'Image_URL3_Alt_Text', 'Image_URL4_Alt_Text', 'Image_URL5_Alt_Text', 'Image_URL6_Alt_Text', 'Image_URL7_Alt_Text', 'Image_URL8_Alt_Text', 'Image_URL9_Alt_Text', 'Image_URL10_Alt_Text', 'Nutritional_Image_URL1_Alt_Text', 'Nutritional_Image_URL2_Alt_Text')->where('ETIN', $productetin)->get();


		foreach ($productimgurl as $key => $value) {
			foreach ($value as $field => $url) {
				$productimgurlarray[] = $url;
			}
		}
		foreach ($productimgtxt as $key => $value) {
			foreach ($value as $field => $text) {
				$productimgtxtarray[] = $text;
			}
		}
		if ($productimgurlarray && $productimgtxtarray) {
			$productimg = array_combine($productimgurlarray, $productimgtxtarray);
		}
		$productinventory = DB::table('product_inventory_queue')->where('ETIN', $productetin)->first();

		//Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand', 'ASC')->groupBy('brand')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		//Getting all Manufacturer Name list
		$getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name', 'ASC')->get();
		foreach ($getmanufacturer as $manufacturers) {
			$manufacturer[] = $manufacturers->manufacturer_name;
		}

		//Getting all Category list
		//  $categories = DB::table('categories')->where('parent_id',0)->orderBy('name','ASC')->get();
		$categories = DB::table('categories')->where('level', 0)->orderBy('name', 'ASC')->get();

		//Getting all Product Type list
		$getproducttype = DB::table('product_type')->orderBy('product_type', 'ASC')->get();
		foreach ($getproducttype as $producttypes) {
			$producttype[] = $producttypes->product_type;
		}

		//Getting all Unit Size list
		$getunitsizes = DB::table('unit_sizes')->orderBy('unit', 'ASC')->get();
		foreach ($getunitsizes as $unitsizes) {
			$unitname[] = $unitsizes->unit;
			$unitabb[] = $unitsizes->abbreviation;
			$unitsize = array_combine($unitabb, $unitname);
		}

		//Getting all Unit Description list
		$getunitdesc = DB::table('unit_desc')->orderBy('unit_description', 'ASC')->get();
		foreach ($getunitdesc as $unitdescs) {
			$unitdesc[] = $unitdescs->unit_description;
		}

		//Getting all Product Tags list
		$getproducttags = DB::table('product_tags')->orderBy('tag')->get();
		foreach ($getproducttags as $producttags) {
			$producttag[$producttags->id] = $producttags->tag;
		}

		//Getting all Product Temparaure list
		$getproducttemp = DB::table('product_temp')->orderBy('product_temperature', 'ASC')->get();
		foreach ($getproducttemp as $producttemps) {
			$producttemp[] = $producttemps->product_temperature;
		}

		//Getting all Suppliers list
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name', 'ASC')->get();
		foreach ($getsuppliers as $suppliers) {
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine($supplier_id, $supplier_name);
		}

		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin')->get();
		foreach ($getcountries as $countries) {
			$country[$countries->id] = $countries->country_of_origin;
		}

		//Getting all Item From Description list
		$getitemsdescs = DB::table('item_from_description')->orderBy('item_desc', 'ASC')->get();
		foreach ($getitemsdescs as $itemsdescs) {
			$itemsdesc[] = $itemsdescs->item_desc;
		}

		//Getting all Clients list
		$getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();
		foreach ($getclients as $clients) {
			$client[$clients->id] = $clients->company_name;
		}

		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->orderBy('etailer_availability', 'ASC')->get();
		foreach ($getetailers as $etailerlist) {
			$etailers[$etailerlist->id] = $etailerlist->etailer_availability;
		}
		//Getting all Warehouse list
		$getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();
		foreach ($getwarehouses as $warehouselist) {
			$warehouse[] = $warehouselist->warehouses;
		}

		//Getting all prop_ingredients list
		$prop_ingredients = [];
		$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients', 'ASC')->get();
		foreach ($getprop_ingredients as $productprops) {
			$prop_ingredients[$productprops->id] = $productprops->prop_ingredients;
		}

		//Getting all allergens list
		$allergens = [];
		$getallergens = DB::table('allergens')->orderBy('allergens', 'ASC')->get();
		foreach ($getallergens as $row_allergens) {
			$allergens[$row_allergens->id] = $row_allergens->allergens;
		}

		//Getting all Supplier Status
		$supplier_status = SupplierStatus::orderBy('supplier_status', 'ASC')->get();

		//Get Images
		$product_images = DB::table('master_product_images')->leftJoin('image_type', 'image_type.id', '=', 'master_product_images.image_type')->select('master_product_images.*', DB::raw('image_type.image_type AS image_type_name'))->where('ETIN', $productetin)->get();

		$image_type = DB::table('image_type')->get();
		$image_type_count = DB::table('image_type')->count();
		$product_status = DB::table('product_statuses')->get();

		return view('cranium.supplierProdListing.editmasterrequestview', 
			[
				'productdetails' => $productdetails, 
				'producthistory' => $producthistory, 
				'brand' => $brand, 
				'manufacturer' => $manufacturer, 
				'categories' => $categories, 
				'productimg' => $productimg, 
				'productinventory' => $productinventory, 
				'producttype' => $producttype, 
				'unitsize' => $unitsize, 
				'unitdesc' => $unitdesc, 
				'producttag' => $producttag, 
				'producttemp' => $producttemp, 
				'supplier' => $supplier, 
				'country' => $country, 
				'itemsdesc' => $itemsdesc, 
				'client' => $client, 
				'etailers' => $etailers, 
				'warehouse' => $warehouse, 
				'supplier_status' => $supplier_status, 
				'prop_ingredients' => $prop_ingredients, 
				'allergens' => $allergens, 
				'suplimental_data' => $suplimental_data, 
				'product_images' => $product_images, 
				'image_types' => $image_type, 
				'image_type_count' => $image_type_count, 
				'nextProductId' => $nextProductId, 
				'prevProductId' => $prevProductId, 
				'nextDraftProductId' => $nextDraftProductId, 
				'prevDraftProductId' => $prevDraftProductId, 
				'product_status' => $product_status
			]);
	}

	public function reeditmasterview(Request $request)
	{


		$productid = $request->id;
		$productdetails = DB::table('master_product')->find($productid);


		if (isset($productdetails->product_temperature)) {
			$explodearray = explode('-', $productdetails->ETIN);
			if (count($explodearray) > 1) {
				$productdetails->ETIN = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($productdetails->product_temperature == "Frozen") {
				$productdetails->ETIN = 'ETFZ-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Strong") {
				$productdetails->ETIN = 'ETDS-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Refrigerated") {
				$productdetails->ETIN = 'ETRF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Beverages") {
				$productdetails->ETIN = 'ETBV-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Perishable") {
				$productdetails->ETIN = 'ETDP-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Dry-Fragile") {
				$productdetails->ETIN = 'ETDF-' . $etinmid . '-' . $productdetails->ETIN;
			} else if ($productdetails->product_temperature == "Thaw & Serv") {
				$productdetails->ETIN = 'ETTS-' . $etinmid . '-' . $productdetails->ETIN;
			} else {
				$productdetails->ETIN = 'ETOT-' . $etinmid . '-' . $productdetails->ETIN;
			}
		}
		$productdetails->product_subcategory1_name = DB::table('product_subcategory')->where('id', $productdetails->product_subcategory1)->value('sub_category_1');
		$productdetails->product_subcategory2_name = DB::table('product_subcategory')->where('id', $productdetails->product_subcategory2)->value('sub_category_2');
		$productdetails->product_subcategory3_name = DB::table('product_subcategory')->where('id', $productdetails->product_subcategory3)->value('sub_category_3');

		$productdetails->current_supplier_name = DB::table('suppliers')->where('id', $productdetails->current_supplier)->value('name');
		$productdetails->unit_list = '';
		$productdetails->unit_num = '';
		if ($productdetails->unit_size) {
			$unitarray = explode('-', $productdetails->unit_size);
			if (!empty($unitarray)) {
				$productdetails->unit_num = isset($unitarray[0]) ? $unitarray[0] : '';
				$productdetails->unit_list = isset($unitarray[1]) ? $unitarray[1] : '';
			}
		}

		$productetin = $productdetails->ETIN;
		$productimg = DB::table('product_images')->select('Image_URL1_Primary', 'Image_URL2_Front', 'Image_URL3_Back', 'Image_URL4_Left', 'Image_URL5_Right', 'Image_URL6_Top', 'Image_URL7_Bottom', 'Image_URL8', 'Image_URL9', 'Image_URL10')->where('ETIN', $productetin)->get();

		$productinventory = DB::table('product_inventory')->where('ETIN', $productetin)->first();
		if (!$productinventory) {
			// $productinventory['W1_Orderable_Quantity'] ="W1 Not available";
			// $productinventory['W2_Orderable_Quantity'] ="W2 Not available";
			// $productinventory['W3_Orderable_Quantity'] ="W3 Not available";
			// $productinventory = (object)$productinventory;
		}

		//Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand', 'ASC')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		//Getting all Manufacturer Name list
		$getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name', 'ASC')->get();
		foreach ($getmanufacturer as $manufacturers) {
			$manufacturer[] = $manufacturers->manufacturer_name;
		}

		//Getting all Category list
		$getcategory = DB::table('categories')->orderBy('name', 'ASC')->get();
		foreach ($getcategory as $categorys) {
			$categoryid[] = $categorys->id;
			$categoryname[] = $categorys->name;
			$category = array_combine($categoryid, $categoryname);
		}

		//Getting all Product Type list
		$getproducttype = DB::table('product_type')->orderBy('product_type', 'ASC')->get();
		foreach ($getproducttype as $producttypes) {
			$producttype[] = $producttypes->product_type;
		}

		//Getting all Unit Size list
		$getunitsizes = DB::table('unit_sizes')->orderBy('unit', 'ASC')->get();
		foreach ($getunitsizes as $unitsizes) {
			$unitname[] = $unitsizes->unit;
			$unitabb[] = $unitsizes->abbreviation;
			$unitsize = array_combine($unitabb, $unitname);
		}

		//Getting all Unit Description list
		$getunitdesc = DB::table('unit_desc')->orderBy('unit_description', 'ASC')->get();
		foreach ($getunitdesc as $unitdescs) {
			$unitdesc[] = $unitdescs->unit_description;
		}

		//Getting all Product Tags list
		$getproducttags = DB::table('product_tags')->orderBy('tag', 'ASC')->get();
		foreach ($getproducttags as $producttags) {
			$producttag[] = $producttags->tag;
		}

		//Getting all Product Temparaure list
		$getproducttemp = DB::table('product_temp')->orderBy('product_temperature', 'ASC')->get();
		foreach ($getproducttemp as $producttemps) {
			$producttemp[] = $producttemps->product_temperature;
		}

		//Getting all Suppliers list
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name', 'ASC')->get();
		foreach ($getsuppliers as $suppliers) {
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine($supplier_id, $supplier_name);
		}


		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin', 'ASC')->get();
		foreach ($getcountries as $countries) {
			$country[] = $countries->country_of_origin;
		}

		//Getting all Item From Description list
		$getitemsdescs = DB::table('item_from_description')->orderBy('item_desc', 'ASC')->get();
		foreach ($getitemsdescs as $itemsdescs) {
			$itemsdesc[] = $itemsdescs->item_desc;
		}

		//Getting all Clients list
		$getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();
		foreach ($getclients as $clients) {
			$client[] = $clients->company_name;
		}

		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->orderBy('etailer_availability', 'ASC')->get();
		foreach ($getetailers as $etailerlist) {
			$etailers[] = $etailerlist->etailer_availability;
		}

		return view('cranium.supplierProdListing.masterproductreedit', ['productdetails' => $productdetails, 'brand' => $brand, 'manufacturer' => $manufacturer, 'category' => $category, 'productimg' => $productimg, 'productinventory' => $productinventory, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'supplier' => $supplier, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'etailers' => $etailers]);
	}

	public function updatemaster(MasterProductRequest $request)
	{
		$updatemaster = [];
		$updateinventory = [];
		$insertimgs = [];
		$id = $request->get('id');

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Images Validated');

		if (isset($request->upc_present) && !isset($request->upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "UPC is missing"
			]);
		}

		if (isset($request->gtin_present) && !isset($request->gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "GTIN is missing"
			]);
		}

		if (isset($request->unit_upc_present) && !isset($request->unit_upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit UPC is missing"
			]);
		}

		if (isset($request->unit_gtin_present) && !isset($request->unit_gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit GTIN is missing"
			]);
		}

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));

			if (count($explodearray) > 1) {
				$updatemaster['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($request->get('product_temperature') == "Frozen") {
				$updatemaster['ETIN'] = 'ETFZ-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$updatemaster['ETIN'] = 'ETDS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$updatemaster['ETIN'] = 'ETRF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$updatemaster['ETIN'] = 'ETBV-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$updatemaster['ETIN'] = 'ETDP-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$updatemaster['ETIN'] = 'ETDF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$updatemaster['ETIN'] = 'ETTS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else {
				$updatemaster['ETIN'] = 'ETOT-' . $etinmid . '-' . $updatemaster['ETIN'];
			}
		}

		//Etin ready for ProductImage table
		$insertimgs['ETIN'] = $updatemaster['ETIN'];
		$updatemaster['parent_ETIN'] = $request->get('parent_ETIN');
		//$updatemaster['product_listing_name'] = $request->get('product_listing_name');
		$updatemaster['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$updatemaster['about_this_item'] = implode('#', $request->get('about_this_item'));
		$updatemaster['manufacturer'] = $request->get('manufacturer');
		$updatemaster['brand'] = $request->get('brand');
		$updatemaster['flavor'] = ProperInput($request->get('flavor'));
		$updatemaster['product_type'] = $request->get('product_type');
		$updatemaster['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$updatemaster['unit_description'] = $request->get('unit_description');
		$updatemaster['pack_form_count'] = $request->get('pack_form_count');
		$updatemaster['item_form_description'] = $request->get('item_form_description');
		$updatemaster['total_ounces'] = $request->get('total_ounces');
		$updatemaster['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$updatemaster['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$updatemaster['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$updatemaster['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$updatemaster['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$updatemaster['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$updatemaster['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$updatemaster['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$updatemaster['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$updatemaster['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$updatemaster['key_product_attributes_diet'] = ProperInput($request->get('key_product_attributes_diet'));
		$updatemaster['product_tags'] = $request->get('product_tags');
		$updatemaster['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$updatemaster['hazardous_materials'] = $request->get('hazardous_materials');
		$updatemaster['storage'] = $request->get('storage');
		$updatemaster['ingredients'] = $request->get('ingredients');
		$updatemaster['allergens'] = $request->get('allergens');
		$updatemaster['prop_65_flag'] = $request->get('prop_65_flag');
		$updatemaster['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$updatemaster['product_temperature'] = $request->get('product_temperature');
		$updatemaster['supplier_status'] = $request->get('supplier_status');
		$updatemaster['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
		$updatemaster['gtin'] = isset($request->gtin) ? ProperInput($request->get('gtin')) : '';
		$updatemaster['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $updatemaster['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $updatemaster['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $updatemaster['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$updatemaster['asin'] = ProperInput($request->get('asin'));
		$updatemaster['GPC_code'] = ProperInput($request->get('GPC_code'));
		$updatemaster['GPC_class'] = ProperInput($request->get('GPC_class'));
		$updatemaster['HS_code'] = ProperInput($request->get('HS_code'));
		$updatemaster['weight'] = $request->get('weight');
		$updatemaster['length'] = $request->get('length');
		$updatemaster['width'] = $request->get('width');
		$updatemaster['height'] = $request->get('height');
		$updatemaster['country_of_origin'] = $request->get('country_of_origin');
		$updatemaster['package_information'] = ProperInput($request->get('package_information'));
		$updatemaster['cost'] = $request->get('cost');
		$updatemaster['acquisition_cost'] = $request->get('acquisition_cost');
		$updatemaster['new_cost'] = $request->get('new_cost');
		$updatemaster['new_cost_date'] = $request->get('new_cost_date');
		$updatemaster['status'] = $request->get('status');
		$updatemaster['etailer_availability'] = ProperInput($request->get('etailer_availability'));
		$updatemaster['dropship_available'] = $request->get('dropship_available');
		$updatemaster['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$updatemaster['POG_flag'] = $request->get('POG_flag');
		$updatemaster['consignment'] = $request->get('consignment');
		$updatemaster['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		$updatemaster['status_date'] = $request->get('status_date');
		$updatemaster['lobs'] = $request->get('lobs');
		$updatemaster['chanel_ids'] = $request->get('chanel_ids');
		$updatemaster['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$updatemaster['client_supplier_id'] = $request->get('current_supplier');
		$updatemaster['alternate_ETINs'] = $request->get('alternate_ETINs');
		$updatemaster['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$updatemaster['unit_in_pack'] = $request->get('unit_in_pack');
		$updatemaster['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$updatemaster['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$updatemaster['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$updatemaster['is_edit'] = 1;
		$updatemaster['is_approve'] = $request->get('is_approve');
		$updatemaster['approved_date'] = $request->get('approved_date');
		$updatemaster['queue_status'] = $request->get('queue_status');
		$updatemaster['week_worth_qty'] = $request->get('week_worth_qty') != null || $request->get('week_worth_qty') > 0 ? $request->get('week_worth_qty') : 0;
		$updatemaster['min_order_qty'] = $request->get('min_order_qty') != null || $request->get('min_order_qty') > 0 ? $request->get('min_order_qty') : 0;
		$updatemaster['lead_time'] = $request->get('lead_time');
		$updatemaster['updated_by'] = Auth::user()->id;
		$updatemaster['is_wl'] = isset($request->list_type) && $request->get('list_type') === 'w_list' ? 1 : 0;
		$updatemaster['is_bl'] = isset($request->list_type) && $request->get('list_type') === 'b_list' ? 1 : 0;

		//adding inventory
		$updateinventory['ETIN'] = $updatemaster['ETIN'];

		$updatemaster['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';
		$oldrecord = DB::table('master_product')->find($id);
		$old_data = $oldrecord;
		if ($request->get('is_approve') == 0 || moduleacess('auto_approval_for_edit')) {
			$updatemaster['updated_by'] = Auth::user()->id;
			$affected = DB::table('master_product')->where('id', $id)->update($updatemaster);
			if (empty($request->get('parent_ETIN'))) {

				$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data')->where('master_product_id', $id)->first();
				if (!$check_supplemental_mpt_data_queue) {
					$insertmpt['ETIN'] = $updatemaster['ETIN'];
					$insertmpt['master_product_id'] = $id;
					$insertmpt['weight'] = $request->get('unit_weight');
					$insertmpt['length'] = $request->get('unit_length');
					$insertmpt['width'] = $request->get('unit_width');
					$insertmpt['height'] = $request->get('unit_height');
					$insertmpt['upc'] = isset($request->unit_upc) ? ProperInput($request->get('unit_upc')) : '';
					$insertmpt['gtin'] = isset($request->unit_gtin) ? ProperInput($request->get('unit_gtin')) : '';
					$insertmpt['created_at'] = date('Y-m-d H:i:s');
					$insertmpt['updated_at'] = date('Y-m-d H:i:s');
					DB::table('supplemental_mpt_data')->insert($insertmpt);
					$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Supplemental MPT Data Added to Queue');
				} else {
					$insertmpt['ETIN'] = $updatemaster['ETIN'];
					$insertmpt['weight'] = $request->get('unit_weight');
					$insertmpt['length'] = $request->get('unit_length');
					$insertmpt['width'] = $request->get('unit_width');
					$insertmpt['height'] = $request->get('unit_height');
					$insertmpt['upc'] = isset($request->unit_upc) ? ProperInput($request->get('unit_upc')) : '';
					$insertmpt['gtin'] = isset($request->unit_gtin) ? ProperInput($request->get('unit_gtin')) : '';
					$insertmpt['updated_at'] = date('Y-m-d H:i:s');
					DB::table('supplemental_mpt_data')->where('master_product_id', $id)->update($insertmpt);
					$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Supplemental MPT Data Queue Updated');
				}
			}
		} else {
			// Insert previous record in history

			$old_data = $oldrecord;
			$oldrecordarray = (array)$oldrecord;
			$oldrecordarray['id'] = null;
			$oldrecordarray['updated_by'] = Auth::user()->id;
			$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
			$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Old Record Added To Master Product History');
			$check_master_product_queue = DB::table('master_product_queue')->where('master_product_id', $id)->first();
			if (!$check_master_product_queue) {

				$updatemaster['created_at'] = date('Y-m-d H:i:s');
				$updatemaster['updated_at'] = date('Y-m-d H:i:s');
				$updatemaster['master_product_id'] = $id;
				$updatemaster['inserted_by'] = $oldrecordarray['inserted_by'];
				$updatemaster['updated_by'] = Auth::user()->id;
				$master_que_id = DB::table('master_product_queue')->insertGetId($updatemaster);
				$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Master Product Added To Queue.');
			} else {
				$updatemaster['updated_at'] = date('Y-m-d H:i:s');
				$updatemaster['updated_by'] = Auth::user()->id;
				$affected = DB::table('master_product_queue')->where('master_product_id', $id)->update($updatemaster);
				$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Master Product Queue Updated.');
				$master_que_id = $check_master_product_queue->id;
			}
			//send notification on product edit
			$this->masterProduct->sendApproveRejectNotificationForEdit($updatemaster, $oldrecord, $master_que_id);
			$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Approve/Reject Notification Sent.');

			if (empty($request->get('parent_ETIN'))) {

				$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $master_que_id)->first();
				if (!$check_supplemental_mpt_data_queue) {
					$insertmpt['ETIN'] = $updatemaster['ETIN'];
					$insertmpt['master_product_id'] = $master_que_id;
					$insertmpt['weight'] = $request->get('unit_weight');
					$insertmpt['length'] = $request->get('unit_length');
					$insertmpt['width'] = $request->get('unit_width');
					$insertmpt['height'] = $request->get('unit_height');
					$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
					$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
					$insertmpt['created_at'] = date('Y-m-d H:i:s');
					$insertmpt['updated_at'] = date('Y-m-d H:i:s');
					DB::table('supplemental_mpt_data_queue')->insert($insertmpt);
					$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Supplemental MPT Data Added to Queue');
				} else {
					$insertmpt['ETIN'] = $updatemaster['ETIN'];
					$insertmpt['weight'] = $request->get('unit_weight');
					$insertmpt['length'] = $request->get('unit_length');
					$insertmpt['width'] = $request->get('unit_width');
					$insertmpt['height'] = $request->get('unit_height');
					$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
					$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
					$insertmpt['updated_at'] = date('Y-m-d H:i:s');
					DB::table('supplemental_mpt_data_queue')->where('master_product_id', $master_que_id)->update($insertmpt);
					$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Supplemental MPT Data Queue Updated');
				}
			}

			DB::table('master_product')->where('id', $id)->update([
				'product_edit_request' => 1
			]);
			$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Master Product Updated to Edit Request');
		}

		$this->masterProduct->MakeProductHistory([
			'old_data' => $old_data,
			'new_data' => $updatemaster,
			'master_product_id' => $id,
			'action' => 'Edit'
		]);
		$logsData = DB::table('product_history')->where('master_product_id', $id)->where('action','edit')->orderBy('id', 'DESC')->first();
		UserLogs([
			'user_id' => Auth::user()->id,
			'action' => 'Click',
			'task' => 'Update Product',
			'details' => 'Update Product'.$logsData ? $logsData->response: NULL,
			'type' => 'CWMS'
		]);

		DB::table('master_product_images')->where('ETIN', $request->get('ETIN'))->update(['ETIN' => $updatemaster['ETIN']]);
		$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Master Product Images Updated');
		$insert_image = $this->masterProduct->insertImageFzl($updatemaster['ETIN'], $request->all());
		$this->masterProduct->insertProcessLog('UpdateMasterProduct', 'Master Product Images Inserted');

		/* Notify other admins */		
		if(auth()->user()){
			$user = auth()->user();
			$note = $updatemaster['ETIN'] . " Master Product Edit by ".$user->name;

			$masterProductReq = MasterProductQueue::where('ETIN', $updatemaster['ETIN'])->first();

			$url = '/editmasterrequestview/'.$masterProductReq['id'].'/tab_comments';
			$type = "Master Product Request";

			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);
				
		}

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product is updated to approve.'
		]);
	}

	public function reupdatemaster(Request $request)
	{
		$updatemaster = [];
		$updateinventory = [];
		$id = $request->get('id');
		//$updatemaster['ETIN'] = $request->get('ETIN');

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));

			if (count($explodearray) > 1) {
				$updatemaster['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($request->get('product_temperature') == "Frozen") {
				$updatemaster['ETIN'] = 'ETFZ-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$updatemaster['ETIN'] = 'ETDS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$updatemaster['ETIN'] = 'ETRF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$updatemaster['ETIN'] = 'ETBV-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$updatemaster['ETIN'] = 'ETDP-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$updatemaster['ETIN'] = 'ETDF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$updatemaster['ETIN'] = 'ETTS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else {
				$updatemaster['ETIN'] = 'ETOT-' . $etinmid . '-' . $updatemaster['ETIN'];
			}
		}

		$updatemaster['parent_ETIN'] = $request->get('parent_ETIN');
		//$updatemaster['product_listing_name'] = $request->get('product_listing_name');
		$updatemaster['full_product_desc'] = $request->get('full_product_desc');
		$updatemaster['about_this_item'] = $request->get('about_this_item');
		$updatemaster['manufacturer'] = $request->get('manufacturer');
		$updatemaster['brand'] = $request->get('brand');
		$updatemaster['flavor'] = $request->get('flavor');
		$updatemaster['product_type'] = $request->get('product_type');
		$updatemaster['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$updatemaster['unit_description'] = $request->get('unit_description');
		$updatemaster['pack_form_count'] = $request->get('pack_form_count');
		$updatemaster['item_form_description'] = $request->get('item_form_description');
		$updatemaster['total_ounces'] = $request->get('total_ounces');
		$updatemaster['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$updatemaster['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$updatemaster['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$updatemaster['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$updatemaster['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$updatemaster['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$updatemaster['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$updatemaster['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$updatemaster['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$updatemaster['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$updatemaster['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		$updatemaster['product_tags'] = implode(',', $request->get('product_tags'));
		$updatemaster['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$updatemaster['hazardous_materials'] = $request->get('hazardous_materials');
		$updatemaster['storage'] = $request->get('storage');
		$updatemaster['ingredients'] = $request->get('ingredients');
		$updatemaster['allergens'] = $request->get('allergens');
		$updatemaster['prop_65_flag'] = $request->get('prop_65_flag');
		$updatemaster['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$updatemaster['product_temperature'] = $request->get('product_temperature');
		$updatemaster['supplier_status'] = $request->get('supplier_status');
		$updatemaster['upc'] = $request->get('upc');
		$updatemaster['gtin'] = $request->get('gtin');
		$updatemaster['asin'] = $request->get('asin');
		$updatemaster['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $updatemaster['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $updatemaster['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $updatemaster['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$updatemaster['GPC_code'] = $request->get('GPC_code');
		$updatemaster['GPC_class'] = $request->get('GPC_class');
		$updatemaster['HS_code'] = $request->get('HS_code');
		$updatemaster['weight'] = $request->get('weight');
		$updatemaster['length'] = $request->get('length');
		$updatemaster['width'] = $request->get('width');
		$updatemaster['height'] = $request->get('height');
		$updatemaster['country_of_origin'] = $request->get('country_of_origin');
		$updatemaster['package_information'] = $request->get('package_information');
		//$updatemaster['cost'] = $request->get('cost');
		//$updatemaster['new_cost'] = $request->get('new_cost');
		//$updatemaster['new_cost_date'] = $request->get('new_cost_date');
		$updatemaster['status'] = $request->get('status');
		$updatemaster['etailer_availability'] = $request->get('etailer_availability');
		$updatemaster['dropship_available'] = $request->get('dropship_available');
		$updatemaster['channel_listing_restrictions'] = $request->get('channel_listing_restrictions');
		$updatemaster['POG_flag'] = $request->get('POG_flag');
		$updatemaster['consignment'] = $request->get('consignment');
		$updatemaster['warehouses_assigned'] = $request->get('warehouses_assigned');
		$updatemaster['status_date'] = $request->get('status_date');
		$updatemaster['lobs'] = $request->get('lobs');
		$updatemaster['chanel_ids'] = $request->get('chanel_ids');
		$updatemaster['client_supplier_id'] = $request->get('current_supplier');
		$updatemaster['alternate_ETINs'] = $request->get('alternate_ETINs');
		$updatemaster['product_listing_ETIN'] = $request->get('product_listing_ETIN');
		$updatemaster['about_this_item'] = $request->get('about_this_item');
		$updatemaster['unit_in_pack'] = $request->get('unit_in_pack');
		$updatemaster['supplier_product_number'] = $request->get('supplier_product_number');
		$updatemaster['manufacture_product_number'] = $request->get('manufacture_product_number');
		$updatemaster['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$updatemaster['is_edit'] = 2;
		//$updatemaster['is_approve'] = 0;
		$updatemaster['created_at'] = date('Y-m-d H:i:s');
		$updatemaster['updated_at'] = date('Y-m-d H:i:s');
		$updatemaster['lead_time'] = $request->get('lead_time');
		$updatemaster['updated_by'] = Auth::user()->id;

		$updatemaster['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

		//adding inventory
		$updateinventory['ETIN'] = $updatemaster['ETIN'];
		// $updateinventory['W1_Orderable_Quantity'] = $request->get('W1_Orderable_Quantity');
		// $updateinventory['W2_Orderable_Quantity'] = $request->get('W2_Orderable_Quantity');
		// $updateinventory['W3_Orderable_Quantity'] = $request->get('W3_Orderable_Quantity');



		//******************Upload New Image
		if ($request->hasFile('file')) {

			$allimgs = $request->file('file');

			$exsistingproductimg = DB::table('product_images')->where('ETIN', $updatemaster['ETIN'])->first();
			if ($exsistingproductimg) {
				if (Storage::disk('s3')->exists($updatemaster['ETIN'])) {
					Storage::disk('s3')->deleteDirectory($updatemaster['ETIN']);
				}

				$exsistingproductimg = DB::table('product_images')->where('ETIN', $updatemaster['ETIN'])->delete();
			}



			$directory = $updatemaster['ETIN'];
			$s3 = Storage::disk('s3')->makeDirectory($directory);

			$imgfields =  array('Image_URL1_Primary', 'Image_URL2_Front', 'Image_URL3_Back', 'Image_URL4_Left', 'Image_URL5_Right', 'Image_URL6_Top', 'Image_URL7_Bottom', 'Image_URL8', 'Image_URL9', 'Image_URL10');

			$i = 0;
			foreach ($allimgs as $file) {
				$s3 = \Storage::disk('s3');
				$file_name = time() . '-' . $file->getClientOriginalName();
				$s3filePath = $directory . '/' . $file_name;
				$s3->put($s3filePath, file_get_contents($file), 'public');
				$imgurls[] = Storage::disk('s3')->url($s3filePath);
			}


			foreach ($imgurls as $url) {
				$totalimgs[] = $imgfields[$i];
				$i++;
			}

			$inset = array();
			$inset['ETIN'] = $updatemaster['ETIN'];
			$insertimgs = array_combine($totalimgs, $imgurls);
			$insertdata =  $inset + $insertimgs;

			$imageurlinsert = DB::table('product_images')->insert($insertdata);
		}

		//******************End Upload New Image

		$affected = DB::table('master_product')->where('id', $id)->update($updatemaster);
		//$affected2 = DB::table('product_inventory')->insert($updateinventory);

		// $checkinventory = DB::table('product_inventory')->where('ETIN', $updatemaster['ETIN'])->first();
		// if (!$checkinventory) {
		// 	$updateinventory['created_at'] = date('Y-m-d H:i:s');
		// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
		// 	$inventoryeffect = DB::table('product_inventory')->insert($updateinventory);
		// } else {
		// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
		// 	$inventoryeffect = DB::table('product_inventory')->where('id', $id)->update($updateinventory);
		// }



		//return redirect('/home')->with('success', 'Master Product is updated..');
		return redirect()->back()->with('success', 'Master Product Re-Edited..');
	}

	public function updateflag(Request $request)
	{
		$productid = $request->id;
		$product = DB::table('master_product')->where('id', $productid)->update(['is_approve' => 1, 'approved_date' => date('Y-m-d H:i:s'), 'is_edit' => 0, 'updated_by' => Auth::user()->id]);
		$this->masterProduct->MakeProductHistory([
			'response' => Auth::user()->name . ' made this product live',
			'master_product_id' => $productid,
			'action' => 'Live'
		]);
		DB::table('product_tickets')->where('master_product_id', $productid)->update(['status' => 0]);

		/* Notify other admins */		
		if(auth()->user()){
			$user = auth()->user();
			$masterProd = MasterProduct::find($productid);
			$note = $masterProd['ETIN'] . " Product Approved by ".$user->name;			

			$url = '/editmasterproduct/'.$productid.'/tab_comments';
			$type = "Prduct Published";
			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);
				
		}

		return redirect()->route('editmasterproduct', $productid)->with('success', 'You have sucessfully published the product.');
	}

	public function deletemasterproduct(Request $request)
	{
		$productid = $request->id;
		DB::table('master_product')->where('id', '=', $productid)->delete();
		return redirect('/home')->with('success', 'Child Product is added sucessfully..');
	}

	public function deletemasterproductdraft(Request $request, $id)
	{
		$data = MasterProductQueue::find($id);
		$etin = $data->ETIN;
		MasterProductQueue::where('id', $id)->delete();
		UserLogs([
			'user_id' => Auth::user()->id,
			'action' => 'Click',
			'task' => 'Delete Queue Product',
			'details' => 'Delete Queue Product'.$etin,
			'type' => 'CWMS'
		]);
		return redirect()->back()->with('success', 'Draft Product is deleted sucessfully..');
	}

	public function getsubcategories(Request $request)
	{
		$id = $request->id;
		$data = DB::table('categories')->select('id', 'name')->where('parent_id', $id)->groupBy('name')->orderBy('name', 'ASC')->get();
		// dd($data);
		return response()->json(['data' => $data]);
	}

	public function getsubcategories1(Request $request)
	{
		$id = $request->id;
		$data = DB::table('product_subcategory')->select('sub_category_1')->where('product_category_id', $id)->groupBy('sub_category_1')->orderBy('sub_category_1', 'ASC')->get();
		return response()->json(['data' => $data]);
	}

	public function getsubcategories2(Request $request)
	{
		$sub1 = $request->id;

		$data = DB::table('product_subcategory')->select('sub_category_2')->where('sub_category_1', $sub1)->groupBy('sub_category_2')->orderBy('sub_category_2', 'ASC')->get();
		return response()->json(['data' => $data]);
	}

	public function getsubcategories3(Request $request)
	{
		$sub2 = $request->id;
		$data = DB::table('product_subcategory')->select('sub_category_3')->where('sub_category_2', $sub2)->groupBy('sub_category_3')->orderBy('sub_category_3', 'ASC')->get();
		return response()->json(['data' => $data]);
	}

	public function getbrand(Request $request)
	{
		$manufacturer_name = $request->name;
		$manufacturer_id = DB::table('manufacturer')->select('id')->where('manufacturer_name', $manufacturer_name)->get();
		$manufacturer_id = $manufacturer_id[0]->id;
		$data = DB::table('brand')->select('brand')->where('manufacturer_id', $manufacturer_id)->groupBy('brand')->get();
		return response()->json(['data' => $data]);
	}

	public function getmanufacturer(Request $request)
	{
		$brand_name = $request->name;

		$data = DB::table('brand')->leftJoin('manufacturer', function ($join) {
			$join->on('manufacturer.id', '=', 'brand.manufacturer_id');
		})->select('manufacturer.manufacturer_name')->where('brand', $brand_name)->groupBy('manufacturer_id')->get();
		return response()->json(['data' => $data]);
	}

	public function s3_bucket_connect()
	{
		$bucket = 'cranium-images';

		// Instantiate the client.
		$s3 = new S3Client([
			'version' => 'latest',
			'region'  => 'us-east-2'
		]);

		// Use the high-level iterators (returns ALL of your objects).
		try {
			$results = $s3->getPaginator('ListObjects', [
				'Bucket' => $bucket
			]);
			$datas = [];
			foreach ($results as $result) {
				foreach ($result['Contents'] as $object) {
					//echo pathinfo($object['Key'],PATHINFO_DIRNAME) . PHP_EOL.'<br>';
					$data[] = array(
						'ETIN' => pathinfo($object['Key'], PATHINFO_DIRNAME),
						//'filename' => pathinfo($object['Key'],PATHINFO_FILENAME),
						'url' => "https://cranium-images.s3.us-east-2.amazonaws.com/" . $object['Key'],
						//'data' => $object,
					);

					$prodImage = new ProductImage();
					$prodImage->ETIN = pathinfo($object['Key'], PATHINFO_DIRNAME);
					$prodImage->Image_URL1_Primary = "https://cranium-images.s3.us-east-2.amazonaws.com/" . $object['Key'];
					$prodImage->save();
				}
			}
			echo "<pre>";
			print_r($data);
		} catch (S3Exception $e) {
			echo $e->getMessage() . PHP_EOL;
		}

		//return view('welcome');
	}

	public function ApproveOrRejectProductRequest($ETIN, $status)
	{
		$oldrecord = DB::table('master_product')->where('ETIN', $ETIN)->first();
		if ($status == 1) {

			$oldrecordarray = (array)$oldrecord;
			$oldrecordarray['id'] = null;
			// $oldrecordarray['updated_by'] = Auth::user()->id;
			$oldrecordarray['inserted_by'] = Auth::user()->id;
			$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);

			$check_master_product_queue = DB::table('master_product_queue')->where('ETIN', $ETIN)->first();
			$updatemaster = [];
			if ($check_master_product_queue) {
				$excluded_keys = ['id', 'created_at', 'updated_at', 'product_edit_request', 'is_approve', 'approved_date'];
				foreach ($check_master_product_queue as $key => $value) {

					if (!in_array($key, $excluded_keys)) {
						$updatemaster[$key] = $value;
					}
				}
				$updatemaster['product_edit_request'] = NULL;
				$CMP = DB::table('master_product')->where('ETIN', $ETIN)->first();
				if ($CMP) {
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					// $updatemaster['updated_by'] = Auth::user()->id;
					DB::table('master_product')->where('ETIN', $ETIN)->update($updatemaster);
					$this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name . ' approved changes',
						'master_product_id' => $check_master_product_queue->master_product_id,
						'action' => 'Approved'
					]);
				} else {
					$updatemaster['created_at'] = date('Y-m-d H:i:s');
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					// $updatemaster['inserted_by'] = Auth::user()->id;
					$master_product_id = DB::table('master_product')->insertGetId($updatemaster);
					$this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name . ' created Product: ' . $updatemaster['ETIN'],
						'master_product_id' => $master_product_id,
						'action' => 'Add'
					]);
				}
			}

			// $checkinventory = DB::table('product_inventory_queue')->where('ETIN', $ETIN)->first();
			// $updateinventory = [];
			// $excluded_keys = ['id', 'created_at', 'updated_at'];
			// if ($checkinventory) {
			// 	$excluded_keys = ['id', 'created_at', 'updated_at'];
			// 	foreach ($checkinventory as $key => $value) {
			// 		if (!in_array($key, $excluded_keys)) {
			// 			$updateinventory[$key] = $value;
			// 		}
			// 	}
			// 	$CI = DB::table('product_inventory')->where('ETIN', $updatemaster['ETIN'])->first();
			// 	if ($CI) {
			// 		$inventoryeffect = DB::table('product_inventory')->where('ETIN', $updatemaster['ETIN'])->update($updateinventory);
			// 	} else {
			// 		$inventoryeffect = DB::table('product_inventory')->insert($updateinventory);
			// 	}
			// }

			$checkimage = DB::table('product_images_queue')->where('ETIN', $ETIN)->first();
			$proimage = [];
			if ($checkimage) {
				$excluded_keys = ['id', 'created_at', 'updated_at'];
				foreach ($checkimage as $key => $value) {
					if (!in_array($key, $excluded_keys)) {
						$proimage[$key] = $value;
					}
				}
				$CIP = DB::table('product_images')->where('ETIN', $ETIN)->first();
				if ($CIP) {
					DB::table('product_images')->where('ETIN', $updatemaster['ETIN'])->update($proimage);
				} else {
					DB::table('product_images')->insert($proimage);
				}
			}
		}
		DB::table('master_product_queue')->where('ETIN', $ETIN)->delete();
		$this->masterProduct->insertProcessLog('ApproveOrRejectProductRequest', 'Master Product Removed To Queue.');
		UserLogs([
			'user_id' => Auth::user()->id,
			'action' => 'Click',
			'task' => 'Reject Product Request',
			'details' => 'Product'.$ETIN.' request is rejected.',
			'type' => 'CWMS'
		]);

		// DB::table('product_inventory_queue')->where('ETIN', $ETIN)->delete();
		// $this->masterProduct->insertProcessLog('ApproveOrRejectProductRequest', 'Master Product Inventory Removed To Queue.');
		DB::table('product_images_queue')->where('ETIN', $ETIN)->delete();
		$this->masterProduct->insertProcessLog('ApproveOrRejectProductRequest', 'Master Product Images Removed To Queue.');


		if ($oldrecord) {
			$this->masterProduct->insertProcessLog('ApproveOrRejectProductRequest', 'Master Product Request Rejected.');
			return redirect(url('editmasterproduct/' . $oldrecord->id . ''))->with('success', 'Master Product is updated..');
		} else {
			return view('cranium.allmasterproductlists');
		}
	}

	public function ApproveProductRequest(MasterProductApproveRequest $request, $id, $status)
	{

		$check_master_product_queue = DB::table('master_product_queue')->where('id', $id)->first();
		if(!$check_master_product_queue){
			return response()->json([
				'error' => 1,
				'msg' => "Product not found"
			]);
		}
		$master_product_id = $check_master_product_queue->master_product_id;
		$oldrecord = DB::table('master_product')->where('id', $check_master_product_queue->master_product_id)->first();
		$proimage = [];

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Image Validated.');

		if (isset($request->upc_present) && !isset($request->upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "UPC is missing"
			]);
		}

		if (isset($request->gtin_present) && !isset($request->gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "GTIN is missing"
			]);
		}

		if (isset($request->unit_upc_present) && !isset($request->unit_upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit UPC is missing"
			]);
		}

		if (isset($request->unit_gtin_present) && !isset($request->unit_gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit GTIN is missing"
			]);
		}

		if ($status == 1) {

			$oldrecordarray = (array)$oldrecord;
			$oldrecordarray['id'] = null;
			$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
			$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Old Record Inserted In Master Product History.');
			$updatemaster = [];
			if ($check_master_product_queue) {
				$excluded_keys = ['id', 'created_at', 'updated_at', 'product_edit_request', 'is_approve', 'approved_date', 'master_product_id', 'queue_status'];


				if ($request->get('product_temperature')) {

					$explodearray = explode('-', $request->get('ETIN'));

					if (count($explodearray) > 1) {
						$updatemaster['ETIN'] = end($explodearray);
						$etinmid = $explodearray[1];
					}
					if ($request->get('product_temperature') == "Frozen") {
						$updatemaster['ETIN'] = 'ETFZ-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Dry-Strong") {
						$updatemaster['ETIN'] = 'ETDS-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Refrigerated") {
						$updatemaster['ETIN'] = 'ETRF-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Beverages") {
						$updatemaster['ETIN'] = 'ETBV-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Dry-Perishable") {
						$updatemaster['ETIN'] = 'ETDP-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Dry-Fragile") {
						$updatemaster['ETIN'] = 'ETDF-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else if ($request->get('product_temperature') == "Thaw & Serv") {
						$updatemaster['ETIN'] = 'ETTS-' . $etinmid . '-' . $updatemaster['ETIN'];
					} else {
						$updatemaster['ETIN'] = 'ETOT-' . $etinmid . '-' . $updatemaster['ETIN'];
					}
				} else {
					$updatemaster['ETIN'] = $request->get('ETIN');
				}

				$proimage['ETIN'] = $updatemaster['ETIN'];
				$updatemaster['parent_ETIN'] = $request->get('parent_ETIN');
				//$updatemaster['product_listing_name'] = $request->get('product_listing_name');
				$updatemaster['full_product_desc'] = ProperInput($request->get('full_product_desc'));
				$updatemaster['about_this_item'] = implode('#', $request->get('about_this_item'));
				$updatemaster['manufacturer'] = $request->get('manufacturer');
				$updatemaster['brand'] = $request->get('brand');
				$updatemaster['flavor'] = $request->get('flavor');
				$updatemaster['product_type'] = $request->get('product_type');
				$updatemaster['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
				$updatemaster['unit_description'] = $request->get('unit_description');
				$updatemaster['pack_form_count'] = $request->get('pack_form_count');
				$updatemaster['item_form_description'] = $request->get('item_form_description');
				$updatemaster['total_ounces'] = $request->get('total_ounces');
				$updatemaster['product_category'] = isset($request->product_category) ? $request->product_category : '';
				$updatemaster['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
				$updatemaster['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
				$updatemaster['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
				$updatemaster['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
				$updatemaster['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
				$updatemaster['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
				$updatemaster['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
				$updatemaster['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
				$updatemaster['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
				$updatemaster['key_product_attributes_diet'] = ProperInput($request->get('key_product_attributes_diet'));
				$updatemaster['product_tags'] = $request->get('product_tags');
				$updatemaster['MFG_shelf_life'] = $request->get('MFG_shelf_life');
				$updatemaster['hazardous_materials'] = $request->get('hazardous_materials');
				$updatemaster['storage'] = ProperInput($request->get('storage'));
				$updatemaster['ingredients'] = $request->get('ingredients');
				$updatemaster['allergens'] = $request->get('allergens');
				$updatemaster['prop_65_flag'] = $request->get('prop_65_flag');
				$updatemaster['prop_65_ingredient'] = $request->get('prop_65_ingredient');
				$updatemaster['product_temperature'] = $request->get('product_temperature');
				$updatemaster['supplier_status'] = $request->get('supplier_status');
				$updatemaster['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
				$updatemaster['gtin'] = isset($request->gtin) ? ProperInput($request->get('gtin')) : '';
				$updatemaster['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
				$updatemaster['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
				$updatemaster['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
				$updatemaster['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
				$updatemaster['asin'] = ProperInput($request->get('asin'));
				$updatemaster['GPC_code'] = ProperInput($request->get('GPC_code'));
				$updatemaster['GPC_class'] = ProperInput($request->get('GPC_class'));
				$updatemaster['HS_code'] = ProperInput($request->get('HS_code'));
				$updatemaster['weight'] = $request->get('weight');
				$updatemaster['length'] = $request->get('length');
				$updatemaster['width'] = $request->get('width');
				$updatemaster['height'] = $request->get('height');
				$updatemaster['country_of_origin'] = $request->get('country_of_origin');
				$updatemaster['package_information'] = ProperInput($request->get('package_information'));
				$updatemaster['cost'] = $request->get('cost');
				$updatemaster['acquisition_cost'] = $request->get('acquisition_cost');
				$updatemaster['new_cost'] = $request->get('new_cost');
				$updatemaster['new_cost_date'] = $request->get('new_cost_date');
				$updatemaster['status'] = $request->get('status');
				$updatemaster['etailer_availability'] = $request->get('etailer_availability');
				$updatemaster['dropship_available'] = $request->get('dropship_available');
				$updatemaster['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
				$updatemaster['POG_flag'] = $request->get('POG_flag');
				$updatemaster['consignment'] = $request->get('consignment');
				if ($request->get('warehouses_assigned')) {
					$updatemaster['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
				} else {
					$updatemaster['warehouses_assigned'] = '';
				}
				// $updatemaster['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
				$updatemaster['status_date'] = $request->get('status_date');
				$updatemaster['lobs'] = $request->get('lobs');
				$updatemaster['chanel_ids'] = $request->get('chanel_ids');
				$updatemaster['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
				$updatemaster['client_supplier_id'] = $request->get('current_supplier');
				$updatemaster['alternate_ETINs'] = $request->get('alternate_ETINs');
				$updatemaster['product_listing_ETIN'] = $request->get('product_listing_ETIN');

				$updatemaster['unit_in_pack'] = $request->get('unit_in_pack');
				$updatemaster['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
				$updatemaster['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
				$updatemaster['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
				$updatemaster['is_edit'] = 1;
				$updatemaster['is_approve'] = $request->get('is_approve');
				$updatemaster['approved_date'] = $request->get('approved_date');


				$updatemaster['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

				$updatemaster['product_edit_request'] = NULL;
				$updatemaster['is_wl'] = isset($request->list_type) && $request->get('list_type') === 'w_list' ? 1 : 0;
				$updatemaster['is_bl'] = isset($request->list_type) && $request->get('list_type') === 'b_list' ? 1 : 0;

				$CMP = DB::table('master_product')->where('id', $check_master_product_queue->master_product_id)->first();
				if ($CMP) {
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					// $this->masterProduct->sendApproveRejectNotificationForEdit($updatemaster,$CMP,$check_master_product_queue->master_product_id);
					// $this->masterProduct->insertProductHistoryForApprove($updatemaster,$CMP,$check_master_product_queue->master_product_id);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Product History Inserted.');
					// $updatemaster['updated_by'] = Auth::user()->id;
					DB::table('master_product')->where('id', $check_master_product_queue->master_product_id)->update($updatemaster);
					$this->masterProduct->transferTickets($check_master_product_queue->id, $check_master_product_queue->master_product_id);

					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Master Product Updated.');
					$this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name . ' approved changes',
						'master_product_id' => $check_master_product_queue->master_product_id,
						'action' => 'Approved'
					]);
					UserLogs([
						'user_id' => Auth::user()->id,
						'action' => 'Click',
						'task' => 'Approved Product change',
						'details' => 'Product'.$updatemaster['ETIN'].' changes approved',
						'type' => 'CWMS'
					]);
					
				} else {
					$updatemaster['created_at'] = date('Y-m-d H:i:s');
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					$updatemaster['inserted_by'] = Auth::user()->id;
					$master_product_id = DB::table('master_product')->insertGetId($updatemaster);
					$this->masterProduct->transferTickets($check_master_product_queue->id, $master_product_id);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Master Product Inserted.');
					if (isset($check_master_product_queue->inserted_by) && $check_master_product_queue->queue_status == 'd') {
						$user = DB::table('users')->find($check_master_product_queue->inserted_by);
						if ($user) {
							$this->masterProduct->MakeProductHistory([
								'response' => $user->name . ' created Product: ' . $updatemaster['ETIN'],
								'master_product_id' => $master_product_id,
								'action' => 'Add'
							]);
							UserLogs([
								'user_id' => $user->id,
								'action' => 'Click',
								'task' => 'Add Product',
								'details' => 'Item '.$updatemaster['ETIN'].' product created .',
								'type' => 'CWMS'
							]);
						}
					} else {
						$this->masterProduct->MakeProductHistory([
							'response' => Auth::user()->name . ' created Product: ' . $updatemaster['ETIN'],
							'master_product_id' => $master_product_id,
							'action' => 'Add'
						]);
						UserLogs([
							'user_id' => Auth::user()->id,
							'action' => 'Click',
							'task' => 'Add Product',
							'details' => 'Item '.$updatemaster['ETIN'].' product created .',
							'type' => 'CWMS'
						]);
					}
				}
			}

			$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $id)->first();

			$update_suppliment = [];
			if ($check_supplemental_mpt_data_queue) {
				$excluded_keys = ['id', 'created_at', 'updated_at'];
				foreach ($check_supplemental_mpt_data_queue as $key => $value) {
					if (!in_array($key, $excluded_keys)) {
						$update_suppliment[$key] = $value;
					}
				}
				$update_suppliment['master_product_id'] = $master_product_id;
				$check_supplemental_mpt_data = DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->first();
				if (!$check_supplemental_mpt_data) {
					DB::table('supplemental_mpt_data')->insert($update_suppliment);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Supplimental MPT Data Inserted.');
				} else {
					DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->update($update_suppliment);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Supplimental MPT Data Updated.');
				}
			}

			$ETIN = $check_master_product_queue->ETIN;
			$checkimage = DB::table('product_images_queue')->where('ETIN', $ETIN)->first();
			if ($checkimage) {
				$excluded_keys = ['id', 'created_at', 'updated_at'];
				foreach ($checkimage as $key => $value) {
					if (!in_array($key, $excluded_keys)) {
						$proimage[$key] = $value;
					}
				}
				$CIP = DB::table('product_images')->where('ETIN', $ETIN)->first();
				if ($CIP) {
					DB::table('product_images')->where('ETIN', $updatemaster['ETIN'])->update($proimage);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Product Images Updated.');
				} else {
					DB::table('product_images')->insert($proimage);
					$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Product Images Inserted.');
				}
			}
		}

		DB::table('master_product_images')->where('ETIN', $request->get('ETIN'))->update(['ETIN' => $updatemaster['ETIN']]);
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Master Product Images Updated.');
		$insert_image = $this->masterProduct->insertImageFzl($updatemaster['ETIN'], $request->all());
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Master Product Images Inserted.');

		DB::table('master_product_queue')->where('ETIN', $ETIN)->delete();
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Master Product Removed From Queue.');
		// DB::table('product_inventory_queue')->where('ETIN', $ETIN)->delete();
		// $this->masterProduct->insertProcessLog('ApproveProductRequest', 'Product Inventory Removed From Queue.');
		DB::table('product_images_queue')->where('ETIN', $ETIN)->delete();
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Product Images Removed From Queue.');
		DB::table('supplemental_mpt_data_queue')->where('master_product_id', $id)->delete();
		$this->masterProduct->insertProcessLog('ApproveProductRequest', 'Supplimental MPT Data Removed From Queue.');

		/* Notify other admins */		
		if(auth()->user()){
			$user = auth()->user();
			$note = $ETIN . " Edit Master Product Approved by ".$user->name;
			$url_id = '';

			$product = MasterProduct::where('ETIN', $ETIN )->first();
			if($product){
				$url_id = $product->id;
			}
			$url = '/editmasterproduct/'.$url_id.'/tab_comments';
			$type = "Approved Product Edit Request";
			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);
	
		}

		$data_info = [
			'msg' => 'Success',
			'error' => 0
		];


		return response()->json($data_info);
	}

	/*
	Upload master product from CSV file
	*/
	public function upload_csv_to_table(Request $request)
	{
		$errorRows = 0;
		$successRows = 0;

		$suppliername = $request->supplier_name;
		$draf_option = NULL;
		if (isset($request->select_option) && $request->select_option == 'Upload & Edit') $draf_option = 'd';
		$csv_header = DB::table('csv_header')->where('map_type', '=', $suppliername)->get();
		// dd($csv_header);
		$map_json_array = json_decode($csv_header[0]->{'map_data'});

		$suppliercolumnname = Schema::getColumnListing($suppliername);

		$file = $request->file('csv_file');
		$path = $file->getRealPath();

		$data = array_map('str_getcsv', file($path));
		$csv_data_for_header = array_slice($data, 0, 3);
		$csv_data = array_slice($data, 1, 500000);
		$csv_data_count = count($csv_data);

		$csvheader = $csv_data_for_header[0];

		foreach ($csv_data as $csv_data_single) {
			$keyarray = null;
			$keynumarray = null;

			foreach ($map_json_array as $key => $value) {

				if ($value) {
					if ($keynum = array_search(strtolower($value), array_map('strtolower', $csvheader))) {
						$keyarray[] = $key;
						$keynumarray[] = $keynum;
					}
				}
			}

			$insertarray = null;
			foreach ($keynumarray as $keynumsingle) {
				// $insertarray[] = htmlspecialchars(str_replace("<br />", "",nl2br($csv_data_single[$keynumsingle])),ENT_SUBSTITUTE);
				$insertarray[] = htmlspecialchars_decode(str_replace("<br />", "", nl2br($csv_data_single[$keynumsingle])), ENT_SUBSTITUTE);
			}

			$orderProductsData[] = array_combine($keyarray, $insertarray);
			$NewProductArray = [];

			if ($orderProductsData) {
				foreach ($orderProductsData as $row) {
					if (isset($row['product_temperature'])) {
						$lastrec = DB::table('master_product')->latest('id')->first();

						if ($lastrec) {
							$lastid = $lastrec->id;
							$lastetin = $lastrec->ETIN;
							if ($lastetin != '') {
								$etinarray = explode('-', $lastetin);
								if (end($etinarray) < 9999 && isset($etinarray[1]) && $etinarray[1] == 0) {
									$last_part = (isset($etinarray[2]) ? $etinarray[2] : 0);
									$last_part++;
									$Second_part = (isset($etinarray[1]) ? $etinarray[1] : 0);
								} else {
									$last_part = 0000;
									$Second_part = (isset($etinarray[1]) ? $etinarray[1] : 0);
									if ($Second_part == 0) $Second_part = 1000;
									$Second_part++;
								}
							} else {
								$Second_part = 0000;
								$last_part = 0001;
							}
						} else {
							$Second_part = 0000;
							$last_part = 0001;
						}

						if ($row['product_temperature'] == "Frozen") {
							$first_part = 'ETFZ';
						} else if ($row['product_temperature'] == "Dry-Strong") {
							$first_part = 'ETDS';
						} else if ($row['product_temperature'] == "Refrigerated") {
							$first_part = 'ETRF';
						} else if ($row['product_temperature'] == "Beverages") {
							$first_part = 'ETBV';
						} else if ($row['product_temperature'] == "Dry-Perishable") {
							$first_part = 'ETDP';
						} else if ($row['product_temperature'] == "Dry-Fragile") {
							$first_part = 'ETDF';
						} else if ($row['product_temperature'] == "Thaw & Serv") {
							$first_part = 'ETTS';
						} else {
							$first_part = 'ETOT';
						}
						if (
							$row['brand'] == '' ||
							$row['product_type'] == '' ||
							$row['unit_size'] == '' ||
							$row['pack_form_count'] == '' ||
							$row['unit_in_pack'] == '' ||
							$row['item_form_description'] == '' ||
							$row['product_category'] == '' ||
							$row['product_temperature'] == '' ||
							$row['supplier_product_number'] == '' ||
							$row['etailer_availability'] == '' ||
							$row['warehouses_assigned'] == '' ||
							$row['current_supplier'] == '' ||
							$row['supplier_status'] == '' ||
							$row['status'] == '' ||
							$row['cost'] == '' ||
							$row['unit_description'] == ''
						) {
							$errorRows += 1;
							continue;
						}

						//$row['ETIN'] = $first_part.'-'.str_pad($Second_part, 4, "0", STR_PAD_RIGHT).'-'.str_pad($last_part, 4, "0", STR_PAD_LEFT);
						$masterProduct = new MasterProduct;
						$row['ETIN'] = $masterProduct->getETIN($row['product_temperature']);
						//$row['manufacture'] = $row['brand']->manufacture->manufacture_name;
						$row['product_listing_name'] = $row['brand'] . ' ' . $row['flavor'] . ' ' . $row['product_type'] . ', ' . $row['unit_size'] . ' ' . $row['unit_description'] . ' (' . $row['pack_form_count'] . '-' . $row['unit_in_pack'] . ' ' . $row['item_form_description'] . ')';
						$row['created_at'] = date('Y-m-d H:i:s');
						$row['updated_at'] = date('Y-m-d H:i:s');
						if (isset($request->client_id)) {

							$row['lobs'] = $request->client_id;
						}

						if ($draf_option == 'd') {
							$row['queue_status'] = 'd';
						}
						$NewProductArray[] = $row;
						$successRows += 1;
					}
				}
			}


			// dd($NewProductArray);
			// dd($orderProductsData);
			$insert = DB::table($suppliername)->insert($NewProductArray);
			$this->masterProduct->insertProcessLog('CSVUpload', 'Record Added For "' . $suppliername . '".');
			$orderProductsData = null;
			$NewProductArray = [];
		}
		if ($errorRows > 0) {
			return response()->json([
				'error' => 1,
				'msg' => $successRows . " Rows Inserted Sucessfully, " . $errorRows . " don't have required values like (Brand, Product Type, Unit Size, Unit Description, Pack Form Count, Units in Pack, Item Form Description, Product Category, Product Temperature, Supplier Product Number, e-tailer Availability, Current Supplier, Supplier Status, Cost, Warehouse(s) Assigned, Status)"
			]);
		}

		return response()->json([
			'error' => 0,
			'msg' => $successRows . " Rows Inserted Sucessfully"
		]);

		// 			return redirect()->back()->with('error', $successRows." Rows Inserted Sucessfully, ".$errorRows." don't have required values like (Brand, Product Type, Unit Size, Unit Description, Pack Form Count, Units in Pack, Item Form Description, Product Category, Product Temperature, Supplier Product Number, e-tailer Availability, Current Supplier, Supplier Status, Cost, Warehouse(s) Assigned, Status)");

		// return redirect()->back()->with('success', );

	}

	public function update_data_using_csv(Request $request)
	{

		$requestsuppliername = $request->supplier_name;
		$supplier_table_columnname = Schema::getColumnListing($requestsuppliername);
		$requestsuppliername = $request->supplier_name;

		$requestsuppliername = $request->supplier_name;
		$csv_header_map = DB::table('csv_header')->where('map_type', '=', $requestsuppliername)->get();
		$map_json_array = json_decode($csv_header_map[0]->{'map_data'});
		$file = $request->file('csv_file');
		$path = $file->getRealPath();
		$data = array_map('str_getcsv', file($path));
		// dd($data);
		$csv_data_for_header = array_slice($data, 0, 5);
		$csvheader = $csv_data_for_header[0];

		$csv_data = array_slice($data, 1, 50000);
		$csv_data_count = count($csv_data);


		if ($supplier_table_columnname !== false) {
			$upckeynum = array_search(strtolower('UPC'), array_map('strtolower', $csvheader));

			foreach ($csv_data as $csv_data_single) {
				$keyarray = null;
				$keynumarray = null;

				foreach ($map_json_array as $key => $value) {
					if ($value) {
						if ($value !== 'UPC') {
							if ($keynum = array_search(strtolower($value), array_map('strtolower', $csvheader))) {
								$keyarray[] = $key;
								$keynumarray[] = $keynum;
							}
						}
					}
				}

				$updatearray = null;
				foreach ($keynumarray as $keynumsingle) {
					$updatearray[] = htmlspecialchars(str_replace("<br />", "", nl2br($csv_data_single[$keynumsingle])), ENT_SUBSTITUTE);
				}

				$orderProductsData[] = array_combine($keyarray, $updatearray);


				if (DB::table($requestsuppliername)->where('UPC', $csv_data_single[$upckeynum])->exists()) {

					$dbupdate = DB::table($requestsuppliername)
						->where('UPC', $csv_data_single[$upckeynum])
						->update($orderProductsData[0]);
					//$output[] = "UPC ".$csv_data_single[$upckeynum]."Updated.";
					// FIRE MAIL FUNCTIONALITY ON UPDATE (Future Scope)
				}
				/*$user = DB::table($requestsuppliername)->where('UPC', '=', $csv_data_single[$upckeynum])->first();
						if ($user === null) {


							$insert = DB::table($requestsuppliername)->insert($orderProductsData[0]);
							$output[] = "UPC ".$csv_data_single[$upckeynum]."Inserted.";
						}*/

				$orderProductsData = null;
			}

			return redirect()->back()->with('message', 'Records are updated..');
		} else {
			return redirect()->back()->with('html', 'ETIN field is not available at selected table. Please try with diffrent Supplier.');
		}
	}

	public function upload_csv_to_other_tables(Request $request)
	{
		$suppliername = $request->supplier_name;
		$csv_header = DB::table('csv_header')->where('map_type', '=', $suppliername)->get();

		$map_json_array = json_decode($csv_header[0]->{'map_data'});

		$suppliercolumnname = Schema::getColumnListing($suppliername);

		$file = $request->file('csv_file');
		$path = $file->getRealPath();

		$data = array_map('str_getcsv', file($path));
		$csv_data_for_header = array_slice($data, 0, 5);
		$csv_data = array_slice($data, 1, 50000);
		$csv_data_count = count($csv_data);

		$csvheader = $csv_data_for_header[0];

		foreach ($csv_data as $csv_data_single) {
			$keyarray = null;
			$keynumarray = null;

			foreach ($map_json_array as $key => $value) {

				if ($value) {
					if ($keynum = array_search(strtolower($value), array_map('strtolower', $csvheader))) {
						$keyarray[] = $key;
						$keynumarray[] = $keynum;
					}
				}
			}

			$insertarray = null;
			foreach ($keynumarray as $keynumsingle) {
				$insertarray[] = htmlspecialchars(str_replace("<br />", "", nl2br($csv_data_single[$keynumsingle])), ENT_SUBSTITUTE);
			}

			$orderProductsData[] = array_combine($keyarray, $insertarray);
			$insert = DB::table($suppliername)->insert($orderProductsData);
			$orderProductsData = null;
		}
		return redirect()->back()->with('message', 'Data Inserted Sucessfully...');
	}

	public function new_manufacturers_request(Request $request)
	{
		$result = new RequestProductSelection;
		$result->request_type = 'Manufacturers';
		$result->request_field = $request->request_field;
		$result->save();

		if ($result) {
			$data_info = [
				'msg' => 'Success',
				'error' => 0
			];
		} else {
			$data_info = [
				'msg' => 'Something wend wrong',
				'error' => 1
			];
		}
		return response()->json($data_info);
	}

	public function new_brand_request(Request $request)
	{
		$result = new RequestProductSelection;
		$result->request_type = 'Brand';
		$result->request_field = $request->request_field;
		$result->save();

		if ($result) {
			$data_info = [
				'msg' => 'Success',
				'error' => 0
			];
		} else {
			$data_info = [
				'msg' => 'Something wend wrong',
				'error' => 1
			];
		}
		return response()->json($data_info);
	}

	public function new_product_type_request(Request $request)
	{
		$result = new RequestProductSelection;
		$result->request_type = 'Product Type';
		$result->request_field = $request->request_field;
		$result->save();

		if ($result) {
			$data_info = [
				'msg' => 'Success',
				'error' => 0
			];
		} else {
			$data_info = [
				'msg' => 'Something wend wrong',
				'error' => 1
			];
		}
		return response()->json($data_info);
	}

	public function new_product_type_kit_request(Request $request)
	{
		$result = new RequestProductSelection;
		$result->request_type = 'Product Type (Kit Description)';
		$result->request_field = $request->request_field;
		$result->save();

		if ($result) {
			$data_info = [
				'msg' => 'Success',
				'error' => 0
			];
		} else {
			$data_info = [
				'msg' => 'Something wend wrong',
				'error' => 1
			];
		}
		return response()->json($data_info);
	}

	public function new_unit_description_request(Request $request)
	{
		$result = new RequestProductSelection;
		$result->request_type = 'Unit Description';
		$result->request_field = $request->request_field;
		$result->save();

		if ($result) {
			$data_info = [
				'msg' => 'Success',
				'error' => 0
			];
		} else {
			$data_info = [
				'msg' => 'Something wend wrong',
				'error' => 1
			];
		}
		return response()->json($data_info);
	}

	public function saveAsDraft(Request $request)
	{		
		$insertmasterproduct = [];
		$insertimgs = [];

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Images Validated.');


		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1) {
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if ($request->get('product_temperature') == "Frozen") {
				$insertmasterproduct['ETIN'] = 'ETFZ-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$insertmasterproduct['ETIN'] = 'ETDS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$insertmasterproduct['ETIN'] = 'ETRF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$insertmasterproduct['ETIN'] = 'ETBV-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$insertmasterproduct['ETIN'] = 'ETDP-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$insertmasterproduct['ETIN'] = 'ETDF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$insertmasterproduct['ETIN'] = 'ETTS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			}
		} else {
			$insertmasterproduct['ETIN'] = $request->get('ETIN');
		}

		$insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'], $request->all());
		$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Images Inserted.');

		//Etin ready for ProductImage table
		if ($request->get('product_temperature')) {
			$insertimgs['ETIN'] = $insertmasterproduct['ETIN'];
		}

		$id = $request->get('id');
		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertmasterproduct['product_listing_name'] = $request->get('product_listing_name');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#', $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
		$insertmasterproduct['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$insertmasterproduct['unit_description'] = $request->get('unit_description');
		$insertmasterproduct['pack_form_count'] = $request->get('pack_form_count');
		$insertmasterproduct['item_form_description'] = $request->get('item_form_description');
		$insertmasterproduct['total_ounces'] = $request->get('total_ounces');
		$insertmasterproduct['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$insertmasterproduct['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$insertmasterproduct['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$insertmasterproduct['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$insertmasterproduct['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$insertmasterproduct['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$insertmasterproduct['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$insertmasterproduct['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$insertmasterproduct['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$insertmasterproduct['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		// $insertmasterproduct['product_tags'] = implode(',' , $request->get('product_tags'));
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = ProperInput($request->get('storage'));
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['supplier_status'] = $request->get('supplier_status');
		$insertmasterproduct['upc'] = ProperInput($request->get('upc'));
		$insertmasterproduct['gtin'] = ProperInput($request->get('gtin'));
		$insertmasterproduct['asin'] = ProperInput($request->get('asin'));
		$insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['GPC_code'] = ProperInput($request->get('GPC_code'));
		$insertmasterproduct['GPC_class'] = ProperInput($request->get('GPC_class'));
		$insertmasterproduct['HS_code'] = ProperInput($request->get('HS_code'));
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = ProperInput($request->get('package_information'));
		$insertmasterproduct['cost'] = $request->get('cost');
		$insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['dropship_available'] = $request->get('dropship_available');
		$insertmasterproduct['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		if ($request->get('warehouses_assigned')) {
			$insertmasterproduct['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		} else {
			$insertmasterproduct['warehouses_assigned'] = '';
		}

		// $updatemaster['warehouses_assigned'] = $request->get('warehouses_assigned');
		$insertmasterproduct['status_date'] = $request->get('status_date');
		// $insertmasterproduct['lobs'] = implode(',' , $request->get('lobs'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['chanel_ids'] = $request->get('chanel_ids');
		$insertmasterproduct['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertmasterproduct['client_supplier_id'] = $request->get('current_supplier');
		$insertmasterproduct['alternate_ETINs'] = $request->get('alternate_ETINs');
		$insertmasterproduct['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertmasterproduct['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$insertmasterproduct['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');

		$insertmasterproduct['queue_status'] = $request->get('queue_status');

		$insertmasterproduct['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';
		$insertmasterproduct['week_worth_qty'] = $request->get('week_worth_qty') != null || $request->get('week_worth_qty') > 0 ? $request->get('week_worth_qty') : 0;
		$insertmasterproduct['min_order_qty'] = $request->get('min_order_qty') != null || $request->get('min_order_qty') > 0 ? $request->get('min_order_qty') : 0;
		$insertmasterproduct['lead_time'] = $request->get('lead_time');

		if ($request->get('warehouses_assigned')) {
			$insertmasterproductinventory['ETIN'] = $insertmasterproduct['ETIN'];
		} else {
			$insertmasterproductinventory['ETIN'] = '';
		}
		// $insertmasterproduct['updated_by'] = Auth::user()->id;
		// $insertmasterproduct['inserted_by'] = Auth::user()->id;

		$existsEtin = DB::table('master_product_queue')->where('ETIN', $insertmasterproduct['ETIN'])->first();

		if (!$existsEtin) {
			$insertmasterproduct['inserted_by'] = Auth::user()->id;
			$master_product_id = DB::table('master_product_queue')->insertGetId($insertmasterproduct);
			$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Product Added To Queue.');
			$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $master_product_id)->first();
			if (!$check_supplemental_mpt_data_queue) {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['master_product_id'] = $master_product_id;
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['created_at'] = date('Y-m-d H:i:s');
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data_queue')->insert($insertmpt);
				$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Supplemental MPT Data Added To Queue.');
			} else {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data_queue')->where('master_product_id', $master_product_id)->update($insertmpt);
				$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Supplemental MPT Data Updated To Queue.');
			}
		} else {
			$insertmasterproduct['updated_by'] = Auth::user()->id;
			$affected = DB::table('master_product_queue')->where('ETIN', $insertmasterproduct['ETIN'])->update($insertmasterproduct);
			$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Peoduct Added To Queue.');
			$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $existsEtin->id)->first();
			if (!$check_supplemental_mpt_data_queue) {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['master_product_id'] = $existsEtin->id;
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['created_at'] = date('Y-m-d H:i:s');
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data_queue')->insert($insertmpt);
				$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Supplemental MPT Data Added To Queue.');
			} else {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = ProperInput($request->get('unit_upc'));
				$insertmpt['gtin'] = ProperInput($request->get('unit_gtin'));
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data_queue')->where('master_product_id', $existsEtin->id)->update($insertmpt);
				$this->masterProduct->insertProcessLog('SaveAsDraftMasterProduct', 'Supplemental MPT Data Updated To Queue.');
			}
		}
		
		/* Notify other admins */		
		if(auth()->user()){
			$draft_product = DB::table('master_product_queue')->where('ETIN', $insertmasterproduct['ETIN'])->first();

			$user = auth()->user();
			$note = $insertmasterproduct['ETIN'] . " Saved as Draft by ".$user->name;
			$url_id = '';
			if($draft_product){
				$url_id = $draft_product->id;
			}
			$url = '/editmasterrequestview/'.$url_id.'/tab_comments';
			$type = "Product Draft Request";

			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);
				
		}
		
		UserLogs([
			'user_id' => Auth::user()->id,
			'action' => 'Click',
			'task' => 'Save As Draft Product',
			'details' => 'Item '.$insertmasterproduct['ETIN'].' product save as draft .',
			'type' => 'CWMS'
		]);
		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Request Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
	}

	public function updateRequest(Request $request)
	{

		$updatemaster = [];
		$updateinventory = [];
		$insertimgs = [];
		$id = $request->get('master_product_id');
		$queue_id = $request->get('id');

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('UpdateRequest', 'Images Validated.');

		if (isset($request->upc_present) && !isset($request->upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "UPC is missing"
			]);
		}

		if (isset($request->gtin_present) && !isset($request->gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "GTIN is missing"
			]);
		}

		if (isset($request->unit_upc_present) && !isset($request->unit_upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit UPC is missing"
			]);
		}

		if (isset($request->unit_gtin_present) && !isset($request->unit_gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit GTIN is missing"
			]);
		}

		//$updatemaster['ETIN'] = $request->get('ETIN');
		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));

			if (count($explodearray) > 1) {
				$updatemaster['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			}
			if ($request->get('product_temperature') == "Frozen") {
				$updatemaster['ETIN'] = 'ETFZ-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$updatemaster['ETIN'] = 'ETDS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$updatemaster['ETIN'] = 'ETRF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$updatemaster['ETIN'] = 'ETBV-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$updatemaster['ETIN'] = 'ETDP-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$updatemaster['ETIN'] = 'ETDF-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$updatemaster['ETIN'] = 'ETTS-' . $etinmid . '-' . $updatemaster['ETIN'];
			} else {
				$updatemaster['ETIN'] = 'ETOT-' . $etinmid . '-' . $updatemaster['ETIN'];
			}
		} else {
			$updatemaster['ETIN'] = $request->get('ETIN');
		}


		//Etin ready for ProductImage table
		$insertimgs['ETIN'] = $updatemaster['ETIN'];
		$updatemaster['parent_ETIN'] = $request->get('parent_ETIN');
		//$updatemaster['product_listing_name'] = $request->get('product_listing_name');
		$updatemaster['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$updatemaster['about_this_item'] = implode('#', $request->get('about_this_item'));
		$updatemaster['manufacturer'] = $request->get('manufacturer');
		$updatemaster['brand'] = $request->get('brand');
		$updatemaster['flavor'] = ProperInput($request->get('flavor'));
		$updatemaster['product_type'] = $request->get('product_type');
		$updatemaster['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$updatemaster['unit_description'] = $request->get('unit_description');
		$updatemaster['pack_form_count'] = $request->get('pack_form_count');
		$updatemaster['item_form_description'] = $request->get('item_form_description');
		$updatemaster['total_ounces'] = $request->get('total_ounces');
		$updatemaster['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$updatemaster['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$updatemaster['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$updatemaster['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$updatemaster['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$updatemaster['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$updatemaster['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$updatemaster['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$updatemaster['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$updatemaster['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$updatemaster['key_product_attributes_diet'] = ProperInput($request->get('key_product_attributes_diet'));
		$updatemaster['product_tags'] = $request->get('product_tags');
		$updatemaster['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$updatemaster['hazardous_materials'] = $request->get('hazardous_materials');
		$updatemaster['storage'] = ProperInput($request->get('storage'));
		$updatemaster['ingredients'] = $request->get('ingredients');
		$updatemaster['allergens'] = $request->get('allergens');
		$updatemaster['prop_65_flag'] = $request->get('prop_65_flag');
		$updatemaster['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$updatemaster['product_temperature'] = $request->get('product_temperature');
		$updatemaster['supplier_status'] = $request->get('supplier_status');
		$updatemaster['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
		$updatemaster['gtin'] = isset($request->upc) ? ProperInput($request->get('gtin')) : '';
		$updatemaster['asin'] = ProperInput($request->get('asin'));
		$updatemaster['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $updatemaster['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $updatemaster['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $updatemaster['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$updatemaster['GPC_code'] = ProperInput($request->get('GPC_code'));
		$updatemaster['GPC_class'] = ProperInput($request->get('GPC_class'));
		$updatemaster['HS_code'] = ProperInput($request->get('HS_code'));
		$updatemaster['weight'] = $request->get('weight');
		$updatemaster['length'] = $request->get('length');
		$updatemaster['width'] = $request->get('width');
		$updatemaster['height'] = $request->get('height');
		$updatemaster['country_of_origin'] = $request->get('country_of_origin');
		$updatemaster['package_information'] = ProperInput($request->get('package_information'));
		$updatemaster['cost'] = $request->get('cost');
		$updatemaster['acquisition_cost'] = $request->get('acquisition_cost');
		$updatemaster['new_cost'] = $request->get('new_cost');
		$updatemaster['new_cost_date'] = $request->get('new_cost_date');
		$updatemaster['status'] = $request->get('status');
		$updatemaster['etailer_availability'] = $request->get('etailer_availability');
		$updatemaster['dropship_available'] = $request->get('dropship_available');
		$updatemaster['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$updatemaster['POG_flag'] = $request->get('POG_flag');
		$updatemaster['consignment'] = $request->get('consignment');
		if ($request->get('warehouses_assigned')) {
			$updatemaster['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		} else {
			$updatemaster['warehouses_assigned'] = '';
		}
		// $updatemaster['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$updatemaster['status_date'] = $request->get('status_date');
		$updatemaster['lobs'] = $request->get('lobs');
		$updatemaster['chanel_ids'] = $request->get('chanel_ids');
		$updatemaster['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$updatemaster['client_supplier_id'] = $request->get('current_supplier');
		$updatemaster['alternate_ETINs'] = $request->get('alternate_ETINs');
		$updatemaster['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$updatemaster['unit_in_pack'] = $request->get('unit_in_pack');
		$updatemaster['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$updatemaster['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$updatemaster['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$updatemaster['is_edit'] = 1;
		$updatemaster['is_approve'] = $request->get('is_approve');
		$updatemaster['approved_date'] = $request->get('approved_date');
		$updatemaster['updated_by'] = Auth::user()->id;

		//adding inventory
		$updateinventory['ETIN'] = $updatemaster['ETIN'];

		$updatemaster['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

		// Insert previous record in history
		$oldrecord = DB::table('master_product')->find($id);
		$oldrecordarray = (array)$oldrecord;
		$oldrecordarray['id'] = null;
		$oldrecordarray['updated_by'] = Auth::user()->id;
		$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
		$this->masterProduct->insertProcessLog('UpdateRequest', 'Old Product Added To Master Product History.');
		$check_master_product_queue = DB::table('master_product_queue')->where('id', $queue_id)->first();
		if (!$check_master_product_queue) {
			$updatemaster['inserted_by'] = $oldrecordarray['inserted_by'];
			$affected = DB::table('master_product_queue')->insert($updatemaster);
			UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Product Added To Queue',
                'details' => 'Product '.$updatemaster['ETIN'].' added to queue',
                'type' => 'CWMS'
            ]);
			$this->masterProduct->insertProcessLog('UpdateRequest', 'Product Added To Queue.');
		} else {
			$updatemaster['updated_by'] = Auth::user()->id;
			$affected = DB::table('master_product_queue')->where('id', $queue_id)->update($updatemaster);
			UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Product update To Queue',
                'details' => 'Product '.$updatemaster['ETIN'].' update to queue',
                'type' => 'CWMS'
            ]);
			$this->masterProduct->insertProcessLog('UpdateRequest', 'Product Updated In Queue.');
		}

		$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $queue_id)->first();
		if (!$check_supplemental_mpt_data_queue) {
			$insertmpt['ETIN'] = $updatemaster['ETIN'];
			$insertmpt['master_product_id'] = $queue_id;
			$insertmpt['weight'] = $request->get('unit_weight');
			$insertmpt['length'] = $request->get('unit_length');
			$insertmpt['width'] = $request->get('unit_width');
			$insertmpt['height'] = $request->get('unit_height');
			$insertmpt['upc'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
			$insertmpt['gtin'] = isset($request->upc) ? ProperInput($request->get('unit_gtin')) : '';
			$insertmpt['created_at'] = date('Y-m-d H:i:s');
			$insertmpt['updated_at'] = date('Y-m-d H:i:s');
			DB::table('supplemental_mpt_data_queue')->insert($insertmpt);
			$this->masterProduct->insertProcessLog('UpdateRequest', 'Supplemental MPT Data Added To Queue.');
		} else {
			$insertmpt['ETIN'] = $updatemaster['ETIN'];
			$insertmpt['weight'] = $request->get('unit_weight');
			$insertmpt['length'] = $request->get('unit_length');
			$insertmpt['width'] = $request->get('unit_width');
			$insertmpt['height'] = $request->get('unit_height');
			$insertmpt['upc'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
			$insertmpt['gtin'] = isset($request->upc) ? ProperInput($request->get('unit_gtin')) : '';
			$insertmpt['updated_at'] = date('Y-m-d H:i:s');
			DB::table('supplemental_mpt_data_queue')->where('master_product_id', $queue_id)->update($insertmpt);
			$this->masterProduct->insertProcessLog('UpdateRequest', 'Supplemental MPT Data Updated To Queue.');
		}

		DB::table('master_product_images')->where('ETIN', $request->get('ETIN'))->update(['ETIN' => $updatemaster['ETIN']]);
		$this->masterProduct->insertProcessLog('UpdateRequest', 'Product Images Updated.');

		$insert_image = $this->masterProduct->insertImageFzl($updatemaster['ETIN'], $request->all());
		$this->masterProduct->insertProcessLog('UpdateRequest', 'Product Images Inserted.');


		DB::table('master_product')->where('id', $id)->update([
			'product_edit_request' => 1,
			'updated_by' => Auth::user()->id
		]);
		$this->masterProduct->insertProcessLog('UpdateRequest', 'Master Product Data Updated.');

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Request is updated.',
			'url' => url('/allmasterproductlsts')
		]);
	}

	public function insertnewmasterwizard(MasterProductRequest $request)
	{

		$insertmasterproduct = [];
		$insertimgs = [];
		$insertmpt = [];

		if (isset($request->upc_present) && !isset($request->upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "UPC is missing"
			]);
		}

		if (isset($request->gtin_present) && !isset($request->gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "GTIN is missing"
			]);
		}

		if (isset($request->unit_upc_present) && !isset($request->unit_upc)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit UPC is missing"
			]);
		}

		if (isset($request->unit_gtin_present) && !isset($request->unit_gtin)) {
			return response()->json([
				'error' => 1,
				'msg' => "Unit GTIN is missing"
			]);
		}

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1) {
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if ($request->get('product_temperature') == "Frozen") {
				$insertmasterproduct['ETIN'] = 'ETFZ-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$insertmasterproduct['ETIN'] = 'ETDS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$insertmasterproduct['ETIN'] = 'ETRF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$insertmasterproduct['ETIN'] = 'ETBV-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$insertmasterproduct['ETIN'] = 'ETDP-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$insertmasterproduct['ETIN'] = 'ETDF-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$insertmasterproduct['ETIN'] = 'ETTS-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-' . $etinmid . '-' . $insertmasterproduct['ETIN'];
			}
		}


		//Etin ready for ProductImage table
		$insertimgs['ETIN'] = $insertmasterproduct['ETIN'];

		$id = $request->get('id');

		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertmasterproduct['product_listing_name'] = $request->get('product_listing_name');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#', $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
		$insertmasterproduct['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$insertmasterproduct['unit_description'] = $request->get('unit_description');
		$insertmasterproduct['pack_form_count'] = $request->get('pack_form_count');
		$insertmasterproduct['item_form_description'] = ProperInput($request->get('item_form_description'));
		$insertmasterproduct['total_ounces'] = $request->get('total_ounces');
		$insertmasterproduct['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$insertmasterproduct['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$insertmasterproduct['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$insertmasterproduct['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$insertmasterproduct['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$insertmasterproduct['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$insertmasterproduct['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$insertmasterproduct['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$insertmasterproduct['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$insertmasterproduct['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$insertmasterproduct['key_product_attributes_diet'] = ProperInput($request->get('key_product_attributes_diet'));
		// $insertmasterproduct['product_tags'] = implode(',' , $request->get('product_tags'));
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = ProperInput($request->get('storage'));
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['supplier_status'] = $request->get('supplier_status');
		$insertmasterproduct['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
		$insertmasterproduct['gtin'] = isset($request->gtin) ? ProperInput($request->get('gtin')) : '';
		$insertmasterproduct['asin'] = ProperInput($request->get('asin'));
		$insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['GPC_code'] = ProperInput($request->get('GPC_code'));
		$insertmasterproduct['GPC_class'] = ProperInput($request->get('GPC_class'));
		$insertmasterproduct['HS_code'] = ProperInput($request->get('HS_code'));
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = ProperInput($request->get('package_information'));
		$insertmasterproduct['cost'] = $request->get('cost');
		$insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['dropship_available'] = $request->get('dropship_available');
		$insertmasterproduct['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		// $updatemaster['warehouses_assigned'] = $request->get('warehouses_assigned');
		$insertmasterproduct['status_date'] = $request->get('status_date');
		// $insertmasterproduct['lobs'] = implode(',' , $request->get('lobs'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['chanel_ids'] = $request->get('chanel_ids');
		$insertmasterproduct['client_supplier_id'] = $request->get('current_supplier');
		$insertmasterproduct['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertmasterproduct['alternate_ETINs'] = $request->get('alternate_ETINs');
		$insertmasterproduct['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertmasterproduct['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$insertmasterproduct['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['week_worth_qty'] = $request->get('week_worth_qty') != null || $request->get('week_worth_qty') > 0 ? $request->get('week_worth_qty') : 0;
		$insertmasterproduct['min_order_qty'] = $request->get('min_order_qty') != null || $request->get('min_order_qty') > 0 ? $request->get('min_order_qty') : 0;


		$insertmasterproduct['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';
		$insertmasterproduct['is_wl'] = isset($request->list_type) && $request->get('list_type') === 'w_list' ? 1 : 0;
		$insertmasterproduct['is_bl'] = isset($request->list_type) && $request->get('list_type') === 'b_list' ? 1 : 0;

		$insertmasterproductinventory['ETIN'] = $insertmasterproduct['ETIN'];
		// $insertmasterproductinventory['W1_Orderable_Quantity'] = $request->get('W1_Orderable_Quantity');
		// $insertmasterproductinventory['W2_Orderable_Quantity'] = $request->get('W2_Orderable_Quantity');
		// $insertmasterproductinventory['W3_Orderable_Quantity'] = $request->get('W3_Orderable_Quantity');



		// Upload Image 1
		if ($request->hasFile('Image_URL1_Primary')) {

			$img1 = $request->file('Image_URL1_Primary');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name1 = time() . '-' . $img1->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name1;
			$s3->put($s3filePath, file_get_contents($img1), 'public');
			$imgurl1 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL1_Primary'] = $imgurl1;
			$insertimgs['Image_URL1_Alt_Text'] = $request->get('Image_URL1_Alt_Text');
		}

		// Upload Image2
		if ($request->hasFile('Image_URL2_Front')) {

			$img2 = $request->file('Image_URL2_Front');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name2 = time() . '-' . $img2->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name2;
			$s3->put($s3filePath, file_get_contents($img2), 'public');
			$imgurl2 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL2_Front'] = $imgurl2;
			$insertimgs['Image_URL2_Alt_Text'] = $request->get('Image_URL2_Alt_Text');
		}

		// Upload Image 3
		if ($request->hasFile('Image_URL3_Back')) {

			$img3 = $request->file('Image_URL3_Back');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name3 = time() . '-' . $img3->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name3;
			$s3->put($s3filePath, file_get_contents($img3), 'public');
			$imgurl3 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL3_Back'] = $imgurl3;
			$insertimgs['Image_URL3_Alt_Text'] = $request->get('Image_URL3_Alt_Text');
		}

		// Upload Image 4
		if ($request->hasFile('Image_URL4_Left')) {

			$img4 = $request->file('Image_URL4_Left');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name4 = time() . '-' . $img4->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name4;
			$s3->put($s3filePath, file_get_contents($img4), 'public');
			$imgurl4 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL4_Left'] = $imgurl4;
			$insertimgs['Image_URL4_Alt_Text'] = $request->get('Image_URL4_Alt_Text');
		}

		// Upload Image 5
		if ($request->hasFile('Image_URL5_Right')) {

			$img5 = $request->file('Image_URL5_Right');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name5 = time() . '-' . $img5->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name5;
			$s3->put($s3filePath, file_get_contents($img5), 'public');
			$imgurl5 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL5_Right'] = $imgurl5;
			$insertimgs['Image_URL5_Alt_Text'] = $request->get('Image_URL5_Alt_Text');
		}
		// Upload Image 6
		if ($request->hasFile('Image_URL6_Top')) {

			$img6 = $request->file('Image_URL6_Top');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name6 = time() . '-' . $img6->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name6;
			$s3->put($s3filePath, file_get_contents($img6), 'public');
			$imgurl6 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL6_Top'] = $imgurl6;
			$insertimgs['Image_URL6_Alt_Text'] = $request->get('Image_URL6_Alt_Text');
		}
		// Upload Image 7
		if ($request->hasFile('Image_URL7_Bottom')) {

			$img7 = $request->file('Image_URL7_Bottom');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name7 = time() . '-' . $img7->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name7;
			$s3->put($s3filePath, file_get_contents($img7), 'public');
			$imgurl7 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL7_Bottom'] = $imgurl7;
			$insertimgs['Image_URL7_Alt_Text'] = $request->get('Image_URL7_Alt_Text');
		}
		// Upload Image 8
		if ($request->hasFile('Image_URL8')) {

			$img8 = $request->file('Image_URL8');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name8 = time() . '-' . $img8->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name8;
			$s3->put($s3filePath, file_get_contents($img8), 'public');
			$imgurl8 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_UinsertnewmasterwizardRL8'] = $imgurl8;
			$insertimgs['Image_URL8_Alt_Text'] = $request->get('Image_URL8_Alt_Text');
		}
		// Upload Image 9
		if ($request->hasFile('Image_URL9')) {

			$img9 = $request->file('Image_URL9');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name9 = time() . '-' . $img9->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name9;
			$s3->put($s3filePath, file_get_contents($img9), 'public');
			$imgurl9 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL9'] = $imgurl9;
			$insertimgs['Image_URL9_Alt_Text'] = $request->get('Image_URL9_Alt_Text');
		}
		// Upload Image 10
		if ($request->hasFile('Image_URL10')) {

			$img10 = $request->file('Image_URL10');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name10 = time() . '-' . $img10->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name10;
			$s3->put($s3filePath, file_get_contents($img10), 'public');
			$imgurl10 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Image_URL10'] = $imgurl10;
			$insertimgs['Image_URL10_Alt_Text'] = $request->get('Image_URL10_Alt_Text');
		}
		// Upload Image 11
		if ($request->hasFile('Nutritional_Image_URL1')) {

			$img11 = $request->file('Nutritional_Image_URL1');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name11 = time() . '-' . $img11->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name11;
			$s3->put($s3filePath, file_get_contents($img11), 'public');
			$imgurl11 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Nutritional_Image_URL1'] = $imgurl11;
			$insertimgs['Nutritional_Image_URL1_Alt_Text'] = $request->get('Nutritional_Image_URL1_Alt_Text');
		}
		// Upload Image 12
		if ($request->hasFile('Nutritional_Image_URL2')) {

			$img12 = $request->file('Nutritional_Image_URL2');
			$directory = $insertmasterproduct['ETIN'];

			if (!Storage::disk('s3')->exists($directory)) {

				$s3 = Storage::disk('s3')->makeDirectory($directory);
			}
			$s3 = Storage::disk('s3');
			$file_name12 = time() . '-' . $img12->getClientOriginalName();
			$s3filePath = $directory . '/' . $file_name12;
			$s3->put($s3filePath, file_get_contents($img12), 'public');
			$imgurl12 = Storage::disk('s3')->url($s3filePath);

			$insertimgs['Nutritional_Image_URL2'] = $imgurl12;
			$insertimgs['Nutritional_Image_URL2_Alt_Text'] = $request->get('Nutritional_Image_URL2_Alt_Text');
		}



		// Checking Images by ETIN
		$existsimage = DB::table('product_images')->where('ETIN', $request->get('ETIN'))->first();
		if (!$existsimage) {
			if (count($insertimgs) > 2) {
				$imageurlinsert = DB::table('product_images')->insert($insertimgs);
			}
		}
		// check UPC
		$existsupc = isset($request->upc) ? DB::table('master_product')->where('UPC', $request->get('upc'))->first() : false;
		$existsgtin = isset($request->upc) ? DB::table('master_product')->where('GTIN', $request->get('gtin'))->first() : false;

		if (!$existsupc && !$existsgtin) {
			$insertmasterproduct['inserted_by'] = Auth::user()->id;
			$master_product_id = DB::table('master_product')->insertGetId($insertmasterproduct);
			$this->masterProduct->MakeProductHistory([
				'response' => Auth::user()->name . ' created Product: ' . $insertmasterproduct['ETIN'],
				'master_product_id' => $master_product_id,
				'action' => 'Add'
			]);
			// $checkinventory = DB::table('product_inventory')->where('ETIN',$insertmasterproduct['ETIN'] )->first();
			// if(!$checkinventory){
			// 	$insertmasterproductinventory['created_at'] = date('Y-m-d H:i:s');
			// 	$insertmasterproductinventory['updated_at'] = date('Y-m-d H:i:s');
			// 	$insertmasterproductinventory['master_product_id'] = $master_product_id;
			// 	$inventoryeffect = DB::table('product_inventory')->insert($insertmasterproductinventory);
			// } else {
			// 	$insertmasterproductinventory['updated_at'] = date('Y-m-d H:i:s');
			// 	$inventoryeffect = DB::table('product_inventory')->where('id', $id)->update($insertmasterproductinventory);
			// }

			$check_supplemental_mpt_data = DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->first();
			if (!$check_supplemental_mpt_data) {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['master_product_id'] = $master_product_id;
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
				$insertmpt['gtin'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
				$insertmpt['created_at'] = date('Y-m-d H:i:s');
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data')->insert($insertmpt);
			} else {
				$insertmpt['ETIN'] = $insertmasterproduct['ETIN'];
				$insertmpt['weight'] = $request->get('unit_weight');
				$insertmpt['length'] = $request->get('unit_length');
				$insertmpt['width'] = $request->get('unit_width');
				$insertmpt['height'] = $request->get('unit_height');
				$insertmpt['upc'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
				$insertmpt['gtin'] = isset($request->upc) ? ProperInput($request->get('unit_upc')) : '';
				$insertmpt['updated_at'] = date('Y-m-d H:i:s');
				DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->update($insertmpt);
			}
		} else {

			if ($existsupc && $existsgtin) {
				$upcetin = $existsupc->ETIN;
				$gtinetin = $existsgtin->ETIN;
				if ($upcetin == $gtinetin) {

					$msg = "UPC " . $request->get('upc') . " and GTIN " . $request->get('gtin') . " already exists for ETIN # " . $upcetin . " and cannot be added.";
				} else {

					$msg = "UPC " . $request->get('upc') . " is already exists for ETIN # " . $upcetin . " , GTIN " . $request->get('gtin') . " is exists for ETIN # " . $gtinetin . " and cannot be added.";
				}
			} else {
				if ($existsupc) {
					$upcetin = $existsupc->ETIN;
					$msg = "UPC " . $request->get('upc') . " already exists for ETIN # " . $upcetin . " and cannot be added.";
				} else if ($existsgtin) {
					$gtinetin = $existsgtin->ETIN;
					$msg = "GTIN " . $request->get('gtin') . " already exists for ETIN # " . $gtinetin . " and cannot be added.";
				}
			}


			return response()->json([
				'error' => 1,
				'msg' => $msg
			]);
		}

		MasterProductQueue::where('ETIN', $insertmasterproduct['ETIN'])->delete();
		SupplementalMptDataQueue::where('ETIN', $insertmasterproduct['ETIN'])->delete();

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
	}

	public function removeImage($id)
	{
		$image = DB::table('master_product_images')->find($id);
		$image_path = explode('.com/', $image->image_url)[1];
		Storage::disk('s3')->delete($image_path);
		$result = DB::table('master_product_images')->where('id', $id)->delete();
		if ($result) {
			return redirect()->back();
		}
	}

	public function imagetext($id)
	{
		$image_types = DB::table('image_type')->get();
		$rows = DB::table('master_product_images')->where('id', $id)->first();
		return view('cranium.supplierProdListing.imagetextupdate', ['row' => $rows, 'image_type' => $image_types]);
	}

	public function imagetext_update(Request $request, $id)
	{
		$input = $request->all();
		$data = [
			'image_text' => $input['image_text'],
			'image_type' => $input['image_type'],
		];
		$pro = DB::table('master_product_images')->where('ETIN', $request->ETIN)->where('image_type', $request->image_type)->where('id', '!=', $id)->first();
		if (!$pro) {
			$result = DB::table('master_product_images')->where('id', $id)->update($data);

			if ($result) {
				$data_info = [
					'msg' => 'Success',
					'error' => 0
				];
			} else {
				$data_info = [
					'msg' => 'Something wend wrong',
					'error' => 1
				];
			}
		} else {
			$data_info = [
				'msg' => 'Image Type is already in use',
				'error' => 1
			];
		}

		return response()->json($data_info);
	}

	public function approveNewProducts(Request $request)
	{
		$result = DB::table('master_product')->whereIn('id', $request->checked)->update(['is_approve' => 1, 'approved_date' => date('Y-m-d H:i:s')
	]);
		$this->masterProduct->insertProcessLog('MassApproveNewProducts', 'Products Approved.');
		foreach ($request->checked as $key => $value) {
			$this->masterProduct->MakeProductHistory([
				'response' => Auth::user()->name . ' approved product',
				'master_product_id' => $value,
				'action' => 'Approved'
			]);
		}

		if ($result) {
			$etins = DB::table('master_product')->whereIn('id', $request->checked)->pluck('ETIN')->toArray();
			$stingEtin = implode(',',$etins);
			UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Approved Product',
                'details' => 'Approved Product'.$stingEtin,
                'type' => 'CWMS'
            ]);
			$data = [
				'error' => false,
				'msg' => count($request->checked) . " Product Approved"
			];
		} else {
			$data = [
				'error' => true,
				'msg' => "Something Went Wrong"
			];
		}

		return response()->json($data);
	}

	public function approveEditProducts(Request $request)
	{
		foreach ($request->checked as $id) {
			$queue_product = DB::table('master_product_queue')->where('id', $id)->first();
			$master_product_id = $queue_product->master_product_id;
			$oldrecord = DB::table('master_product')->where('id', $queue_product->master_product_id)->first();
			$proimage = [];

			$oldrecordarray = (array)$oldrecord;
			$oldrecordarray['id'] = null;
			$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
			$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Old Record Inserted In Product History.');

			$updatemaster = [];
			if ($queue_product) {
				$excluded_keys = ['id', 'created_at', 'updated_at', 'product_edit_request', 'is_approve', 'approved_date', 'master_product_id', 'queue_status'];

				$updatemaster['ETIN'] = $queue_product->ETIN;
				$updatemaster['parent_ETIN'] = $queue_product->parent_ETIN;
				$updatemaster['full_product_desc'] = $queue_product->full_product_desc;
				$updatemaster['about_this_item'] = $queue_product->about_this_item;
				$updatemaster['manufacturer'] = $queue_product->manufacturer;
				$updatemaster['brand'] = $queue_product->brand;
				$updatemaster['flavor'] = $queue_product->flavor;
				$updatemaster['product_type'] = $queue_product->product_type;
				$updatemaster['unit_size'] = $queue_product->unit_size;
				$updatemaster['unit_description'] = $queue_product->unit_description;
				$updatemaster['pack_form_count'] = $queue_product->pack_form_count;
				$updatemaster['item_form_description'] = $queue_product->item_form_description;
				$updatemaster['total_ounces'] = $queue_product->total_ounces;
				$updatemaster['product_category'] = isset($queue_product->product_category) ? $queue_product->product_category : '';
				$updatemaster['product_subcategory1'] =  isset($queue_product->product_subcategory1) ? $queue_product->product_subcategory1 : '';
				$updatemaster['product_subcategory2'] =  isset($queue_product->product_subcategory2) ? $queue_product->product_subcategory2 : '';
				$updatemaster['product_subcategory3'] =  isset($queue_product->product_subcategory3) ? $queue_product->product_subcategory3 : '';
				$updatemaster['product_subcategory4'] =  isset($queue_product->product_subcategory4) ? $queue_product->product_subcategory4 : '';
				$updatemaster['product_subcategory5'] =  isset($queue_product->product_subcategory5) ? $queue_product->product_subcategory5 : '';
				$updatemaster['product_subcategory6'] =  isset($queue_product->product_subcategory6) ? $queue_product->product_subcategory6 : '';
				$updatemaster['product_subcategory7'] =  isset($queue_product->product_subcategory7) ? $queue_product->product_subcategory7 : '';
				$updatemaster['product_subcategory8'] =  isset($queue_product->product_subcategory8) ? $queue_product->product_subcategory8 : '';
				$updatemaster['product_subcategory9'] =  isset($queue_product->product_subcategory9) ? $queue_product->product_subcategory9 : '';
				$updatemaster['key_product_attributes_diet'] = $queue_product->key_product_attributes_diet;
				$updatemaster['product_tags'] = $queue_product->product_tags;
				$updatemaster['MFG_shelf_life'] = $queue_product->MFG_shelf_life;
				$updatemaster['hazardous_materials'] = $queue_product->hazardous_materials;
				$updatemaster['storage'] = $queue_product->storage;
				$updatemaster['ingredients'] = $queue_product->ingredients;
				$updatemaster['allergens'] = $queue_product->allergens;
				$updatemaster['prop_65_flag'] = $queue_product->prop_65_flag;
				$updatemaster['prop_65_ingredient'] = $queue_product->prop_65_ingredient;
				$updatemaster['product_temperature'] = $queue_product->product_temperature;
				$updatemaster['supplier_status'] = $queue_product->supplier_status;
				$updatemaster['upc'] = $queue_product->upc;
				$updatemaster['gtin'] = $queue_product->gtin;
				$updatemaster['asin'] = $queue_product->asin;
				$updatemaster['upc_scanable'] =  isset($queue_product->upc_scanable) ? 1 : 0;
				$updatemaster['gtin_scanable'] =  isset($queue_product->gtin_scanable) ? 1 : 0;
				$updatemaster['unit_upc_scanable'] =  isset($queue_product->unit_upc_scanable) ? 1 : 0;
				$updatemaster['unit_gtin_scanable'] =  isset($queue_product->unit_gtin_scanable) ? 1 : 0;
				$updatemaster['GPC_code'] = $queue_product->GPC_code;
				$updatemaster['GPC_class'] = $queue_product->GPC_class;
				$updatemaster['HS_code'] = $queue_product->HS_code;
				$updatemaster['weight'] = $queue_product->weight;
				$updatemaster['length'] = $queue_product->length;
				$updatemaster['width'] = $queue_product->width;
				$updatemaster['height'] = $queue_product->height;
				$updatemaster['country_of_origin'] = $queue_product->country_of_origin;
				$updatemaster['package_information'] = $queue_product->package_information;
				$updatemaster['cost'] = $queue_product->cost;
				$updatemaster['acquisition_cost'] = $queue_product->acquisition_cost;
				$updatemaster['new_cost'] = $queue_product->new_cost;
				$updatemaster['new_cost_date'] = $queue_product->new_cost_date;
				$updatemaster['status'] = $queue_product->status;
				$updatemaster['etailer_availability'] = $queue_product->etailer_availability;
				$updatemaster['dropship_available'] = $queue_product->dropship_available;
				$updatemaster['channel_listing_restrictions'] = $queue_product->channel_listing_restrictions;
				$updatemaster['POG_flag'] = $queue_product->POG_flag;
				$updatemaster['consignment'] = $queue_product->consignment;
				$updatemaster['warehouses_assigned'] = $queue_product->warehouses_assigned;
				$updatemaster['status_date'] = $queue_product->status_date;
				$updatemaster['lobs'] = $queue_product->lobs;
				$updatemaster['chanel_ids'] = $queue_product->chanel_ids;
				$updatemaster['client_supplier_id'] = $queue_product->client_supplier_id;
				$updatemaster['alternate_ETINs'] = $queue_product->alternate_ETINs;
				$updatemaster['product_listing_ETIN'] = $queue_product->product_listing_ETIN;

				$updatemaster['unit_in_pack'] = $queue_product->unit_in_pack;
				$updatemaster['manufacture_product_number'] = $queue_product->manufacture_product_number;
				$updatemaster['supplier_product_number'] = $queue_product->supplier_product_number;
				$updatemaster['total_ounces'] = $queue_product->total_ounces;
				$updatemaster['is_edit'] = 1;
				$updatemaster['is_approve'] = $queue_product->is_approve;
				$updatemaster['approved_date'] = $queue_product->approved_date;
				$updatemaster['product_listing_name'] = $queue_product->product_listing_name;
				$updatemaster['supplier_type'] = $queue_product->supplier_type;


				$updatemaster['product_edit_request'] = NULL;

				// $updatemaster['updated_by'] = Auth::user()->id;
				$CMP = DB::table('master_product')->where('id', $queue_product->master_product_id)->first();

				if ($CMP) {
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					DB::table('master_product')->where('id', $queue_product->master_product_id)->update($updatemaster);
					$this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name . ' approved changes',
						'master_product_id' => $queue_product->master_product_id,
						'action' => 'Approved'
					]);
					$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Master Product Updated.');
				} else {
					$updatemaster['created_at'] = date('Y-m-d H:i:s');
					$updatemaster['updated_at'] = date('Y-m-d H:i:s');
					$updatemaster['inserted_by'] = Auth::user()->id;
					$master_product_id = DB::table('master_product')->insertGetId($updatemaster);
					$this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name . ' created Product: ' . $updatemaster['ETIN'],
						'master_product_id' => $master_product_id,
						'action' => 'Add'
					]);
					$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Master Product Inserted.');
				}
			}

			$check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id', $id)->first();

			$update_suppliment = [];
			if ($check_supplemental_mpt_data_queue) {
				$excluded_keys = ['id', 'created_at', 'updated_at'];
				foreach ($check_supplemental_mpt_data_queue as $key => $value) {
					if (!in_array($key, $excluded_keys)) {
						$update_suppliment[$key] = $value;
					}
				}
				$update_suppliment['master_product_id'] = $master_product_id;
				$check_supplemental_mpt_data = DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->first();
				if (!$check_supplemental_mpt_data) {
					DB::table('supplemental_mpt_data')->insert($update_suppliment);
					$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Supplemental MPT Data Inserted.');
				} else {
					DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->update($update_suppliment);
					$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Supplemental MPT Data Updated.');
				}
			}

			$ETIN = $queue_product->ETIN;

			DB::table('master_product_images')->where('ETIN', $ETIN)->update(['ETIN' => $updatemaster['ETIN']]);
			$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Product Images Updated.');

			DB::table('master_product_queue')->where('ETIN', $ETIN)->delete();
			$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Product Removed To Queue.');
			// DB::table('product_inventory_queue')->where('ETIN', $ETIN)->delete();
			// $this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Product Inventory Removed To Queue.');
			DB::table('product_images_queue')->where('ETIN', $ETIN)->delete();
			$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Product Images Removed To Queue.');
			DB::table('supplemental_mpt_data_queue')->where('master_product_id', $id)->delete();
			$this->masterProduct->insertProcessLog('MassApproveEditProducts', 'Supplemental MPT Data Removed To Queue.');
		}
		$etins = DB::table('master_product')->whereIn('id', $request->checked)->pluck('ETIN')->toArray();
		$stingEtin = implode(',',$etins);
			UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Edit Product Approved',
                'details' => 'Edit Product Approved'.$stingEtin,
                'type' => 'CWMS'
            ]);
		$data_info = [
			'msg' => count($request->checked) . ' Product Approved',
			'error' => 0
		];


		return response()->json($data_info);
	}

	public function SlackDirectApproveProduct($id)
	{
		$is_approved = $this->masterProduct->SlackDirectApproveProduct($id);

		if ($is_approved['error'] == false) {
			$this->masterProduct->insertProcessLog('SlackDirectApproveProduct', 'Product Approved.');
			return redirect('/editmasterproduct/' . $is_approved['product_id'])->with(['approved' => $is_approved['msg']]);
		}
		if ($is_approved['error'] == true) {
			$this->masterProduct->insertProcessLog('SlackDirectApproveProduct', 'Product Not Approved.');
			return redirect('/allmasterproductlsts')->with(['not_approved' => $is_approved['msg']]);
		}
	}

	public function getProductHistory($id)
	{
		$data = DB::table('product_history')->leftJoin('users', function ($join) {
			$join->on('users.id', '=', 'product_history.updated_by');
		})->select('product_history.*', 'users.name as username')->where('product_history.master_product_id', $id)->orderBy('product_history.id', 'DESC')->get();


		return Datatables::of($data)
			->addColumn('description', function ($data) {
				$btn = '<table class="table">';
				$history_response = (array) json_decode($data->response);
				if (isset($history_response['response'])) {
					$btn .= $history_response['response'];
				} else {
					$i = 0;
					foreach ($history_response as $key => $row) {
						if ($i == 0) {
							$btn .= '<tr>';
						}
						if ($key == 'About this item Old' || $key == 'About this item New') {
							$exp = explode('#', $row);
							$btn .= '<td><b>' . $key . '</b>';
							foreach ($exp as $row_key) {
								if ($row_key != '') {
									$btn .= '<br>' . $row_key . '';
								}
							}
							$btn .= '</td>';
						} else {
							$btn .= '<td><b>' . $key . '</b><br>' . $row . '</td>';
						}


						if ($i == 1) {
							$btn .= '</tr>';
							$i = -1;
						}
						$i++;
					}
				}
				$btn .= '</table>';
				return $btn;
			})
			->addColumn('actionbtn', function ($data) {
				$btn = '';
				if ($data->action == 'approve') {
					$btn = '<a onClick="GetHistoryModel(\'' . route('product-history.view', $data->id) . '\')" href="#" class="btn btn-sm btn-primary btn-flat">View</a>';
				}
				return $btn;
			})
			->rawColumns(['actionbtn', 'description'])
			->make(true);
	}

	public function viewProductHistory($id)
	{
		$product_history = DB::table('product_history')->where('id', $id)->first();
		if ($product_history) {
			$history_response = (array) json_decode($product_history->response);
		}
		$history_response =  $history_response;

		return view('cranium.supplierProdListing.viewProductHistory', compact('history_response'));
	}


	public function ProductWizardAjax()
	{
		$masterProduct = new MasterProduct;
		$newetin = $masterProduct->getETIN();
		// $lastrec = DB::table('master_product')->latest('id')->first();
		// if($lastrec){
		// 	$lastid = $lastrec->id;
		// 	$lastetin = $lastrec->ETIN;

		// 	$etinarray = explode('-',$lastetin);

		// 	if (end($etinarray) != 9999){
		// 		$newetin = ++$lastetin;
		// 	} else {
		// 		$newetin = $etinarray[0].'-'.++$etinarray[1].'-0000';
		// 	}
		// }
		// else{
		// 	$newetin = 'ETFZ-1000-0000';
		// }

		// Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		//Getting all Manufacturer list
		$manufacturer = [];
		//  $getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name')->get();
		//  foreach ($getmanufacturer as $manufacturers){
		// 	 $manufacturer[] = $manufacturers->manufacturer_name;
		//  }

		//Getting all Category list
		$categories = DB::table('categories')->where('level', 0)->orderBy('name')->get();


		//Getting all Product Type list
		$getproducttype = DB::table('product_type')->get();
		foreach ($getproducttype as $producttypes) {
			$producttype[] = $producttypes->product_type;
		}

		//Getting all Unit Size list
		$getunitsizes = DB::table('unit_sizes')->get();
		foreach ($getunitsizes as $unitsizes) {
			$unitname[] = $unitsizes->unit;
			$unitabb[] = $unitsizes->abbreviation;
			$unitsize = array_combine($unitabb, $unitname);
		}

		//Getting all Unit Description list
		$getunitdesc = DB::table('unit_desc')->get();
		foreach ($getunitdesc as $unitdescs) {
			$unitdesc[] = $unitdescs->unit_description;
		}

		//Getting all Product Tags list
		$getproducttags = DB::table('product_tags')->orderBy('tag')->get();
		foreach ($getproducttags as $producttags) {
			$producttag[$producttags->id] = $producttags->tag;
		}

		//Getting all Product Temparaure list
		$getproducttemp = DB::table('product_temp')->orderBy('product_temperature')->get();
		foreach ($getproducttemp as $producttemps) {
			$producttemp[] = $producttemps->product_temperature;
		}

		//Getting all Suppliers list
		$getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name')->get();
		foreach ($getsuppliers as $suppliers) {
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine($supplier_id, $supplier_name);
		}

		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin')->get();
		foreach ($getcountries as $countries) {
			$country[$countries->id] = $countries->country_of_origin;
		}

		//Getting all Item From Description list
		$getitemsdescs = DB::table('item_from_description')->get();
		foreach ($getitemsdescs as $itemsdescs) {
			$itemsdesc[] = $itemsdescs->item_desc;
		}

		//Getting all Clients list
		$getclients = DB::table('clients')->get();
		foreach ($getclients as $clients) {
			$client[$clients->id] = $clients->company_name;
		}

		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->get();
		foreach ($getetailers as $etailerlist) {
			$etailers[$etailerlist->id] = $etailerlist->etailer_availability;
		}
		//Getting all Warehouse list
		$getwarehouses = DB::table('warehouses')->get();
		foreach ($getwarehouses as $warehouselist) {
			$warehouse[] = $warehouselist->warehouses;
		}

		//Getting all Supplier Status
		$supplier_status = SupplierStatus::all();

		//Getting all prop_ingredients list
		$prop_ingredients = [];
		$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients')->get();
		foreach ($getprop_ingredients as $productprops) {
			$prop_ingredients[$productprops->id] = $productprops->prop_ingredients;
		}

		//Getting all allergens list
		$allergens = [];
		$getallergens = DB::table('allergens')->orderBy('allergens')->get();
		foreach ($getallergens as $row_allergens) {
			$allergens[$row_allergens->id] = $row_allergens->allergens;
		}

		return view('cranium.parentProductWizardAjax', ['brand' => $brand, 'manufacturer' => $manufacturer, 'categories' => $categories, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'supplier' => $supplier, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'newetin' => $newetin, 'etailers' => $etailers, 'warehouse' => $warehouse, 'supplier_status' => $supplier_status, 'prop_ingredients' => $prop_ingredients, 'allergens' => $allergens]);
	}


	public function upload_client_product(Request $request)
	{
		$errorRows = 0;
		$successRows = 0;
		$skiprows = 0;

		$client_id = $request->client_id;
		$draf_option = NULL;
		if (isset($request->select_option) && $request->select_option == 'Upload & Edit') $draf_option = 'd';
		$csv_header = DB::table('csv_header')->where('client_id', $client_id)->get();
		$map_json_array = json_decode($csv_header[0]->{'map_data'});
		$file = $request->file('csv_file');
		$path = $file->getRealPath();
		$UploadHistory = new UploadHistory;
		$UploadHistory->client_id = $client_id;
		$UploadHistory->save();
		if ($request->select_option == 'Insert for Approval') {
			Excel::import(new ThreePLClientInsertImport($map_json_array, $client_id, $UploadHistory->id), request()->file('csv_file'));
		} else {
			Excel::import(new ThreePLClientImport($map_json_array, $client_id, $UploadHistory->id), request()->file('csv_file'));
		}


		$result = UploadHistory::find($UploadHistory->id);

		$errorRows = 0;
		$successRows = 0;
		if ($result) {
			if ($result->failed_products_count > 0) {
				$errorRows = $result->failed_products_count;
			}

			if ($result->total_products > 0) {
				$successRows = $result->total_products;
			}
		}
		$result->delete();
		if ($request->select_option == 'Insert for Approval') {
			$msg = $successRows . " Rows Inserted Sucessfully, " . $errorRows . " rows don't have required values like (Supplier Product Number, Brand, Unit Size, Category, Product Temperature, Supplier Product Number, UPC Case, Supplir Status, Cose, Unit Description)";
		} else {
			$msg = $successRows . " Rows Inserted Sucessfully, " . $errorRows . " rows don't have required values like (Supplier Product Number) OR Dublicate Supplier Product Numbers";
		}
		if ($errorRows > 0) {
			return response()->json([
				'error' => 1,
				'msg' => $msg
			]);
		}

		return response()->json([
			'error' => 0,
			'msg' => $successRows . " Rows Inserted and " . $skiprows . " Skipped"
		]);
	}

	function getClientChanels(Request $request){		
		$lobs = $request->lobs;
		$bl = $request->bl;
		$chanels = DB::table('client_channel_configurations')->select('id','channel')->whereIN('client_id',explode(',',$lobs))->get();
		$chanel_ids = $request->chanel_ids;
		$product_consignment_client = DB::table('clients')->whereIN('id',explode(',',$lobs))->where('product_consignment','Yes')->first();
		if($product_consignment_client){
			$chanels_2 = DB::table('client_channel_configurations')->leftJoin('clients',function($join){
				$join->on('clients.id','=','client_channel_configurations.client_id');
			})->select('client_channel_configurations.id','client_channel_configurations.channel')->where('company_name','etailer')->get();
			
			$chanels = $chanels->merge($chanels_2);
		}
		if (isset($bl) && $bl == 1) {		
			return view('cranium.supplierProdListing.getClientChanels_Bl',compact('chanels','chanel_ids'));	
		}
		return view('cranium.supplierProdListing.getClientChanels',compact('chanels','chanel_ids'));
	}
}

