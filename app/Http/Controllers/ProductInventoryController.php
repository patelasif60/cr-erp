<?php

namespace App\Http\Controllers;

use DB;
use App\Client;
use DataTables;
use App\Productinventory;
use App\MasterShelf;
use App\WareHouse;
use App\InventorySummery;
use Illuminate\Http\Request;
use App\Services\ProductInventoryService;
use App\Exports\InventorySummeryExport;
use Illuminate\Support\Str;
use Excel;

class ProductInventoryController extends Controller
{
	public function __construct(ProductInventoryService $service)
    {
        $this->service = $service;
    }
    public function index(){
    	$etin = [];//DB::table('master_product')->where('is_approve', 1)->whereNull('parent_ETIN')->pluck('ETIN','id')->toArray();
    	$warehouses = [];//DB::table('warehouses')->orderBy('warehouses', 'ASC')->pluck('warehouses','id')->toArray();
        return view('product_inventory.index',['etin' => $etin,'warehouses'=>$warehouses]);
    }
    public function getProductInventorylist(Request $request){
    	//$results = $this->service->getProductInventorylist($request);
    	//$warehouse = WareHouse::all()->pluck('id')->toArray();
    	// $results = MasterShelf::whereNotNull('ETIN')->groupBy('ETIN')->get();
		$results = InventorySummery::whereNotNull('ETIN')->groupBy('ETIN')->get();
    	return $this->service->datatableData($results);

		// ->addColumn('warehouse_id', function ($results) {
		// 	return $results->werehouse->warehouses;
		// })
		// ->addColumn('pick_from_count', function ($results) {
		// 	return $results->masterProduct->pack_form_count;
		// })
		// ->addColumn('unit_in_pack', function ($results) {
		// 	return $results->masterProduct->unit_in_pack;
		// })
		// ->addColumn('inventory', function ($results) {
		// 	$inventory = $this->service->eachQty($results->ETIN,$results->warehouse_id);
		// 	return $inventory;
		// })
		// ->addColumn('each_qty', function ($results) {
		// 	$inventory = $this->service->eachQty($results->ETIN,$results->warehouse_id);
		// 	return $results->masterProduct->unit_in_pack * $results->masterProduct->pack_form_count * $inventory;
		// })
    	//->rawColumns(['action'])->make(true);
    }
    public function store(Request $request){
        $input = $request->all();
     	$results = $this->service->store($input);
     	if(!$results)
        {
           $data_info = [
                'msg' => 'Product ETIN Already Exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }
    public function editProductInventory(Request $request){
    	$productinventory = Productinventory::find($request->id);
    	$etin = DB::table('master_product')->where('is_approve', 1)->whereNull('parent_ETIN')->pluck('ETIN','id')->toArray();
    	$warehouses = DB::table('warehouses')->orderBy('warehouses', 'ASC')->pluck('warehouses','id')->toArray();
        return view('product_inventory.edit',['etin' => $etin,'warehouses'=>$warehouses,'productinventory' => $productinventory]);
    }
    public function update(Request $request){  
      	$input = $request->all();
     	$results = $this->service->update($input);
     	if(!$results)
        {
           $data_info = [
                'msg' => 'Product ETIN Already Exists',
                'error' => 1
            ];
        }
        else{
        	$data_info = [
                'msg' => 'Success',
                'error' => 0
            ];
        }
        return response()->json($data_info);
    }
    public function getChildProductInventorylist(Request $request){
    	$results = $this->service->getChildProductInventorylist($request);
    	return $this->service->childDatatableData($results,$request);
  //   	return Datatables::of($results)	
		// ->addColumn('each_qty', function ($results) use ($request) {
		// 	$inventory = $this->service->eachQty($request->etin,$request->warehouse_id);
		// 	if($inventory > 0)
		// 	{																														
		// 		return $inventory / ($results->pack_form_count * $results->unit_in_pack);	
		// 	}
		// 	return 0;
		// })->rawColumns(['action'])->make(true);
    }
    public function kitIndex(){
        return view('product_inventory.kit_index');
    }
    public function getProductKitInventorylist(Request $request){
    	$results = DB::table('master_product')->where('is_approve', 1)->where('item_form_description','kit')->whereRaw('FIND_IN_SET(\''.$request->name.'\',master_product.warehouses_assigned)')->get();
    	return Datatables::of($results)
		->editColumn('product_listing_name', function ($results) {
			return Str::limit($results->product_listing_name,100, ('...'));
		})
		->addColumn('kit_inventory', function ($results) use ($request) {
			$total=[];
			$data = DB::table('master_product_kit_components')->where('ETIN', $results->ETIN)->get();
			if(count($data) > 0){
				foreach($data as $key=>$val)
				{
					$inventory =  $this->service->eachQty($val->components_ETIN,$request->warehouse_id);
					$unitPack = DB::table('master_product')->where('ETIN', $val->components_ETIN)->first();
					$each_qty =$unitPack->unit_in_pack * $unitPack->pack_form_count * $inventory;
					// if($results->ETIN == 'ETBV-1000-3030'){
			  //           echo  $inventory.'-';
			  //           echo $request->warehouse_id.'-';
			  //           echo $unitPack->unit_in_pack.'-';
			  //           echo $unitPack->pack_form_count.'-';
			  //           dd($each_qty);
			  //           dd($aisleMaster->shelfFromAisle()->where('ETIN',$ETIN)->sum('cur_qty'));
			  //       }
					$componentInventory =  $each_qty * $inventory;
					$total[$val->components_ETIN] = $componentInventory==0 ? 0 : (($each_qty * $inventory) / $val->qty);
				}
				// if($results->ETIN == 'ETBV-0000-3028'){
				// 	dd($total);
				// }
				return min($total);
			}
			return 0;
		})->rawColumns(['action'])->make(true);
    }
    public function warehouselist(Request $request){
    	$results = WareHouse::all();
    	return Datatables::of($results)
    	->addColumn('action', function($result)
        {
        	$btn = '';
            $btn .= '<a href="javascript:void(0);" onClick="openChildModal(\''.$result->id.'\',\''.$result->warehouses.'\')" class="btn btn-primary ml-2">Show Warehouse Kit</a>';
            return $btn;

        })->rawColumns(['action'])->make(true);
    }

	public function exportInv(){
		$results = InventorySummery::select('ETIN',
        'parent_ETIN',
        'wi_qty',
        'wi_each_qty',
        'wi_orderable_qty',
        'wi_fulfilled_qty',
        'wi_open_order_qty',
        'pa_qty',
        'pa_each_qty',
        'pa_orderable_qty',
        'pa_fulfilled_qty',
        'pa_open_order_qty',
        'nv_qty',
        'nv_each_qty',
        'nv_orderable_qty',
        'nv_fulfilled_qty',
        'nv_open_order_qty',
        'okc_qty',
        'okc_each_qty',
        'okc_orderable_qty',
        'okc_fulfilled_qty',
        'okc_open_order_qty')->whereNotNull('ETIN')->groupBy('ETIN')->get();
		return  Excel::download(new InventorySummeryExport($results), time().'_.xlsx', null, [\Maatwebsite\Excel\Excel::XLSX]);
	}
}