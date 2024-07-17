<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Schema;
use App\Brand;
use App\Client;
use DataTables;
use App\Category;
use App\Supplier;
use App\UnitSize;
use App\Allergens;
use App\WareHouse;
use App\Categories;
use App\ProductTags;
use App\ProductType;
use Aws\S3\S3Client;
use App\Manufacturer;
use App\ProductImage;
use App\MasterProduct;
use App\SupplierStatus;
use App\CountryOfOrigin;
use App\PropIngredients;
use App\UnitDescription;
use App\MasterProductQueue;
use App\ProductTemperature;

use App\ItemFormDescription;
use Illuminate\Http\Request;
use App\ETailer_availability;
use Aws\Exception\AwsException;
use App\RequestProductSelection;
use App\SupplementalMptDataQueue;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\DuplicateMasterProductRequest;
use App\AisleMaster;
use App\MasterShelf;
use App\User;
use App\Repositories\NotificationRepository;

class DuplicateMasterProductController extends Controller
{

	public function __construct(MasterProduct $masterProduct, NotificationRepository $NotificationRepository)
	{
		$this->masterProduct = $masterProduct;
		$this->NotificationRepository = $NotificationRepository;
	}

   	public function duplicateproduct(Request $request){
		if(ReadWriteAccess('EditMasterProductDuplicateProduct') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
		$newetinadd = new MasterProduct;
		$newetin = $newetinadd->getETIN();

		$productid = $request->id;
		$productdetails = DB::table('master_product')->find($productid);
		$productetin = $productdetails->ETIN;
		$productimg = DB::table('product_images')->where('ETIN', $productetin)->first();
		$productdetails->unit_num = NULL;
		$productdetails->unit_list = NULL;
		if($productdetails->unit_size){
			$unitarray = explode('-', $productdetails->unit_size);
			if(isset($unitarray[0])) $productdetails->unit_num = $unitarray[0];
			if(isset($unitarray[1])) $productdetails->unit_list = $unitarray[1];
		}

		$productdetails->product_category_name = DB::table('categories')->where('id', $productdetails->product_category)->value('name');
		$productdetails->product_subcategory1_name = DB::table('product_subcategory')->where('sub_category_1', $productdetails->product_subcategory1)->value('sub_category_1');
		$productdetails->product_subcategory2_name = DB::table('product_subcategory')->where('sub_category_2', $productdetails->product_subcategory2)->value('sub_category_2');
		$productdetails->product_subcategory3_name = DB::table('product_subcategory')->where('sub_category_3', $productdetails->product_subcategory3)->value('sub_category_3');

		if( ! $productimg){
			$productimg['Image_URL1_Primary'] = url("/assets/images/no_img.png");
			$productimg = (object)$productimg;
		}
		$productinventory = DB::table('product_inventory')->where('ETIN', $productetin)->first();

		//suplimental_data
		$suplimental_data = DB::table('supplemental_mpt_data')->where('master_product_id',$productid)->first();

		//Getting all Product Type list
			 $producttypenew = new ProductType;
			 $producttype = $producttypenew->productTypeList();

		//Getting all Unit Size list
			 $unitsizenew = new UnitSize();
			 $unitsize = $unitsizenew->UnitSizeList();

		//Getting all Unit Description list
			$unitdescnew = new UnitDescription();
			$unitdesc = $unitdescnew->UnitDescriptionList();

		 //Getting all Product Tags list
		 $getproducttags = DB::table('product_tags')->orderBy('tag')->get();
		 foreach ($getproducttags as $producttags){
			 $producttag[$producttags->id] = $producttags->tag;
		 }

		//Getting all Product Temparaure list
			$producttempnew = new ProductTemperature();
			$producttemp = $producttempnew->ProductTemperatureList();

		//Getting all Suppliers list
			$suppliernew = new Supplier();
			$supplier = $suppliernew->SupplierList();

		//Getting all Country Of Origin list
		$getcountries = DB::table('country_of_origin')->orderBy('country_of_origin')->get();
		foreach ($getcountries as $countries){
			$country[$countries->id] = $countries->country_of_origin;
		}

		//Getting all Item From Description list
		$itemsdescnew = new ItemFormDescription();
		$itemsdesc = $itemsdescnew->itemFormDescList();

		//Getting all Clients list
		$clientnew = new Client();
		$client = $clientnew->ClientList();

		//Getting all Clients list
		$getetailers = DB::table('etailer_availability')->get();
		foreach ($getetailers as $etailerlist){
			$etailers[$etailerlist->id] = $etailerlist->etailer_availability;
		}

		//Getting all Warehouse list
		$getwarehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();
		
		$onHandQty = [];
		foreach ($getwarehouses as $warehouselist) {
			$AisleMaster = AisleMaster::where('warehouse_id',$warehouselist->id)->pluck('id')->toArray();
        	$masterShelfSum = MasterShelf::where('ETIN',$productdetails->ETIN )->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
			$onHandQty[$warehouselist->id] =  $masterShelfSum;
			$warehouse[$warehouselist->id] = $warehouselist->warehouses;
		}

		//Getting all Manufacturer list
			$manufacturernew =new Manufacturer();
			$manufacturer = $manufacturernew->manufacturerList();

		//Getting all Category list
			$categorynew = new Categories();
			$category = DB::table('categories')->where('level',0)->orderBy('name','ASC')->get()->pluck('name','id')->toArray(); ///$categorynew->GetCategoryList();

		// Getting all Brand list
			$brandnew = new Brand();
			$brand = $brandnew->brandList();

		// Getting all prop_65_ingredients list
			// $prop_ingredientsnew = new PropIngredients();
			// $prop_ingredients = $prop_ingredientsnew->PropIngredientsList();
			$prop_ingredients = [];
			$getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients')->get();
			foreach ($getprop_ingredients as $productprops){
				$prop_ingredients[$productprops->id] = $productprops->prop_ingredients;
			}

			//Getting all allergens list
			$allergens = [];
			$getallergens = DB::table('allergens')->orderBy('allergens')->get();
			foreach ($getallergens as $row_allergens){
				$allergens[$row_allergens->id] = $row_allergens->allergens;
			}

		 //Getting all Supplier Status
		 $supplier_status = SupplierStatus::all();

		 //Getting parent product unit dimensions
		 $unit_dimensions = DB::table('supplemental_mpt_data')->where('ETIN',$productetin)->first();
		 $product_status = DB::table('product_statuses')->get();
		//  dd($unit_dimensions->weight);

		$image_type = DB::table('image_type')->get();

		return view('cranium.supplierProdListing.duplicateproductadd', ['productdetails' =>$productdetails, 'productimg' => $productimg, 'productinventory' => $productinventory, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'newetin' => $newetin, 'supplier' => $supplier, 'etailers' => $etailers, 'warehouse' => $warehouse, 'supplier_status' => $supplier_status, 'manufacturer' => $manufacturer, 'category' => $category, 'brand' => $brand, 'prop_ingredients' => @$prop_ingredients, 'allergens' => $allergens,'unit_dimensions'=>$unit_dimensions, 'suplimental_data'=>$suplimental_data,'image_types' => $image_type,'product_status' => $product_status]);
	}

	public function insertduplicateproduct(DuplicateMasterProductRequest $request){
		$insertchild = [];
		$id=$request->get('id');

		$masterProduct = new MasterProduct();
		$validate_images = $masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }
		$this->masterProduct->insertProcessLog('insertduplicateproduct','Image Validated');
		if($request->get('product_temperature')){

			$explodearray = explode('-', $request->get('ETIN'));
			if (count($explodearray) > 1){
				$insertchild['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
				}

				if($request->get('product_temperature') == "Frozen"){
					$insertchild['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Dry-Strong"){
					$insertchild['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Refrigerated"){
					$insertchild['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Beverages"){
					$insertchild['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Dry-Perishable"){
					$insertchild['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Dry-Fragile"){
					$insertchild['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertchild['ETIN'];
				} else if($request->get('product_temperature') == "Thaw & Serv"){
					$insertchild['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertchild['ETIN'];
				} else {$insertchild['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertchild['ETIN'];
			}

		}
		$insertimgs['ETIN'] = $insertchild['ETIN'];

		//$insertchild['ETIN'] = $request->get('ETIN');
		$insertchild['parent_ETIN'] = $request->get('parent_ETIN');
		//$insertchild['product_listing_name'] = $request->get('product_listing_name');
		$insertchild['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertchild['about_this_item'] = implode('#',$request->get('about_this_item'));
		$insertchild['manufacturer'] = $request->get('manufacturer');
		$insertchild['brand'] = $request->get('brand');
		$insertchild['flavor'] = ProperInput($request->get('flavor'));
		$insertchild['product_type'] = $request->get('product_type');
		$insertchild['unit_size'] = $request->get('unit_num'). '-'.$request->get('unit_list');
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
		$insertchild['storage'] = $request->get('storage');
		$insertchild['ingredients'] = $request->get('ingredients');
		$insertchild['allergens'] = $request->get('allergens');
		$insertchild['prop_65_flag'] = $request->get('prop_65_flag');
		$insertchild['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertchild['product_temperature'] = $request->get('product_temperature');
		$insertchild['upc'] = $request->get('upc');
		$insertchild['gtin'] = $request->get('gtin');
		$insertchild['asin'] = $request->get('asin');
		$insertchild['GPC_code'] = $request->get('GPC_code');
		$insertchild['GPC_class'] = $request->get('GPC_class');
		$insertchild['HS_code'] = $request->get('HS_code');
		$insertchild['weight'] = $request->get('weight');
		$insertchild['length'] = $request->get('length');
		$insertchild['width'] = $request->get('width');
		$insertchild['height'] = $request->get('height');
		$insertchild['country_of_origin'] = $request->get('country_of_origin');
		$insertchild['package_information'] = $request->get('package_information');
		$insertchild['cost'] = $request->get('cost');
        $insertchild['acquisition_cost'] = $request->get('acquisition_cost');
		$insertchild['new_cost'] = $request->get('new_cost');
		$insertchild['new_cost_date'] = $request->get('new_cost_date');
		$insertchild['status'] = $request->get('status');
		$insertchild['etailer_availability'] = $request->get('etailer_availability');
		$insertchild['dropship_available'] = $request->get('dropship_available');
		$insertchild['channel_listing_restrictions'] = $request->get('channel_listing_restrictions');
		$insertchild['POG_flag'] = $request->get('POG_flag');
		$insertchild['consignment'] = $request->get('consignment');
		$insertchild['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$insertchild['status_date'] = $request->get('status_date');
		$insertchild['lobs'] = $request->get('lobs');
		$insertchild['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertchild['chanel_ids'] = $request->get('chanel_ids');
		$insertchild['supplier_type'] = $request->get('sup_type') === 'type_supplier' ? 'supplier' : 'client';
		$insertchild['client_supplier_id'] = $request->get('current_supplier');
		$insertchild['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
		$insertchild['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
		$insertchild['supplier_description'] = $request->get('supplier_description');

		$insertchild['unit_in_pack'] = $request->get('unit_in_pack');
		$insertchild['supplier_product_number'] = $request->get('supplier_product_number');
		$insertchild['manufacture_product_number'] = $request->get('manufacture_product_number');
		$insertchild['supplier_status'] = $request->get('supplier_status');
		$insertchild['total_ounces'] = $request->get('unit_num') * $request->get('pack_form_count');
		$insertchild['created_at'] = date('Y-m-d H:i:s');
		$insertchild['updated_at'] = date('Y-m-d H:i:s');
		$insertchild['updated_by'] = Auth::user()->id;
		$insertchild['inserted_by'] = Auth::user()->id;
		if(moduleacess('auto_approval_for_edit')){
			$insertchild['is_approve'] = 1;
			$insertchild['approved_date'] = date('Y-m-d H:i:s');
		}

		$insertchild['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ', ' .$request->get('unit_num'). ' '.$request->get('unit_list'). ' ' .$request->get('unit_description'). ' (' .$request->get('pack_form_count'). '-' . $request->get('unit_in_pack'). ' ' . $request->get('item_form_description') .')';

		// check UPC

		$existsupc = isset($request->upc) ? DB::table('master_product')->where('UPC',$request->get('upc'))->first() : false;
		$existsgtin = isset($request->gtin) ? DB::table('master_product')->where('GTIN',$request->get('gtin'))->first() : false;
		if(!$existsupc && !$existsgtin){
			$affected = DB::table('master_product')->insertGetId($insertchild);
			UserLogs([
				'user_id' => Auth::user()->id,
				'action' => 'Click',
				'task' => 'Duplicate Product Add',
				'details' => 'Item '.$insertchild['ETIN'].' duplicate product created .',
				'type' => 'CWMS'
			]);
			$this->masterProduct->MakeProductHistory([
				'response' => Auth::user()->name.' created Product: '.$insertchild['ETIN'],
				'master_product_id' => $affected,
				'action' => 'Add'
			]);
			$this->masterProduct->insertProcessLog('insertduplicateproduct','Store InsertGetId');
			$MasterProduct = new MasterProduct;
			$MasterProduct->insertProductHistory($affected,"add");
			$this->masterProduct->insertProcessLog('insertduplicateproduct','Duplicate Product History');
			$masterProduct->sendApproveRejectNotificationForAdd($affected, $insertchild['ETIN'], $insertchild['product_listing_name']);

			// $checkinventory = DB::table('product_inventory')->where('ETIN',$insertchild['ETIN'])->first();
			// 	if(!$checkinventory){
			// 		$updateinventory['created_at'] = date('Y-m-d H:i:s');
			// 		$updateinventory['updated_at'] = date('Y-m-d H:i:s');
			// 		$inventoryeffect = DB::table('product_inventory')->insert($updateinventory);
			// 		$this->masterProduct->insertProcessLog('insertduplicateproduct','Update Inventory InsertGetId');
			// 	} else {
			// 		$updateinventory['updated_at'] = date('Y-m-d H:i:s');
			// 		$inventoryeffect = DB::table('product_inventory')->where('id', $id)->update($updateinventory);
			// 		$this->masterProduct->insertProcessLog('insertduplicateproduct','Update Inventory');
			// 	}

				//$insert_image = $masterProduct->insertImageFzl($insertchild['ETIN'],$request->all());

				/* Notify other admins */		
				if(auth()->user()){
					$user = auth()->user();
					$note = $insertchild['ETIN'] . " Duplicate Product Added by ".$user->name;
					$url_id = '';
		
					$product = MasterProduct::where('ETIN', $insertchild['ETIN'])->first();
					if($product){
						$url_id = $product->id;
					}
					$url = '/editmasterproduct/'.$url_id.'/tab_comments';
					$type = "Duplicate Product";

					$this->NotificationRepository->SendProductNotification([
						'subject' => $type,
						'body' => $note,
						'url' => $url,
						'user' => $user
					]);
				}
				return response()->json([
					'error' => 0,
					'msg' => 'Duplicate Product is added sucessfully..',
					'url' => url('/allmasterproductlsts')
				]);
		} else {

				if($existsupc && $existsgtin){
					$upcetin = $existsupc->ETIN;
					$gtinetin = $existsgtin->ETIN;
					if ($upcetin == $gtinetin){

						$msg = "UPC ".$request->get('upc')." and GTIN ".$request->get('gtin')." already exists for ETIN # ".$upcetin." and cannot be added.";
					} else {

						$msg = "UPC ".$request->get('upc')." is already exists for ETIN # ".$upcetin." , GTIN ".$request->get('gtin')." is exists for ETIN # ".$gtinetin." and cannot be added.";
						}
					} else {
						if($existsupc){
							$upcetin = $existsupc->ETIN;

							$msg = "UPC ".$request->get('upc')." already exists for ETIN # ".$upcetin." and cannot be added.";
						}
							else if($existsgtin){

							$gtinetin = $existsgtin->ETIN;
							$msg = "GTIN ".$request->get('gtin')." already exists for ETIN # ".$gtinetin." and cannot be added.";
						}
					}

				return response()->json([
					'error' => 1,
					'msg' => $msg
				]);
			}
	}

	public function checkupdatefields(Request $request){
		$data = null;
		$reqdata = $request->all();


		$etin = $request->ETIN;
		$masterproduct = MasterProduct::where('ETIN','=',$reqdata['ETIN'])->first();

		if($masterproduct['unit_size'] == $reqdata['unit_size']){
			$data = 'Unit Size,';
		}
		if($masterproduct['unit_description'] == $reqdata['unit_description']){
			$data = $data.' Unit Description, ';
		}
		if($masterproduct['pack_form_count'] == $reqdata['pack_form_count']){
			$data = $data.' Pack Form Count, ';
		}
		if($masterproduct['unit_in_pack'] == $reqdata['unit_in_pack']){
			$data = $data.' Units In Pack, ';
		}
		if($masterproduct['item_form_description'] == $reqdata['item_form_description']){
			$data = $data.' Item Form Description, ';
		}
		if($masterproduct['key_product_attributes_diet'] == $reqdata['key_product_attributes_diet']){
			$data = $data.' Key Product Attribute, ';
		}
		if($masterproduct['allergens'] == $reqdata['allergens']){
			$data = $data.' Allergens, ';
		}
		if($masterproduct['product_tags'] == $reqdata['product_tags']){
			$data = $data.' Product Tags';
		}

		 return $data;
	}
}
