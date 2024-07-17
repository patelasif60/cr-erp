<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MasterProductQueue;
use App\MasterProduct;
use App\SupplierStatus;
use DB;
class ParentProductWizardController extends Controller
{

    public function __construct(MasterProductQueue $MasterProductQueue){
        $this->MasterProductQueue = $MasterProductQueue;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		if(moduleacess('ParentProductWizard') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
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
		 foreach ($getbrand as $brands){
			 $brand[] = $brands->brand;			 
		 }
		 
		 //Getting all Manufacturer list
		 $manufacturer = [];
		//  $getmanufacturer = DB::table('manufacturer')->orderBy('manufacturer_name')->get();		 
		//  foreach ($getmanufacturer as $manufacturers){		 
		// 	 $manufacturer[] = $manufacturers->manufacturer_name;		 
		//  }
		 
		 //Getting all Category list
		 $getcategory = DB::table('categories')->orderBy('name')->get();		 
		 foreach ($getcategory as $categorys){		 
			$categoryid[] = $categorys->id;
			$categoryname[] = $categorys->name;
			$category = array_combine( $categoryid, $categoryname );			 
		 }
		 
		 //Getting all Product Type list
		 $getproducttype = DB::table('product_type')->get();		 
		 foreach ($getproducttype as $producttypes){		 
			 $producttype[] = $producttypes->product_type;			 
		 }
		 
		 //Getting all Unit Size list
		 $getunitsizes = DB::table('unit_sizes')->get();		 
		 foreach ($getunitsizes as $unitsizes){		 
			 $unitname[] = $unitsizes->unit;			 
			 $unitabb[] = $unitsizes->abbreviation;
			$unitsize = array_combine( $unitabb, $unitname );
		 }
		 
		 //Getting all Unit Description list
		 $getunitdesc = DB::table('unit_desc')->get();		 
		 foreach ($getunitdesc as $unitdescs){		 
			 $unitdesc[] = $unitdescs->unit_description;			 
		 }
		 
		 //Getting all Product Tags list
		 $getproducttags = DB::table('product_tags')->orderBy('tag')->get();		 
		 foreach ($getproducttags as $producttags){		 
			 $producttag[] = $producttags->tag;			 
		 }
		 
		 //Getting all Product Temparaure list
		 $getproducttemp = DB::table('product_temp')->orderBy('product_temperature')->get();		 
		 foreach ($getproducttemp as $producttemps){		 
			 $producttemp[] = $producttemps->product_temperature;			 
		 }		 
		 		 
		 //Getting all Suppliers list
		 $getsuppliers = DB::table('suppliers')->where('status', 'Active')->orderBy('name')->get();		 
		 foreach ($getsuppliers as $suppliers){	
			$supplier_id[] = $suppliers->id;
			$supplier_name[] = $suppliers->name;
			$supplier = array_combine( $supplier_id, $supplier_name );
		}
		
		//Getting all Country Of Origin list
		 $getcountries = DB::table('country_of_origin')->orderBy('country_of_origin')->get();		 
		 foreach ($getcountries as $countries){		 
			 $country[] = $countries->country_of_origin;			 
		 }
		 
		 //Getting all Item From Description list
		 $getitemsdescs = DB::table('item_from_description')->get();		 
		 foreach ($getitemsdescs as $itemsdescs){		 
			 $itemsdesc[] = $itemsdescs->item_desc;			 
		 }
		 
		//Getting all Clients list
		 $getclients = DB::table('clients')->get();		 
		 foreach ($getclients as $clients){		 
			 $client[] = $clients->company_name;			 
		 }
		 
		 //Getting all Clients list
		 $getetailers = DB::table('etailer_availability')->get();		 
		 foreach ($getetailers as $etailerlist){		 
			 $etailers[] = $etailerlist->etailer_availability;			 
		 }
		 //Getting all Warehouse list
		 $getwarehouses = DB::table('warehouses')->get();		 
		 foreach ($getwarehouses as $warehouselist){		 
			 $warehouse[] = $warehouselist->warehouses;			 
		 }

		 //Getting all Supplier Status
		 $supplier_status = SupplierStatus::all();

		 //Getting all prop_ingredients list
		 $prop_ingredients = [];
		 $getprop_ingredients = DB::table('prop_ingredients')->orderBy('prop_ingredients')->get();		 
		 foreach ($getprop_ingredients as $productprops){		 
			 $prop_ingredients[] = $productprops->prop_ingredients;			 
		 }

		 //Getting all allergens list
		 $allergens = [];
		 $getallergens = DB::table('allergens')->orderBy('allergens')->get();		 
		 foreach ($getallergens as $row_allergens){		 
			 $allergens[] = $row_allergens->allergens;			 
		 }
		
		return view('cranium.parentProductWizard', ['brand' => $brand, 'manufacturer' => $manufacturer, 'category' => $category, 'producttype' => $producttype, 'unitsize' => $unitsize, 'unitdesc' => $unitdesc, 'producttag' => $producttag, 'producttemp' => $producttemp, 'supplier' => $supplier, 'country' => $country, 'itemsdesc' => $itemsdesc, 'client' => $client, 'newetin' => $newetin, 'etailers' => $etailers, 'warehouse' => $warehouse, 'supplier_status' => $supplier_status, 'prop_ingredients' => $prop_ingredients, 'allergens' => $allergens]);
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function add(Request $request){
		if(moduleacess('ParentProductWizard') == false){
			return redirect('/home')->withErrors(['errors' => 'You Don\'t Have Permission To Access This.']);
		}
        // dd($request->all());
        $input = $request->all();
        $result = $this->MasterProductQueue->updateOrCreate(
        [
            'ETIN' => $input['ETIN'],
        ],
        [
            'ETIN' => $input['ETIN'],
            'status' => $input['status']

        ]);
        
        return response()->json([
			'error' => 0,
			'msg' => 'Success',
			'url' => url('/parentproductwizard')
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
}
