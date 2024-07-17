<?php

namespace App\Http\Controllers;

use DB;
use App;
use PDF;
use App\PriceGroup;
use Auth;
use Excel;
use Schema;
use App\User;
use App\Brand;
use App\Client;
use DataTables;
use App\Supplier;
use App\ProductType;
use App\SmartFilter;

use Aws\S3\S3Client;
use App\MasterShelf;
use App\AisleMaster;
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
use Aws\S3\Exception\S3Exception;
use App\Imports\ThreePLClientImport;
use App\SupplementalMptDataQueue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use App\Exports\MasterProductExcelExport;
use App\Imports\ThreePLClientInsertImport;
use App\Http\Requests\MasterProductRequest;
use App\Http\Requests\MasterProductApproveRequest;
use Illuminate\Support\Facades\Notification;

use App\Notifications\ApproveRejectProductNotification;
use App\Services\PriceGroupService;
use Illuminate\Support\Facades\Log;
use App\Repositories\NotificationRepository;

class MasterProductChildController extends Controller
{
    public function __construct(MasterProduct $masterProduct, SmartFilter $SmartFilter, ProductListingFilter $ProductListingFilter, PriceGroupService $priceGroupService,NotificationRepository $NotificationRepository)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->masterProduct = $masterProduct;
		$this->SmartFilter = $SmartFilter;
		$this->ProductListingFilter = $ProductListingFilter;
		$this->priceGroupService = $priceGroupService;
		$this->NotificationRepository = $NotificationRepository;
	}

    public function addchildview(Request $request)
	{
		if (ReadWriteAccess('EditMasterProductAddChildProduct') == false) {
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
		$newetin = $this->masterProduct->getETIN();

		$productid = $request->id;
		$productdetails = DB::table('master_product')->find($productid);
		$productetin = $productdetails->ETIN;
		$productimg = DB::table('product_images')->where('ETIN', $productetin)->first();
		$productdetails->unit_num = '';
		$productdetails->unit_list = '';
		if ($productdetails->unit_size) {
			$unitarray = explode('-', $productdetails->unit_size);
			if (!empty($unitarray)) {
				$productdetails->unit_num = isset($unitarray[0]) ? $unitarray[0] : '';
				$productdetails->unit_list = isset($unitarray[1]) ? $unitarray[1] : '';
			}
		}

		$productdetails->product_tags_array = explode(',', $productdetails->product_tags);
		$productdetails->lobs_array = explode(',', $productdetails->lobs);
		$productdetails->allergens_array = explode(',', $productdetails->allergens);


		if (!$productimg) {
			$productimg['Image_URL1_Primary'] = url("/assets/images/no_img.png");
			$productimg = (object)$productimg;
		}
		$productinventory = DB::table('product_inventory')->where('ETIN', $productetin)->first();

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

		//Getting all Manufacturer list
		$getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name', 'ASC')->get();
		foreach ($getmanufacturer as $manufacturers) {
			$manufacturer[] = $manufacturers->manufacturer_name;
		}

		//Getting all Category list
		$categories = DB::table('categories')->where('parent_id', 0)->orderBy('name', 'ASC')->get();

		// Getting all Brand list
		$getbrand = DB::table('brand')->orderBy('brand', 'ASC')->groupBy('brand')->get();
		foreach ($getbrand as $brands) {
			$brand[] = $brands->brand;
		}

		// Getting all prop_65_ingredients list
		$prop_ingredients = [];
		$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients')->get();
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

		//Getting parent product unit dimensions
		$unit_dimensions = DB::table('supplemental_mpt_data')->where('master_product_id', $productid)->first();

		$image_type = DB::table('image_type')->get();
		$image_type_count = DB::table('image_type')->count();
		$product_status = DB::table('product_statuses')->get();

		return view('cranium.supplierProdListing.childproductadd', ['productdetails' => $productdetails, 'productimg' => $productimg, 'productinventory' => $productinventory, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'newetin' => $newetin, 'supplier' => $supplier, 'etailers' => $etailers, 'warehouse' => $warehouse, 'supplier_status' => $supplier_status, 'manufacturer' => $manufacturer, 'categories' => $categories, 'brand' => $brand, 'prop_ingredients' => @$prop_ingredients, 'allergens' => $allergens, 'unit_dimensions' => $unit_dimensions, 'image_type_count' => $image_type_count, 'image_types' => $image_type, 'product_status' => $product_status]);
	}

	public function insertchild(MasterProductRequest $request)
	{
		$insertchild = [];
		$id = $request->get('id');

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

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('InsertChild', 'Images Validated.');

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));
			if (count($explodearray) > 1) {
				$insertchild['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			}

			if ($request->get('product_temperature') == "Frozen") {
				$insertchild['ETIN'] = 'ETFZ-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$insertchild['ETIN'] = 'ETDS-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$insertchild['ETIN'] = 'ETRF-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$insertchild['ETIN'] = 'ETBV-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$insertchild['ETIN'] = 'ETDP-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$insertchild['ETIN'] = 'ETDF-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$insertchild['ETIN'] = 'ETTS-' . $etinmid . '-' . $insertchild['ETIN'];
			} else {
				$insertchild['ETIN'] = 'ETOT-' . $etinmid . '-' . $insertchild['ETIN'];
			}
		}
		$insertimgs['ETIN'] = $insertchild['ETIN'];

		//$insertchild['ETIN'] = $request->get('ETIN');
		$insertchild['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertchild['product_listing_name'] = $request->get('product_listing_name');
		$insertchild['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertchild['about_this_item'] = implode('#', $request->get('about_this_item'));
		$insertchild['manufacturer'] = $request->get('manufacturer');
		$insertchild['brand'] = $request->get('brand');
		$insertchild['flavor'] = $request->get('flavor');
		$insertchild['product_type'] = $request->get('product_type');
		$insertchild['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$insertchild['unit_description'] = $request->get('unit_description');
		$insertchild['pack_form_count'] = $request->get('pack_form_count');
		$insertchild['item_form_description'] = $request->get('item_form_description');
		$insertchild['total_ounces'] = $request->get('total_ounces');
		$insertchild['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$insertchild['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$insertchild['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$insertchild['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$insertchild['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$insertchild['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$insertchild['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$insertchild['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$insertchild['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$insertchild['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$insertchild['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		// $insertchild['product_tags'] = implode(',' , $request->get('product_tags'));
		$insertchild['product_tags'] = $request->get('product_tags');
		$insertchild['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$insertchild['hazardous_materials'] = $request->get('hazardous_materials');
		$insertchild['storage'] = ProperInput($request->get('storage'));
		$insertchild['ingredients'] = $request->get('ingredients');
		$insertchild['allergens'] = $request->get('allergens');
		$insertchild['prop_65_flag'] = $request->get('prop_65_flag');
		$insertchild['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertchild['product_temperature'] = $request->get('product_temperature');
		$insertchild['upc'] = isset($request->upc) ? ProperInput($request->get('upc')) : '';
		$insertchild['gtin'] = isset($request->gtin) ? ProperInput($request->get('gtin')) : '';
		$insertchild['asin'] = ProperInput($request->get('asin'));
		$insertchild['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertchild['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertchild['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertchild['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertchild['GPC_code'] = ProperInput($request->get('GPC_code'));
		$insertchild['GPC_class'] = ProperInput($request->get('GPC_class'));
		$insertchild['HS_code'] = ProperInput($request->get('HS_code'));
		$insertchild['weight'] = $request->get('weight');
		$insertchild['length'] = $request->get('length');
		$insertchild['width'] = $request->get('width');
		$insertchild['height'] = $request->get('height');
		$insertchild['country_of_origin'] = $request->get('country_of_origin');
		$insertchild['package_information'] = ProperInput($request->get('package_information'));
		$insertchild['cost'] = $request->get('cost');
		$insertchild['acquisition_cost'] = $request->get('acquisition_cost');
		$insertchild['new_cost'] = $request->get('new_cost');
		$insertchild['new_cost_date'] = $request->get('new_cost_date');
		$insertchild['status'] = $request->get('status');
		$insertchild['etailer_availability'] = $request->get('etailer_availability');
		$insertchild['dropship_available'] = $request->get('dropship_available');
		$insertchild['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$insertchild['POG_flag'] = $request->get('POG_flag');
		$insertchild['consignment'] = $request->get('consignment');
		$insertchild['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		$insertchild['status_date'] = $request->get('status_date');
		// $insertchild['lobs'] = implode(',' , $request->get('lobs'));
		$insertchild['lobs'] = $request->get('lobs');
		$insertchild['chanel_ids'] = $request->get('chanel_ids');
		$insertchild['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertchild['client_supplier_id'] = $request->get('current_supplier');
		$insertchild['alternate_ETINs'] = $request->get('alternate_ETINs');
		$insertchild['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$insertchild['unit_in_pack'] = $request->get('unit_in_pack');
		$insertchild['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertchild['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$insertchild['supplier_status'] = $request->get('supplier_status');
		$insertchild['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$insertchild['created_at'] = date('Y-m-d H:i:s');
		$insertchild['updated_at'] = date('Y-m-d H:i:s');
		$insertchild['inserted_by'] = Auth::user()->id;
		// $insertchild['updated_by'] = Auth::user()->id;
		if (moduleacess('auto_approval_for_edit')) {
			$insertmasterproduct['is_approve'] = 1;
			$insertmasterproduct['approved_date'] = date('Y-m-d H:i:s');
		}

		$insertchild['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

		// check UPC
		$existsupc = isset($request->upc) ? DB::table('master_product')->where('UPC', $request->get('upc'))->first() : false;
		$existsgtin = isset($request->gtin) ? DB::table('master_product')->where('GTIN', $request->get('gtin'))->first() : false;
		if (!$existsupc && !$existsgtin) {
			$affected = DB::table('master_product')->insertGetId($insertchild);
			UserLogs([
				'user_id' => Auth::user()->id,
				'action' => 'Click',
				'task' => 'Add Child Product',
				'details' => 'Item '.$insertchild['ETIN'].' child product created .',
				'type' => 'CWMS'
			]);
			$this->masterProduct->insertProcessLog('InsertChild', 'Child Product Inserted.');


			$this->masterProduct->insertProcessLog('InsertChild', 'Child Product History Inserted.');

			$this->masterProduct->sendApproveRejectNotificationForAdd($affected, $insertchild['ETIN'], $insertchild['product_listing_name']);
			$this->masterProduct->insertProcessLog('InsertChild', 'Child Product Added Notification Sent');
			$this->masterProduct->MakeProductHistory([
				'response' => Auth::user()->name . ' created Product: ' . $insertimgs['ETIN'],
				'master_product_id' => $affected,
				'action' => 'Add'
			]);

			// $checkinventory = DB::table('product_inventory')->where('ETIN', $insertchild['ETIN'])->first();
			// if (!$checkinventory) {
			// 	$updateinventory['created_at'] = date('Y-m-d H:i:s');
			// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
			// 	$inventoryeffect = DB::table('product_inventory')->insert($updateinventory);
			// 	$this->masterProduct->insertProcessLog('InsertChild', 'Child Product Inventory Inserted.');
			// } else {
			// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
			// 	$inventoryeffect = DB::table('product_inventory')->where('id', $id)->update($updateinventory);
			// 	$this->masterProduct->insertProcessLog('InsertChild', 'Child Product Inventory Updated.');
			// }

			$insert_image = $this->masterProduct->insertImageFzl($insertchild['ETIN'], $request->all());
			$this->masterProduct->insertProcessLog('InsertChild', 'Child Product Images Inserted.');

            /* Notify other admins */		
            if(auth()->user()){
                $user = auth()->user();
                $note = $insertchild['ETIN'] . " Child Product Added by ".$user->name;
                $url_id = '';

                $product = DB::table('master_product')->where('ETIN', $insertchild['ETIN'])->first();
                if($product){
                    $url_id = $product->id;
                }
                $url = '/editmasterproduct/'.$url_id.'/tab_comments';
                $type = "Child Master Product";

				$this->NotificationRepository->SendProductNotification([
					'subject' => $type,
					'body' => $note,
					'url' => $url,
					'user' => $user
				]);
                	
            }

			return response()->json([
				'error' => 0,
				'msg' => 'Child Product is added sucessfully..',
				'url' => url('/allmasterproductlsts')
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
	}

	public function saveAsDraftChild(Request $request)
	{
		$insertchild = [];
		$id = $request->get('id');

		$validate_images = $this->masterProduct->ValidateImages($request->all());
		if ($validate_images['error']) {
			return response()->json([
				'error' => 1,
				'msg' => $validate_images['msg']
			]);
		}
		$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Images Validated.');

		if ($request->get('product_temperature')) {

			$explodearray = explode('-', $request->get('ETIN'));
			if (count($explodearray) > 1) {
				$insertchild['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			}

			if ($request->get('product_temperature') == "Frozen") {
				$insertchild['ETIN'] = 'ETFZ-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Strong") {
				$insertchild['ETIN'] = 'ETDS-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Refrigerated") {
				$insertchild['ETIN'] = 'ETRF-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Beverages") {
				$insertchild['ETIN'] = 'ETBV-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Perishable") {
				$insertchild['ETIN'] = 'ETDP-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Dry-Fragile") {
				$insertchild['ETIN'] = 'ETDF-' . $etinmid . '-' . $insertchild['ETIN'];
			} else if ($request->get('product_temperature') == "Thaw & Serv") {
				$insertchild['ETIN'] = 'ETTS-' . $etinmid . '-' . $insertchild['ETIN'];
			} else {
				$insertchild['ETIN'] = 'ETOT-' . $etinmid . '-' . $insertchild['ETIN'];
			}
		}
		$insertimgs['ETIN'] = $insertchild['ETIN'];

		//$insertchild['ETIN'] = $request->get('ETIN');
		$insertchild['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertchild['product_listing_name'] = $request->get('product_listing_name');
		$insertchild['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertchild['about_this_item'] = implode('#', $request->get('about_this_item'));
		$insertchild['manufacturer'] = $request->get('manufacturer');
		$insertchild['brand'] = $request->get('brand');
		$insertchild['flavor'] = $request->get('flavor');
		$insertchild['product_type'] = $request->get('product_type');
		$insertchild['unit_size'] = $request->get('unit_num') . '-' . $request->get('unit_list');
		$insertchild['unit_description'] = $request->get('unit_description');
		$insertchild['pack_form_count'] = $request->get('pack_form_count');
		$insertchild['item_form_description'] = $request->get('item_form_description');
		$insertchild['total_ounces'] = $request->get('total_ounces');
		$insertchild['product_category'] = isset($request->product_category) ? $request->product_category : '';
		$insertchild['product_subcategory1'] =  isset($request->product_subcategory1) ? $request->product_subcategory1 : '';
		$insertchild['product_subcategory2'] =  isset($request->product_subcategory2) ? $request->product_subcategory2 : '';
		$insertchild['product_subcategory3'] =  isset($request->product_subcategory3) ? $request->product_subcategory3 : '';
		$insertchild['product_subcategory4'] =  isset($request->product_subcategory4) ? $request->product_subcategory4 : '';
		$insertchild['product_subcategory5'] =  isset($request->product_subcategory5) ? $request->product_subcategory5 : '';
		$insertchild['product_subcategory6'] =  isset($request->product_subcategory6) ? $request->product_subcategory6 : '';
		$insertchild['product_subcategory7'] =  isset($request->product_subcategory7) ? $request->product_subcategory7 : '';
		$insertchild['product_subcategory8'] =  isset($request->product_subcategory8) ? $request->product_subcategory8 : '';
		$insertchild['product_subcategory9'] =  isset($request->product_subcategory9) ? $request->product_subcategory9 : '';
		$insertchild['key_product_attributes_diet'] = ProperInput($request->get('key_product_attributes_diet'));
		// $insertchild['product_tags'] = implode(',' , $request->get('product_tags'));
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertchild['MFG_shelf_life'] = $request->get('MFG_shelf_life');
		$insertchild['hazardous_materials'] = $request->get('hazardous_materials');
		$insertchild['storage'] = ProperInput($request->get('storage'));
		$insertchild['ingredients'] = $request->get('ingredients');
		$insertchild['allergens'] = $request->get('allergens');
		$insertchild['prop_65_flag'] = $request->get('prop_65_flag');
		$insertchild['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertchild['product_temperature'] = $request->get('product_temperature');
		$insertchild['upc'] = ProperInput($request->get('upc'));
		$insertchild['gtin'] = ProperInput($request->get('gtin'));
		$insertchild['asin'] = ProperInput($request->get('asin'));
		$insertchild['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertchild['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertchild['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertchild['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertchild['GPC_code'] = ProperInput($request->get('GPC_code'));
		$insertchild['GPC_class'] = ProperInput($request->get('GPC_class'));
		$insertchild['HS_code'] = ProperInput($request->get('HS_code'));
		$insertchild['weight'] = $request->get('weight');
		$insertchild['length'] = $request->get('length');
		$insertchild['width'] = $request->get('width');
		$insertchild['height'] = $request->get('height');
		$insertchild['country_of_origin'] = $request->get('country_of_origin');
		$insertchild['package_information'] = ProperInput($request->get('package_information'));
		$insertchild['cost'] = $request->get('cost');
		$insertchild['acquisition_cost'] = $request->get('acquisition_cost');
		$insertchild['new_cost'] = $request->get('new_cost');
		$insertchild['new_cost_date'] = $request->get('new_cost_date');
		$insertchild['status'] = $request->get('status');
		$insertchild['etailer_availability'] = $request->get('etailer_availability');
		$insertchild['dropship_available'] = $request->get('dropship_available');
		$insertchild['channel_listing_restrictions'] = ProperInput($request->get('channel_listing_restrictions'));
		$insertchild['POG_flag'] = $request->get('POG_flag');
		$insertchild['consignment'] = $request->get('consignment');
		// $insertchild['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		if ($request->get('warehouses_assigned')) {
			$insertchild['warehouses_assigned'] = implode(',', $request->get('warehouses_assigned'));
		} else {
			$insertchild['warehouses_assigned'] = '';
		}
		$insertchild['status_date'] = $request->get('status_date');
		// $insertchild['lobs'] = implode(',' , $request->get('lobs'));
		$insertchild['lobs'] = $request->get('lobs');
		$insertchild['chanel_ids'] = $request->get('chanel_ids');
		$insertchild['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertchild['client_supplier_id'] = $request->get('current_supplier');
		$insertchild['alternate_ETINs'] = $request->get('alternate_ETINs');
		$insertchild['product_listing_ETIN'] = $request->get('product_listing_ETIN');

		$insertchild['unit_in_pack'] = $request->get('unit_in_pack');
		$insertchild['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertchild['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
		$insertchild['supplier_status'] = $request->get('supplier_status');
		$insertchild['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$insertchild['created_at'] = date('Y-m-d H:i:s');
		$insertchild['updated_at'] = date('Y-m-d H:i:s');
		$insertchild['queue_status'] = "d";
		// $insertchild['updated_by'] = Auth::user()->id;
		// $insertchild['inserted_by'] = Auth::user()->id;


		$insertchild['product_listing_name'] = $request->get('brand') . ' ' . $request->get('flavor') . ' ' . $request->get('product_type') . ', ' . $request->get('unit_num') . ' ' . $request->get('unit_list') . ' ' . $request->get('unit_description') . ' (' . $request->get('pack_form_count') . '-' . $request->get('unit_in_pack') . ' ' . $request->get('item_form_description') . ')';

		$existsEtin = DB::table('master_product_queue')->where('ETIN', $insertchild['ETIN'])->first();
		if (!$existsEtin) {
			$insertchild['inserted_by'] = Auth::user()->id;
			$affected = DB::table('master_product_queue')->insert($insertchild);
			UserLogs([
				'user_id' => Auth::user()->id,
				'action' => 'Click',
				'task' => 'Save As Draft Child Product',
				'details' => 'Item '.$insertchild['ETIN'].' child product save as draft.',
				'type' => 'CWMS'
			]);
			$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Child Product Inserted To Queue.');
		} else {
			$insertchild['updated_by'] = Auth::user()->id;
			$affected = DB::table('master_product_queue')->where('ETIN', $insertchild['ETIN'])->update($insertchild);
			UserLogs([
				'user_id' => Auth::user()->id,
				'action' => 'Click',
				'task' => 'Update As Draf Child Product',
				'details' => 'Item '.$insertchild['ETIN'].' child product update as draft .',
				'type' => 'CWMS'
			]);
			$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Child Product Updated In Queue.');
		}

		// $checkinventory = DB::table('product_inventory')->where('ETIN', $insertchild['ETIN'])->first();
		// if (!$checkinventory) {
		// 	$updateinventory['created_at'] = date('Y-m-d H:i:s');
		// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
		// 	$inventoryeffect = DB::table('product_inventory')->insert($updateinventory);
		// 	$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Child Product Inventory Inserted.');
		// } else {
		// 	$updateinventory['updated_at'] = date('Y-m-d H:i:s');
		// 	$inventoryeffect = DB::table('product_inventory')->where('id', $id)->update($updateinventory);
		// 	$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Child Product Inventory Updated.');
		// }

		$insert_image = $this->masterProduct->insertImageFzl($insertchild['ETIN'], $request->all());
		$this->masterProduct->insertProcessLog('SaveAsDraftChild', 'Child Product Images Inserted.');

        /* Notify other admins */		
        if(auth()->user()){
            $user = auth()->user();
            $note = $insertchild['ETIN'] . " Child Product Draft Added by ".$user->name;
            $url_id = '';

            $product = MasterProductQueue::where('ETIN', $insertchild['ETIN'])->first();
            if($product){
                $url_id = $product->id;
            }
            $url = '/editmasterrequestview/'.$url_id.'/tab_comments';
            $type = "Child Draft Product";

			$this->NotificationRepository->SendProductNotification([
				'subject' => $type,
				'body' => $note,
				'url' => $url,
				'user' => $user
			]);
            	
        }
		return response()->json([
			'error' => 0,
			'msg' => 'Child Product is added to Draft..',
			'url' => url('/allmasterproductlsts')
		]);
	}
}
