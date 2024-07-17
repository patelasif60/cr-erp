<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;

use App\AisleMaster;
use App\MasterShelf;
use App\MasterProduct;
use App\SupplierStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\MasterProductKitComponents;
use App\Http\Requests\KitProductsRequest;

class MasterKitProductsController extends Controller
{
    public function __construct(MasterProduct $masterProduct)
	{
		ini_set('max_execution_time', 999999);
		ini_set('memory_limit', '1024M');
		$this->masterProduct = $masterProduct;
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $newetin = $this->masterProduct->getETIN();
		$req = $request->all();
        //Getting all Product Type list
        $producttype = [];
        $getproducttype = DB::table('kit_description')->orderBy('kit_description','ASC')->get();
        foreach ($getproducttype as $producttypes){
            $producttype[] = $producttypes->kit_description;
        }

        //Getting all Product Temparaure list
        $getproducttemp = DB::table('product_temp')->orderBy('product_temperature','ASC')->get();
        foreach ($getproducttemp as $producttemps){
            $producttemp[] = $producttemps->product_temperature;
        }

        //Getting all Clients list
        $getetailers = DB::table('etailer_availability')->orderBy('etailer_availability','ASC')->get();
        foreach ($getetailers as $etailerlist){
            $etailers[$etailerlist->id] = $etailerlist->etailer_availability;
        }

        //Getting all Warehouse list
        $getwarehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        foreach ($getwarehouses as $warehouselist){
            $warehouse[] = $warehouselist->warehouses;
        }

        //Getting all Category list
        $categories = DB::table('categories')->where('level',0)->orderBy('name','ASC')->get();


         //Getting all Product Tags list
		 $getproducttags = DB::table('product_tags')->orderBy('tag','ASC')->get();
		 foreach ($getproducttags as $producttags){
            $producttag[$producttags->id] = $producttags->tag;
		 }

         //Getting all Clients list
         $getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();
         $client = $getclients->pluck('company_name','id')->toArray();
		//  $getclients = DB::table('clients')->orderBy('company_name','ASC')->get();
		//  foreach ($getclients as $clients){
        //     $client[$clients->id] = $clients->company_name;
		//  }

         //Getting all prop_ingredients list
		 $prop_ingredients = [];
		 $getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients','ASC')->get();
		 foreach ($getprop_ingredients as $productprops){
			 $prop_ingredients[] = $productprops->prop_ingredients;
		 }

		 //Getting all allergens list
		 $allergens = [];
		 $getallergens = DB::table('allergens')->orderBy('allergens','ASC')->get();
		 foreach ($getallergens as $row_allergens){
			 $allergens[] = $row_allergens->allergens;
		 }

         //Getting all Brand list
		$brand = [];
		$getbrand = DB::table('brand')->groupBy('brand')->get();
		 foreach ($getbrand as $brands){
			 $brand[] = $brands->brand;
		}
        //Get ETIN
		$getet = [];
		$upcs = [];
        $brand = [];
        $product_listing_name = [];
		// $getupcs =  DB::table('master_product')->where('is_approve', 1)->where(function($q){
        //     $q->whereNull('parent_ETIN');
        //     $q->orWhere('parent_ETIN','=','');
        // })->where('item_form_description','!=','Kit')->select('master_product.upc','master_product.ETIN','master_product.brand','master_product.product_listing_name')->get();
		// foreach ($getupcs as $getupc){
		// 	$upcs[] = $getupc->upc;
		// 	$getet[] = $getupc->ETIN;
        //     $brand[] = $getupc->brand;
        //     $product_listing_name[] = $getupc->product_listing_name;
		// }
        $product_status = DB::table('product_statuses')->get();

        $brand = array_unique($brand);
        $image_type = DB::table('image_type')->get();
		return view('cranium.kitproducts.create', ['producttemp' => $producttemp, 'newetin' => $newetin, 'etailers' => $etailers, 'warehouse' => $warehouse, 'producttype' => $producttype,'image_types' => $image_type, 'categories' => $categories, 'producttag' => $producttag, 'client' => $client, 'prop_ingredients' => $prop_ingredients, 'allergens' => $allergens, 'brand' => $brand, 'getet' => $getet, 'upcs' => $upcs, 'product_listing_name' => $product_listing_name,'req' => $req,'product_status' => $product_status]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KitProductsRequest $request)
    {
        $validate_images = $this->masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }

        $total_kit_components = 0;
        if(isset($request->kit_components) && !empty($request->kit_components)){
            $total_kit_components = count($request->kit_components);
        }
        if($total_kit_components <= 1){
            return response()->json([
                'error' => 1,
                'msg' => "Kits must have more than 1 item",
            ]);
        }

        $this->masterProduct->insertProcessLog('Master Kit Product Store','Image Validated');
        $insertmasterproduct = [];

		$insertmpt = [];

        if($request->get('product_temperature')){
			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1){
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if($request->get('product_temperature') == "Frozen"){
				$insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Strong"){
				$insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Refrigerated"){
				$insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Beverages"){
				$insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Perishable"){
				$insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Fragile"){
				$insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Thaw & Serv"){
				$insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			}

		}

		//Etin ready for ProductImage table

		$id = $request->get('id');
		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#' , $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
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
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = $request->get('storage');
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;

		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = $request->get('package_information');
		$insertmasterproduct['cost'] = $request->get('cost');
        $insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
		$insertmasterproduct['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['is_approve'] = 1;
        $insertmasterproduct['approved_date'] = date('Y-m-d H:i:s');
        $insertmasterproduct['updated_by'] = Auth::user()->id;
		$insertmasterproduct['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ' (' . $request->get('unit_in_pack'). ' Kit)';
        $master_product_id = DB::table('master_product')->insertGetId($insertmasterproduct);
        $insertmasterproduct['supplier_product_number'] = ProperInput($request->get('supplier_product_number'));
		$insertmasterproduct['manufacture_product_number'] = ProperInput($request->get('manufacture_product_number'));
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

        $this->masterProduct->MakeProductHistory([
            'response' => Auth::user()->name.' created Product: '.$insertmasterproduct['ETIN'],
            'master_product_id' => $master_product_id,
            'action' => 'Add'
        ]);
        if($master_product_id){
            $this->masterProduct->insertProcessLog('Master Kit Product Store','Master Kit Product Inserted');
            UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Add Kit Product',
                'details' => 'Item '.$insertmasterproduct['ETIN'].'kit product created .',
                'type' => 'CWMS'
            ]);
        }
        $insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'],$request->all());
        $this->masterProduct->insertProcessLog('Master Kit Product Store','Master Kit Product Images Inserted');
        if(isset($request->kit_components)){
            $etinconcate = [];
            foreach($request->kit_components as $row_kit_components){
                if($row_kit_components['components_ETIN'] != '' && $row_kit_components['qty'] != ''){
                    $etinconcate[] = $row_kit_components['components_ETIN'] . '=' . $row_kit_components['qty'];
                    DB::table('master_product_kit_components')->insert([
                        'ETIN' => $insertmasterproduct['ETIN'],
                        'components_ETIN' => $row_kit_components['components_ETIN'],
                        'qty' => $row_kit_components['qty'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
            $etinscompon = implode(",",$etinconcate);
            UserLogs([
                'user_id' => Auth::user()->id,
                'action' => 'Click',
                'task' => 'Add Product Kit Components',
                'details' => 'Kit Product '.$insertmasterproduct['ETIN'].' select components product with qty are '.$etinscompon,
                'type' => 'CWMS'
            ]);
        }

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = DB::table('master_product')->where('id',$id)->first();

        $row->product_subcategory1_name = DB::table('product_subcategory')->where('sub_category_1', $row->product_subcategory1)->value('sub_category_1');
		$row->product_subcategory2_name = DB::table('product_subcategory')->where('sub_category_2', $row->product_subcategory2)->value('sub_category_2');
		$row->product_subcategory3_name = DB::table('product_subcategory')->where('sub_category_3', $row->product_subcategory3)->value('sub_category_3');

        //Getting all Product Type list
        $getproducttype = DB::table('kit_description')->orderBy('kit_description','ASC')->get();
        foreach ($getproducttype as $producttypes){
            $producttype[] = $producttypes->kit_description;
        }

        //Getting all Product Temparaure list
        $getproducttemp = DB::table('product_temp')->orderBy('product_temperature','ASC')->get();
        foreach ($getproducttemp as $producttemps){
            $producttemp[] = $producttemps->product_temperature;
        }

        //Getting all Clients list
        $getetailers = DB::table('etailer_availability')->orderBy('etailer_availability','ASC')->get();
        foreach ($getetailers as $etailerlist){
            $etailers[$etailerlist->id] = $etailerlist->etailer_availability;
        }

        //Getting all Warehouse list
        $getwarehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        foreach ($getwarehouses as $warehouselist){
            $warehouse[] = $warehouselist->warehouses;
        }

        //Getting all Product Tags list
        $getproducttags = DB::table('product_tags')->orderBy('tag','ASC')->get();
        foreach ($getproducttags as $producttags){
            $producttag[] = $producttags->tag;
        }

        //Getting all Clients list
        $getclients = DB::table('clients')->orderBy('company_name', 'ASC')->get();
        $client = $getclients->pluck('company_name','id')->toArray();

        //Getting all allergens list
        $allergens = [];
        $getallergens = DB::table('allergens')->orderBy('allergens','ASC')->get();
        foreach ($getallergens as $row_allergens){
            $allergens[] = $row_allergens->allergens;
        }

        //Getting all Category list
		 $categories = DB::table('categories')->where('level',0)->orderBy('name','ASC')->get();
		//  dd($categories);

        $image_type = DB::table('image_type')->get();

        $selected_products = [];

        $kit_com = DB::table('master_product_kit_components')->leftJoin('master_product',function($join){
            $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
        })->select('master_product.id','master_product_kit_components.qty')->where('master_product_kit_components.ETIN',$row->ETIN)->get();
        if($kit_com){
            foreach($kit_com as $row_kit_components){
                $selected_products[] = $row_kit_components->id.'#'.$row_kit_components->qty;
            }
        }
        //Get ETIN
		$getet = [];
		$upcs = [];
        $brand = [];
        $product_listing_name = [];
		// $getupcs =  DB::table('master_product')->where('is_approve', 1)->where(function($q){
        //     $q->whereNull('parent_ETIN');
        //     $q->orWhere('parent_ETIN','=','');
        // })->where('item_form_description','!=','Kit')->get();
		// foreach ($getupcs as $getupc){
		// 	$upcs[] = $getupc->upc;
		// 	$getet[] = $getupc->ETIN;
        //     $brand[] = $getupc->brand;
        //     $product_listing_name[] = $getupc->product_listing_name;
		// }

        //Getting all Product Tags list
		 $getproducttags = DB::table('product_tags')->orderBy('tag')->get();
		 foreach ($getproducttags as $producttags){
			 $producttag[$producttags->id] = $producttags->tag;
		 }

        $product_images = DB::table('master_product_images')->where('ETIN',$row->ETIN)->get();
        $product_request_details = DB::table('master_product_queue')->where('master_product_id',$id)->first();
        
        $product_status = DB::table('product_statuses')->get();

        $onHandQty = $this->getKitQuantity($row->ETIN);

		return view('cranium.kitproducts.edit', ['onHandQty' => $onHandQty, 'producttemp' => $producttemp, 'etailers' => $etailers, 'warehouse' => $warehouse, 'producttype' => $producttype,'image_types' => $image_type,'row' => $row,'selected_products' => implode(',',$selected_products),'product_images' => $product_images,'product_request_details' => $product_request_details,'producttag'=>$producttag,'client'=>$client,'allergens'=>$allergens,'categories'=>$categories, 'brand' => $brand, 'getet' => $getet, 'upcs' => $upcs, 'product_listing_name' => $product_listing_name,'product_status' => $product_status]);
    }

    public function edit_request($id){
        $row = DB::table('master_product_queue')->where('id',$id)->first();
        //Getting all Product Type list
        $getproducttype = DB::table('kit_description')->orderBy('kit_description','ASC')->get();
        foreach ($getproducttype as $producttypes){
            $producttype[] = $producttypes->kit_description;
        }

        //Getting all Product Temparaure list
        $getproducttemp = DB::table('product_temp')->orderBy('product_temperature','ASC')->get();
        foreach ($getproducttemp as $producttemps){
            $producttemp[] = $producttemps->product_temperature;
        }

        //Getting all Clients list
        $getetailers = DB::table('etailer_availability')->orderBy('etailer_availability','ASC')->get();
        foreach ($getetailers as $etailerlist){
            $etailers[] = $etailerlist->etailer_availability;
        }

        //Getting all Warehouse list
        $getwarehouses = DB::table('warehouses')->orderBy('warehouses','ASC')->get();
        foreach ($getwarehouses as $warehouselist){
            $warehouse[] = $warehouselist->warehouses;
        }

        $image_type = DB::table('image_type')->get();

        $selected_products = [];

        $kit_com = DB::table('master_product_kit_components')->leftJoin('master_product',function($join){
            $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
        })->select('master_product.id','master_product_kit_components.qty')->where('master_product_kit_components.ETIN',$row->ETIN)->get();
        if($kit_com){
            foreach($kit_com as $row_kit_components){
                $selected_products[] = $row_kit_components->id.'#'.$row_kit_components->qty;
            }
        }

        $product_images = DB::table('master_product_images')->where('ETIN',$row->ETIN)->get();

		return view('cranium.kitproducts.edit_request', ['producttemp' => $producttemp, 'etailers' => $etailers, 'warehouse' => $warehouse, 'producttype' => $producttype,'image_types' => $image_type,'row' => $row,'selected_products' => implode(',',$selected_products),'product_images' => $product_images]);
    }

    private function getKitQuantity($kit_etin) {
        
        $result = [];
        $etins = [];

        $kit_comps = MasterProductKitComponents::leftJoin('master_product', function($join){
            $join->on('master_product.ETIN','=','master_product_kit_components.components_ETIN');
        })
        ->select('master_product_kit_components.*')
        ->where('master_product_kit_components.ETIN', $kit_etin)->get();
        
        if($kit_comps && count($kit_comps) > 0){
            foreach($kit_comps as $row_kit_components){
                array_push($etins, $row_kit_components->components_ETIN);
            }
        }

        $warehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->get();
        foreach ($warehouses as $warehouse) {
			
            $AisleMaster = AisleMaster::where('warehouse_id',$warehouse->id)->pluck('id')->toArray();
            $count = 0;
                
            $masterShelfSum = 0;
            foreach($etins as $et) {
                $shelfSum = MasterShelf::where('ETIN',$et)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
                $mp = MasterProduct::where('ETIN', $et)->first();
                if (isset($mp) && isset($mp->parent_ETIN)) {		
                    $etin = $mp->parent_ETIN;
                    $units_in_pack_child = $mp->unit_in_pack;
        
                    $parent = MasterProduct::where('ETIN', $etin)->first();
                    if($parent){
                        $units_in_pack_parent = $parent->unit_in_pack;

                        $masterShelfSum_parent = MasterShelf::where('ETIN',$etin)->whereIN('aisle_id',$AisleMaster)->sum('cur_qty');
                        if (isset($masterShelfSum_parent) && $masterShelfSum_parent > 0 && $units_in_pack_child > 0) {
                            $count = floor(($masterShelfSum_parent * $units_in_pack_parent)/$units_in_pack_child);
                        }
                        if ($masterShelfSum < ($shelfSum + $count)) {
                            $masterShelfSum = $shelfSum + $count;
                        }
                    }                    
                } else {
                    $sum = MasterShelf::whereIn('ETIN',$etins)
                        ->whereIN('aisle_id',$AisleMaster)
                        ->where('cur_qty', '>', 0)
                        ->orderBy('cur_qty', 'asc')
                        ->limit(1)
                        ->sum('cur_qty');
                    $masterShelfSum += !isset($sum) && $sum <= 0 ? 0 : $sum;                        
                }
            }
            $result[$warehouse->warehouses] = $masterShelfSum;
        }

        return $result;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(KitProductsRequest $request, $id)
    {
        // dd($request->all());
        $validate_images = $this->masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }

        $total_kit_components = 0;
        if(isset($request->kit_components) && !empty($request->kit_components)){
            $total_kit_components = count($request->kit_components);
        }
        if($total_kit_components <= 1){
            return response()->json([
                'error' => 1,
                'msg' => "Kits must have more than 1 item",
            ]);
        }

        $this->masterProduct->insertProcessLog('Master Kit Product Update','Image Validated');
        $insertmasterproduct = [];
        $insertmasterproduct = [];

		$insertmpt = [];

        if($request->get('product_temperature')){
			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1){
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if($request->get('product_temperature') == "Frozen"){
				$insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Strong"){
				$insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Refrigerated"){
				$insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Beverages"){
				$insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Perishable"){
				$insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Fragile"){
				$insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Thaw & Serv"){
				$insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			}

		}

		//Etin ready for ProductImage table

		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#' , $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
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
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = $request->get('storage');
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['asin'] = $request->get('asin');
        $insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = $request->get('package_information');
		$insertmasterproduct['cost'] = $request->get('cost');
        $insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
		$insertmasterproduct['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
        $insertmasterproduct['updated_by'] = Auth::user()->id;
        $insertmasterproduct['queue_status'] = 'e';
		$insertmasterproduct['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ' (' . $request->get('unit_in_pack'). ' Kit)';

        $oldrecord = DB::table('master_product')->find($id);
        $old_data = $oldrecord;
		$oldrecordarray = (array)$oldrecord;
		$oldrecordarray['id'] = null;
        $oldrecordarray['updated_by'] = Auth::user()->id;
		$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
        $this->masterProduct->insertProcessLog('Master Kit Product Update','Master Kit Product History Inserted');
		$check_master_product_queue = DB::table('master_product')->where('id',$id)->first();
        if(!$check_master_product_queue){
			$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
			$insertmasterproduct['master_product_id'] =$id;
			$master_product_id = DB::table('master_product')->insertGetId($insertmasterproduct);
            $this->masterProduct->MakeProductHistory([
                'response' => Auth::user()->name.' created Product: '.$insertmasterproduct['ETIN'],
                'master_product_id' => $master_product_id,
                'action' => 'Add'
            ]);
            $this->masterProduct->insertProcessLog('Master Kit Product Update','Master Kit Product Inserted');
		}else{
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
			$affected = DB::table('master_product')->where('id', $id)->update($insertmasterproduct);
            $this->masterProduct->MakeProductHistory([
                'old_data' => $old_data,
                'new_data' => $insertmasterproduct,
                'master_product_id' => $id,
                'action' => 'Edit'
            ]);
            $this->masterProduct->insertProcessLog('Master Kit Product Update','Master Kit Product Updated');

		}

        $insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'],$request->all());
        $this->masterProduct->insertProcessLog('Master Kit Product Update','Master Kit Product Images Updated');
        if(isset($request->kit_components)){
            DB::table('master_product_kit_components')->where('ETIN',$insertmasterproduct['ETIN'])->delete();
            foreach($request->kit_components as $row_kit_components){
                if($row_kit_components['components_ETIN'] != '' && $row_kit_components['qty'] != ''){
                    DB::table('master_product_kit_components')->insert([
                        'ETIN' => $insertmasterproduct['ETIN'],
                        'components_ETIN' => $row_kit_components['components_ETIN'],
                        'qty' => $row_kit_components['qty'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
    }

    public function updateApproveBackup(KitProductsRequest $request, $id)
    {
        // dump($request->all());
        $validate_images = $this->masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }
        $insertmasterproduct = [];

		$insertmpt = [];

        if($request->get('product_temperature')){
			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1){
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if($request->get('product_temperature') == "Frozen"){
				$insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Strong"){
				$insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Refrigerated"){
				$insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Beverages"){
				$insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Perishable"){
				$insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Fragile"){
				$insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Thaw & Serv"){
				$insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			}

		}

		//Etin ready for ProductImage table

		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#' , $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
		$insertmasterproduct['item_form_description'] = ProperInput($request->get('item_form_description'));
		$insertmasterproduct['total_ounces'] = $request->get('total_ounces');
		$insertmasterproduct['product_category'] = $request->get('product_category');
		$insertmasterproduct['product_subcategory1'] = $request->get('product_subcategory1');
		$insertmasterproduct['product_subcategory2'] = $request->get('product_subcategory2');
		$insertmasterproduct['product_subcategory3'] = $request->get('product_subcategory3');
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = $request->get('storage');
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['asin'] = $request->get('asin');
        $insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = $request->get('package_information');
		$insertmasterproduct['cost'] = $request->get('cost');
        $insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
		$insertmasterproduct['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
        $insertmasterproduct['updated_by'] = Auth::user()->id;
        $insertmasterproduct['queue_status'] = 'e';
		$insertmasterproduct['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ' (' . $request->get('unit_in_pack'). ' Kit)';

        $oldrecord = DB::table('master_product')->find($id);
		$oldrecordarray = (array)$oldrecord;
		$oldrecordarray['id'] = null;
		$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
		$check_master_product_queue = DB::table('master_product_queue')->where('master_product_id',$id)->first();
        if(!$check_master_product_queue){
			$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
            $insertmasterproduct['updated_by'] = Auth::user()->id;
			$insertmasterproduct['master_product_id'] =$id;
			DB::table('master_product_queue')->insert($insertmasterproduct);
		}else{
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
            $insertmasterproduct['updated_by'] = Auth::user()->id;
			$affected = DB::table('master_product_queue')->where('master_product_id', $id)->update($insertmasterproduct);

		}

        $insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'],$request->all());
        // if(isset($request->kit_components)){
        //     foreach($request->kit_components as $row_kit_components){
        //         if($row_kit_components['components_ETIN'] != '' && $row_kit_components['qty'] != ''){
        //             DB::table('master_product_kit_components')->insert([
        //                 'ETIN' => $insertmasterproduct['ETIN'],
        //                 'components_ETIN' => $row_kit_components['components_ETIN'],
        //                 'qty' => $row_kit_components['qty'],
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //         }
        //     }
        // }

		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
    }


    public function update_request(Request $request, $id)
    {
        // dump($request->all());
        $validate_images = $this->masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }
        $insertmasterproduct = [];

		$insertmpt = [];

        if($request->get('product_temperature')){
			$explodearray = explode('-', $request->get('ETIN'));
			$etinmid = NULL;
			if (count($explodearray) > 1){
				$insertmasterproduct['ETIN'] = end($explodearray);
				$etinmid = $explodearray[1];
			} else {
				$insertmasterproduct['ETIN'] = $request->get('ETIN');
			}
			if($request->get('product_temperature') == "Frozen"){
				$insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Strong"){
				$insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Refrigerated"){
				$insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Beverages"){
				$insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Perishable"){
				$insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Dry-Fragile"){
				$insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else if($request->get('product_temperature') == "Thaw & Serv"){
				$insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			} else {
				$insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
			}

		}

		//Etin ready for ProductImage table

		$insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
		$insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
		$insertmasterproduct['about_this_item'] = implode('#' , $request->get('about_this_item'));
		$insertmasterproduct['manufacturer'] = $request->get('manufacturer');
		$insertmasterproduct['brand'] = $request->get('brand');
		$insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
		$insertmasterproduct['product_type'] = $request->get('product_type');
		$insertmasterproduct['item_form_description'] = ProperInput($request->get('item_form_description'));
		$insertmasterproduct['total_ounces'] = $request->get('total_ounces');
		$insertmasterproduct['product_category'] = $request->get('product_category');
		$insertmasterproduct['product_subcategory1'] = $request->get('product_subcategory1');
		$insertmasterproduct['product_subcategory2'] = $request->get('product_subcategory2');
		$insertmasterproduct['product_subcategory3'] = $request->get('product_subcategory3');
		$insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
		$insertmasterproduct['product_tags'] = $request->get('product_tags');
		$insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
		$insertmasterproduct['storage'] = $request->get('storage');
		$insertmasterproduct['ingredients'] = $request->get('ingredients');
		$insertmasterproduct['allergens'] = $request->get('allergens');
		$insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
		$insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
		$insertmasterproduct['product_temperature'] = $request->get('product_temperature');
		$insertmasterproduct['asin'] = $request->get('asin');
        $insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
        $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
        $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
        $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
		$insertmasterproduct['weight'] = $request->get('weight');
		$insertmasterproduct['length'] = $request->get('length');
		$insertmasterproduct['width'] = $request->get('width');
		$insertmasterproduct['height'] = $request->get('height');
		$insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
		$insertmasterproduct['package_information'] = $request->get('package_information');
		$insertmasterproduct['cost'] = $request->get('cost');
        $insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
		$insertmasterproduct['new_cost'] = $request->get('new_cost');
		$insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
		$insertmasterproduct['status'] = $request->get('status');
		$insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
		$insertmasterproduct['POG_flag'] = $request->get('POG_flag');
		$insertmasterproduct['consignment'] = $request->get('consignment');
		$insertmasterproduct['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
		$insertmasterproduct['lobs'] = $request->get('lobs');
		$insertmasterproduct['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
		$insertmasterproduct['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
		$insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
		$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
		$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
        $insertmasterproduct['queue_status'] = 'e';
        $insertmasterproduct['updated_by'] = Auth::user()->id;
		$insertmasterproduct['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ' (' . $request->get('unit_in_pack'). ' Kit)';


		$check_master_product_queue = DB::table('master_product_queue')->where('id',$id)->first();
        if(!$check_master_product_queue){
			$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
            $insertmasterproduct['updated_by'] = Auth::user()->id;
			$insertmasterproduct['master_product_id'] =$id;
			DB::table('master_product_queue')->insert($insertmasterproduct);
		}else{
			$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
			$affected = DB::table('master_product_queue')->where('id', $id)->update($insertmasterproduct);
		}
        $insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'],$request->all());


		return response()->json([
			'error' => 0,
			'msg' => 'Master Product Added Sucessfully',
			'url' => url('/allmasterproductlsts')
		]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


	public function GetAllParentApprovedProducts(Request $request)
    {

        if ($request->ajax()) {
			if($request->ids == ''){
				// $dataget = DB::table('master_product')->where('is_approve', 1)->where(function($q){
                //     $q->whereNull('parent_ETIN');
                //     $q->orWhere('parent_ETIN','=','');
                // })->where('item_form_description','!=','Kit');

               	$dataget = DB::table('master_product')->where('is_approve', 1)->where('item_form_description','!=','Kit')->select('master_product.id','master_product.ETIN','master_product.brand','master_product.product_listing_name','master_product.upc','pack_form_count','unit_in_pack','unit_description','unit_size');

                if($request->etin_filter != '' ){
                    if(isset($request->etin_filter)){
                        $dataget->whereIn('ETIN', $request->etin_filter);
                    }
                }
                if($request->brand_filter != '' ){
                    if(isset($request->brand_filter)){
                        $dataget->whereIn('brand', $request->brand_filter);
                    }
                }
                if($request->product_list_filter != '' ){
                    if(isset($request->product_list_filter)){
                        $dataget->whereIn('product_listing_name', $request->product_list_filter);
                    }
                }
                if($request->upc_filter != '' ){
                    if(isset($request->upc_filter)){
                        $dataget->whereIn('upc', $request->upc_filter);
                    }
                }

                if($request->client_id != ''){
                    $dataget->whereRaw('FIND_IN_SET('.$request->client_id.',lobs)');

                }

                $data = $dataget->get();

                // dd($data);
			}else{
                $ids = explode(',',$request->ids);
                $product_data = [];
                $pro_ids = [];
                if($ids){
                    foreach($ids as $row){
                        $data = explode('#',$row);
                        if($data){
                            $product_data[] = [
                                'id' => $data[0],
                                'qty' => $data[1],
                            ];
                            $pro_ids[] = $data[0];
                        }
                    }
                }
				// $dataget = DB::table('master_product')->where('is_approve', 1)->whereNotIn('id',$pro_ids)->where('is_approve', 1)->where(function($q){
                //     $q->whereNull('parent_ETIN');
                //     $q->orWhere('parent_ETIN','=','');
                // });

                $dataget = DB::table('master_product')->where('is_approve', 1)->whereNotIn('id',$pro_ids)->where('is_approve', 1)->select(['master_product.id','master_product.ETIN','master_product.brand','master_product.product_listing_name','master_product.upc','pack_form_count','unit_in_pack','unit_description','unit_size']);

                if($request->etin_filter != '' ){
                    if(isset($request->etin_filter)){
                        $dataget->whereIn('ETIN', $request->etin_filter);
                    }
                }
                if($request->brand_filter != '' ){
                    if(isset($request->brand_filter)){
                        $dataget->whereIn('brand', $request->brand_filter);
                    }
                }
                if($request->product_list_filter != '' ){
                    if(isset($request->product_list_filter)){
                        $dataget->whereIn('product_listing_name', $request->product_list_filter);
                    }
                }
                if($request->upc_filter != '' ){
                    if(isset($request->upc_filter)){
                        $dataget->whereIn('upc', $request->upc_filter);
                    }
                }

                $data = $dataget->get();
			}
            return Datatables::of($data)
                    ->addColumn('action', function($row){
							$btn = '';
							$btn = '<a href="javascript:void(0)" onclick="selectProduct('.$row->id.')"  class="edit btn btn-primary btn-sm">Add Product >> </a>';
                            return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }

	public function GetSelectedProductForKit(Request $request)
    {

        if ($request->ajax()) {

			if($request->ids != ''){
                $id = $request->id;
                if($id != ''){
                    $pro = DB::table('master_product')->where('id',$id)->first();
                }
                $ids = explode(',',$request->ids);
                $product_data = [];
                $pro_ids = [];

                if($ids){
                    foreach($ids as $row){
                        $data = explode('#',$row);
                        if($data){
                            $product_data[] = [
                                'id' => $data[0],
                                'qty' => $data[1],
                            ];
                            $pro_ids[] = $data[0];
                        }
                    }
                }

                $html='';
                $brand = [];
                $manufacturer = [];
                $prop_65_flag = 'No';
                $hazardous_materials = 'No';
                $prop_65_ingredient = [];
                $prop_65_ingredient_names = [];
                $product_tags = [];
                $product_tags_names = [];
                $lobs = [];
                $allergens = [];
                $allergens_names = [];
                $country_of_origin = [];
                $country_of_origin_names = [];
                $parent_ETIN = [];
                $product_category_name = [];
                $product_category = [];
                $product_subcategory1 = [];
                $product_subcategory2 = [];
                $product_subcategory3 = [];
                $key_product_attributes_diet = [];
                $storage = [];
                $ingredients = [];
                $package_information = [];
                $product_tags_arr = [];
                $lobs_arr = [];
                $lobs_names = [];
                $unit_in_pack = 0;
                if($product_data){
                    foreach($product_data as $key =>  $row_data){
                        $id = $row_data['id'].'#'.$row_data['qty'];
                        $result = DB::table('master_product')->leftJoin('categories',function($join){
                            $join->on('categories.id','=','master_product.product_category');
                        })->select('master_product.*','categories.name as product_category_name')->where('master_product.id',$row_data['id'])->first();
                        if($result){
                            $unit_in_pack+=$row_data['qty'];
                            $html.='<tr>
                                <td>'.$result->ETIN.'</td>
                                <td>'.$result->product_listing_name.'</td>
                                <td>

                                    <input type="hidden" name="kit_components['.$key.'][components_ETIN]" value="'.$result->ETIN.'" id="kit_components_etin">
                                    <input type="number" class="form-control" name="kit_components['.$key.'][qty]" value="'.$row_data['qty'].'" id="kit_components_qty" style="width:55px;padding:0px">
                                </td>
                                <td><a href="javascript:void(0)" class="btn btn-danger" onClick="removeProduct(\''.$id.'\')">Delete</a></td>
                            </tr>';
                            $brand[] = $result->brand;
                            $manufacturer[] = $result->manufacturer;
                            if($result->prop_65_flag == 'Yes'){
                                $prop_65_flag = 'Yes';
                            }

                            if($result->hazardous_materials == 'Yes'){
                                $hazardous_materials = 'Yes';
                            }
                            if($result->prop_65_ingredient != ''){
                                foreach(explode(',',$result->prop_65_ingredient) as $row_ing){
                                    $prop_65_ingredient[] = $row_ing;
                                    $prop_65_ingredient_names[] = prop_65_name($row_ing);
                                }
                            }

                            if($result->product_tags != ""){
                                foreach(explode(',',$result->product_tags) as $row_tags){
                                    $product_tags[] = $row_tags;

                                }

                                foreach(array_unique($product_tags) as $row_p){
                                    $product_tags_names[$row_p] = producttageName($row_p);
                                }
                            }

                            if($result->lobs != ""){
                                if($id != '' && isset($pro)){
                                    foreach(explode(',',$pro->lobs) as $row_lobs){
                                        $lobs[] = $row_lobs;
                                    }
                                }else{
                                    foreach(explode(',',$result->lobs) as $row_lobs){
                                        $lobs[] = $row_lobs;
                                    }
                                }
                                

                                foreach(array_unique($lobs) as $row_lobs){
                                    $lobs_names[$row_lobs] = clientName($row_lobs);
                                }


                            }

                            if($result->allergens != ""){
                                foreach(explode(',',$result->allergens) as $row_allergens){
                                    $allergens[] = $row_allergens;
                                    $allergens_names[] = allergensName($row_allergens);
                                }
                            }

                            if($result->country_of_origin != ""){
                                $country_of_origin[] = $result->country_of_origin;
                                $country_of_origin_names[] = countryName($result->country_of_origin);
                            }

                            if($result->ETIN != ''){
                                foreach(explode(',',$result->ETIN) as $row_parent_ETIN){
                                    $parent_ETIN[] = $row_parent_ETIN;
                                }
                            }

                            if($result->product_category != ''){
                                foreach(explode(',',$result->product_category) as $row_product_category){
                                    $product_category[] = $row_product_category;
                                    $product_category_name[] = $result->product_category_name;
                                }
                            }

                            if($result->product_subcategory1 != ''){
                                foreach(explode(',',$result->product_subcategory1) as $row_product_subcategory1){
                                    $product_subcategory1[] = $row_product_subcategory1;
                                }
                            }

                            if($result->product_subcategory2 != ''){
                                foreach(explode(',',$result->product_subcategory2) as $row_product_subcategory2){
                                    $product_subcategory2[] = $row_product_subcategory2;
                                }
                            }

                            if($result->product_subcategory3 != ''){
                                foreach(explode(',',$result->product_subcategory3) as $row_product_subcategory3){
                                    $product_subcategory3[] = $row_product_subcategory3;
                                }
                            }

                            if($result->key_product_attributes_diet != ''){
                                foreach(explode(',',$result->key_product_attributes_diet) as $row_key_product_attributes_diet){
                                    $key_product_attributes_diet[] = $row_key_product_attributes_diet;
                                }
                            }

                            if($result->storage != ''){
                                foreach(explode(',',$result->storage) as $row_storage){
                                    $storage[] = $row_storage;
                                }
                            }

                            if($result->ingredients != ''){
                                foreach(explode(',',$result->ingredients) as $row_ingredients){
                                    $ingredients[] = $row_ingredients;
                                }
                            }

                            if($result->package_information != ''){
                                foreach(explode(',',$result->package_information) as $row_package_information){
                                    $package_information[] = $row_package_information;
                                }
                            }
                        }
                    }

                }

                return response()->json([
                    'table_data' => $html,
                    'brand' => implode(',',array_unique($brand)),
                    'manufacturer' => implode(',',array_unique($manufacturer)),
                    'prop_65_flag' => $prop_65_flag,
                    'prop_65_ingredient' => implode(',',array_unique($prop_65_ingredient)),
                    'prop_65_ingredient_names' => implode(',',array_unique($prop_65_ingredient_names)),
                    'product_tags' => implode(',',array_unique($product_tags)),
                    'product_tags_names' => $product_tags_names,
                    'lobs' => implode(',',array_unique($lobs)),
                    'lobs_names' => $lobs_names,
                    'allergens' => implode(',',array_unique($allergens)),
                    'allergens_names' => implode(',',array_unique($allergens_names)),
                    'country_of_origin' => implode(',',array_unique($country_of_origin)),
                    'country_of_origin_names' => implode(',',array_unique($country_of_origin_names)),
                    'unit_in_pack' => $unit_in_pack,
                    'parent_ETIN' =>  implode(',',array_unique($parent_ETIN)),
                    'product_category' =>  implode(',',array_unique($product_category)),
                    'product_category_name' =>  implode(',',array_unique($product_category_name)),

                    'key_product_attributes_diet' =>  implode(',',array_unique($key_product_attributes_diet)),
                    'hazardous_materials' => $hazardous_materials,
                    'storage' => implode(',',array_unique($storage)),
                    'ingredients' => implode(',',array_unique($ingredients)),
                    'package_information' => implode(',',array_unique($package_information)),
                ]);
			}
        }
    }

    public function ApproveKitRequest(KitProductsRequest $request,$id){
        $check_master_product_queue = DB::table('master_product_queue')->where('id',$id)->first();
		$master_product_id = $check_master_product_queue->master_product_id;
		$oldrecord = DB::table('master_product')->where('id',$check_master_product_queue->master_product_id)->first();
		$proimage = [];

		$validate_images = $this->masterProduct->ValidateImages($request->all());
        if($validate_images['error']){
            return response()->json([
                'error' => 1,
                'msg' => $validate_images['msg']
            ]);
        }


			$oldrecordarray = (array)$oldrecord;
			$oldrecordarray['id'] = null;
			$inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
			$insertmasterproduct = [];
			if($check_master_product_queue){
				$excluded_keys = ['id','created_at','updated_at','product_edit_request','is_approve','approved_date','master_product_id','queue_status'];
				if($request->get('product_temperature')){
					$explodearray = explode('-', $request->get('ETIN'));
					if (count($explodearray) > 1){
						$insertmasterproduct['ETIN'] = end($explodearray);
						$etinmid = $explodearray[1];
					}
					if($request->get('product_temperature') == "Frozen"){
						$insertmasterproduct['ETIN'] = 'ETFZ-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Dry-Strong"){
						$insertmasterproduct['ETIN'] = 'ETDS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Refrigerated"){
						$insertmasterproduct['ETIN'] = 'ETRF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Beverages"){
						$insertmasterproduct['ETIN'] = 'ETBV-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Dry-Perishable"){
						$insertmasterproduct['ETIN'] = 'ETDP-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Dry-Fragile"){
						$insertmasterproduct['ETIN'] = 'ETDF-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					} else if($request->get('product_temperature') == "Thaw & Serv"){
						$insertmasterproduct['ETIN'] = 'ETTS-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					}  else {
						$insertmasterproduct['ETIN'] = 'ETOT-'.$etinmid.'-'.$insertmasterproduct['ETIN'];
					}
				}else{
					$insertmasterproduct['ETIN'] = $request->get('ETIN');
				}

				$proimage['ETIN'] = $insertmasterproduct['ETIN'];

                $insertmasterproduct['parent_ETIN'] = $request->get('parent_ETIN');
                $insertmasterproduct['full_product_desc'] = ProperInput($request->get('full_product_desc'));
                $insertmasterproduct['about_this_item'] = implode('#' , $request->get('about_this_item'));
                $insertmasterproduct['manufacturer'] = $request->get('manufacturer');
                $insertmasterproduct['brand'] = $request->get('brand');
                $insertmasterproduct['flavor'] = ProperInput($request->get('flavor'));
                $insertmasterproduct['product_type'] = $request->get('product_type');
                $insertmasterproduct['item_form_description'] = ProperInput($request->get('item_form_description'));
                $insertmasterproduct['total_ounces'] = $request->get('total_ounces');
                $insertmasterproduct['product_category'] = $request->get('product_category');
                $insertmasterproduct['product_subcategory1'] = $request->get('product_subcategory1');
                $insertmasterproduct['product_subcategory2'] = $request->get('product_subcategory2');
                $insertmasterproduct['product_subcategory3'] = $request->get('product_subcategory3');
                $insertmasterproduct['key_product_attributes_diet'] = $request->get('key_product_attributes_diet');
                $insertmasterproduct['product_tags'] = $request->get('product_tags');
                $insertmasterproduct['hazardous_materials'] = $request->get('hazardous_materials');
                $insertmasterproduct['storage'] = $request->get('storage');
                $insertmasterproduct['ingredients'] = $request->get('ingredients');
                $insertmasterproduct['allergens'] = $request->get('allergens');
                $insertmasterproduct['prop_65_flag'] = $request->get('prop_65_flag');
                $insertmasterproduct['prop_65_ingredient'] = $request->get('prop_65_ingredient');
                $insertmasterproduct['product_temperature'] = $request->get('product_temperature');
                $insertmasterproduct['asin'] = $request->get('asin');
                $insertmasterproduct['upc_scanable'] =  isset($request->upc_scanable) ? 1 : 0;
                $insertmasterproduct['gtin_scanable'] =  isset($request->gtin_scanable) ? 1 : 0;
                $insertmasterproduct['unit_upc_scanable'] =  isset($request->unit_upc_scanable) ? 1 : 0;
                $insertmasterproduct['unit_gtin_scanable'] =  isset($request->unit_gtin_scanable) ? 1 : 0;
                $insertmasterproduct['weight'] = $request->get('weight');
                $insertmasterproduct['length'] = $request->get('length');
                $insertmasterproduct['width'] = $request->get('width');
                $insertmasterproduct['height'] = $request->get('height');
                $insertmasterproduct['country_of_origin'] = $request->get('country_of_origin');
                $insertmasterproduct['package_information'] = $request->get('package_information');
                $insertmasterproduct['cost'] = $request->get('cost');
                $insertmasterproduct['acquisition_cost'] = $request->get('acquisition_cost');
                $insertmasterproduct['new_cost'] = $request->get('new_cost');
                $insertmasterproduct['new_cost_date'] = $request->get('new_cost_date');
                $insertmasterproduct['status'] = $request->get('status');
                $insertmasterproduct['etailer_availability'] = $request->get('etailer_availability');
                $insertmasterproduct['POG_flag'] = $request->get('POG_flag');
                $insertmasterproduct['consignment'] = $request->get('consignment');
                $insertmasterproduct['warehouses_assigned'] = implode(',' , $request->get('warehouses_assigned'));
                $insertmasterproduct['lobs'] = $request->get('lobs');
                $insertmasterproduct['alternate_ETINs'] = ProperInput($request->get('alternate_ETINs'));
                $insertmasterproduct['product_listing_ETIN'] = ProperInput($request->get('product_listing_ETIN'));
                $insertmasterproduct['unit_in_pack'] = $request->get('unit_in_pack');
                $insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
                $insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
                $insertmasterproduct['updated_by'] = Auth::user()->id;
                $insertmasterproduct['queue_status'] = 'e';
                $insertmasterproduct['product_listing_name'] = $request->get('brand'). ' ' .$request->get('flavor'). ' ' .$request->get('product_type'). ' (' . $request->get('unit_in_pack'). ' Kit)';
				$insertmasterproduct['is_edit'] = 1;
				$insertmasterproduct['is_approve'] = $request->get('is_approve');
                $insertmasterproduct['approved_date'] = $request->get('approved_date');
				$insertmasterproduct['product_edit_request'] = NULL;
				$CMP = DB::table('master_product')->where('id',$check_master_product_queue->master_product_id)->first();
				if($CMP){
					$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');
					DB::table('master_product')->where('id',$check_master_product_queue->master_product_id)->update($insertmasterproduct);
                    $this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name.' approved changes',
						'master_product_id' => $check_master_product_queue->master_product_id,
						'action' => 'Approved'
					]);
				}else{
					$insertmasterproduct['created_at'] = date('Y-m-d H:i:s');
					$insertmasterproduct['updated_at'] = date('Y-m-d H:i:s');$insertmasterproduct['updated_by'] = Auth::user()->id;
					$master_product_id = DB::table('master_product')->insertGetId($insertmasterproduct);
                    $this->masterProduct->MakeProductHistory([
						'response' => Auth::user()->name.' created Product: '.$insertmasterproduct['ETIN'],
						'master_product_id' => $master_product_id,
						'action' => 'Add'
					]);
				}

			}



		DB::table('master_product_images')->where('ETIN',$request->get('ETIN'))->update(['ETIN' => $insertmasterproduct['ETIN']]);
		$insert_image = $this->masterProduct->insertImageFzl($insertmasterproduct['ETIN'],$request->all());
		DB::table('master_product_queue')->where('id',$id)->delete();
		$data_info = [
			'msg' => 'Success',
			'error' => 0,
            'url' => route('allmasterproductlsts')
		];


        return response()->json($data_info);

    }

    public function ApproveKit(Request $request){
		$productid = $request->id;
		DB::table('master_product')->where('id', $productid)->update(['is_approve' => 1,'approved_date' => date('Y-m-d H:i:s'), 'is_edit' => 0, 'updated_by' => Auth::user()->id]);
        $this->masterProduct->MakeProductHistory([
            'response' => Auth::user()->name.' approved changes',
            'master_product_id' => $productid,
            'action' => 'Approved'
        ]);
        return response()->json([
            'error' => 0,
            'msg' => 'You have sucessfully published the product.'
        ]);
	}

    public function ETINAutoComplete(Request $request){
        $name = '';
        if(isset($request['search'])) $name = $request['search'];
            $result_obj =  MasterProduct::where('is_approve', 1)->where(function($q){
                $q->whereNull('parent_ETIN');
                $q->orWhere('parent_ETIN','=','');
            });
            if($name != ''){
                $result_obj->where('ETIN','LIKE','%'.$name.'%');
            }

            $result_obj->select('ETIN');

            $result = $result_obj->get()->toArray();

        return response()->json($result);
    }
}
