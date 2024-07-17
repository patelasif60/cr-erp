<?php

namespace App\Http\Controllers;

use App\Client;
use DataTables;
use App\Supplier;
use App\WareHouse;
use App\MaterialType;
use App\MasterProduct;
use App\ProductTemperature;
use Illuminate\Http\Request;
use App\PackageKitComponents;
use App\MaterialWarehouseTdCount;
use App\Services\SupplierService;
use Illuminate\Support\Facades\DB;
use App\ClientChannelConfiguration;

class PackageKitController extends Controller
{
	public function __construct(SupplierService $service)
	{
        $this->service = $service;
	}
	public function create($id){
        $type ='kit';
        $supplier = Supplier::find($id);       
        $mstrProd = new MasterProduct();
        $newetin = $mstrProd->getETIN('','package');
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        return view('cranium.kitpackaging.create',compact('materialTypes','producttemp','client','newetin','warehouse','supplier','type'));
    }
    public function edit($id){
        $type ='kit';
        $pagetype ='';
        $packagingMatirials = $this->service->editpackagematerial($id);

        $packageKitComponents = DB::table('package_kit_components')->leftJoin('packaging_materials',function($join){
            $join->on('packaging_materials.ETIN','=','package_kit_components.components_ETIN');
        })->select('packaging_materials.id as pid','packaging_materials.product_description as product_description','package_kit_components.qty as qty','package_kit_components.components_ETIN as components_ETIN')->where('package_kit_components.ETIN',$packagingMatirials->ETIN)->get();
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        $channels = ClientChannelConfiguration::where('client_id', $packagingMatirials->clients_assigned)->get()->toArray();

        if (isset($packagingMatirials->clients_assigned)) {
            $result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $packagingMatirials->clients_assigned . ',lobs)');
            $result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id','unit_description','item_form_description');	
        }

        $wh_td_count = [];
        $mwtc = MaterialWarehouseTdCount::where('material_id', $id)->get();
        if (isset($mwtc) && count($mwtc) > 0) {
            foreach($mwtc as $m) {
                $wh_td_count[$m->warehouse->warehouses][$m->transit_days] = $m->count;
            }
        }

		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();
        $all_unit_descs = [];
        $all_item_form_descs = []; 
        foreach($products as $mp) {
            if (isset($mp['unit_description']) && $mp['unit_description'] != '' 
                && !in_array($mp['unit_description'], $all_unit_descs)) {
                array_push($all_unit_descs, $mp['unit_description']);
            }

            if (isset($mp['item_form_description']) && $mp['item_form_description'] != '' 
                && !in_array($mp['item_form_description'], $all_item_form_descs)) {
                    array_push($all_item_form_descs, $mp['item_form_description']);
            }
        }

        return view('cranium.kitpackaging.edit',compact('packagingMatirials','materialTypes','producttemp',
        'client','warehouse','packageKitComponents','type','channels','products', 'wh_td_count','pagetype',
        'all_item_form_descs', 'all_unit_descs','id'));
    }
    public function packagekiteditlist($id){
        $type ='kit';
        $pagetype ='packaginglist';
        $packagingMatirials = $this->service->editpackagematerial($id);

        $packageKitComponents = DB::table('package_kit_components')->leftJoin('packaging_materials',function($join){
            $join->on('packaging_materials.ETIN','=','package_kit_components.components_ETIN');
        })->select('packaging_materials.id as pid','packaging_materials.product_description as product_description','package_kit_components.qty as qty','package_kit_components.components_ETIN as components_ETIN')->where('package_kit_components.ETIN',$packagingMatirials->ETIN)->get();
        $materialTypes = MaterialType::all();
        $producttemp = ProductTemperature::orderBy('product_temperature','ASC')->pluck('product_temperature')->toArray();
        $client = Client::orderBy('company_name','ASC')->pluck('company_name','id')->toArray();
        $warehouse = WareHouse::orderBy('warehouses','ASC')->pluck('warehouses')->toArray();
        $channels = ClientChannelConfiguration::where('client_id', $packagingMatirials->clients_assigned)->get()->toArray();

        if (isset($packagingMatirials->clients_assigned)) {
            $result_obj = MasterProduct::whereRaw('FIND_IN_SET(' . $packagingMatirials->clients_assigned . ',lobs)');
            $result_obj->select(DB::raw('CONCAT(product_listing_name," - ", ETIN) as product_name'),'ETIN','id','unit_description','item_form_description');	
        }

        $wh_td_count = [];
        $mwtc = MaterialWarehouseTdCount::where('material_id', $id)->get();
        if (isset($mwtc) && count($mwtc) > 0) {
            foreach($mwtc as $m) {
                $wh_td_count[$m->warehouse->warehouses][$m->transit_days] = $m->count;
            }
        }

		$products = !isset($result_obj) ? array() : $result_obj->get()->toArray();
        $all_unit_descs = [];
        $all_item_form_descs = []; 
        foreach($products as $mp) {
            if (isset($mp['unit_description']) && $mp['unit_description'] != '' 
                && !in_array($mp['unit_description'], $all_unit_descs)) {
                array_push($all_unit_descs, $mp['unit_description']);
            }

            if (isset($mp['item_form_description']) && $mp['item_form_description'] != '' 
                && !in_array($mp['item_form_description'], $all_item_form_descs)) {
                    array_push($all_item_form_descs, $mp['item_form_description']);
            }
        }

        return view('cranium.kitpackaging.edit',compact('packagingMatirials','materialTypes','producttemp',
        'client','warehouse','packageKitComponents','type','channels','products', 'wh_td_count','pagetype',
        'all_item_form_descs', 'all_unit_descs','id'));
    }

}