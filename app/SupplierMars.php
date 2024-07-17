<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class SupplierMars extends Model
{
    protected $table = 'supplier_mars';
	protected $fillable = ['sync_master','ETIN'];

    public function supplier_mars_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->brand.'%')->first();
        if($brand)
            return $brand->brand;
        else
            return null;
    }

    public function supplier_mars_manufacture(){
        $manufacturer = Manufacturer::where('manufacturer_name','Like','Mars'.'%')->first()->toArray();
       if(!empty($manufacturer)){
        return $manufacturer['manufacturer_name'];
       }
       else{
           return "";
       }
    }

    //** Need to review ** //
    public function supplier_mars_flavor(){
        return $this->product;
    }

    public function supplier_mars_unit_size(){
        $unit_size = UnitSize::where('abbreviation', 'like', '%'.$this->unit_weight.'%')->first();
        if($unit_size)
            return $unit_size->unit_size;
        else
            return 'fl. oz.';
    }

    public function supplier_mars_unit_desc(){
        $unit_desc = UnitDescription::where('unit_description', 'like', '%'.$this->pack_type.'%')->first();
        if($unit_desc)
            return $this->pack_type;
        else
            return null;
    }

    public function supplier_mars_MFG_shelf_life(){
        return $this->weeks_best_before * 7;
    }

    public function supplier_mars_product_number(){
        return $this->ITEM_NO;
    }

    public function supplier_mars_pack_from_count(){
        return $this->units_per_case;
    }

    public function supplier_mars_unit_in_pack(){
        $packType = substr($this->pack_type,0,1);
        return $packType ?? 1;
    }

    public function supplier_mars_upc(){
        return $this->twelve_digit_unit_UPC;
    }

    public function supplier_mars_GTIN(){
        return $this->GTIN_14_digit_case_UPC;
    }

    public function supplier_mars_weight(){
        return $this->gross_case_weight;
    }

    public function supplier_mars_length(){
        return $this->outside_case_dimensions_lx;
    }

    public function supplier_mars_width(){
        return $this->outside_case_dimensions_wx;
    }

    public function supplier_mars_height(){
        return $this->outside_case_dimensions_h;
    }

    public function supplier_mars_cost(){
        return $this->PRICE_AND_WEIGHT_SCHEDULE_10_22_PALLETS;
    }

    public function supplier_mars_country_of_origin(){
        return "United States";
    }


    public function addMasterProduct($draf_option){
        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }
        $pro->ETIN = $pro->getETIN();
        $pro->upc = $this->ten_digit_unit_UPC;
        $pro->manufacturer = "Mars";
        $pro->brand = $this->supplier_mars_brand();
        $pro->flavor = $this->supplier_mars_flavor();
        $pro->unit_size = $this->unit_weight;
        $pro->unit_description = $this->supplier_mars_unit_desc();
        $pro->pack_form_count = $this->units_per_case;
        $pro->unit_in_pack = $this->supplier_mars_unit_in_pack();
        $pro->MFG_shelf_life = $this->supplier_mars_MFG_shelf_life();
        $pro->supplier_product_number = $this->ITEM_NO;
        $pro->upc = $this->twelve_digit_unit_UPC;
        $pro->gtin = $this->GTIN_14_digit_case_UPC;
        $pro->weight = $this->gross_case_weight;
        $pro->length = $this->outside_case_dimensions_lx;
        $pro->width = $this->outside_case_dimensions_wx;
        $pro->height = $this->outside_case_dimensions_h;
        $pro->cost = $this->UNIT_PRICE_LIST_10_22_PALLETS;
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->upc = $this->ten_digit_unit_UPC;
        $pro->manufacturer = "Mars";
        $pro->brand = $this->supplier_mars_brand();
        $pro->flavor = $this->supplier_mars_flavor();
        $pro->unit_size = $this->unit_weight;
        $pro->unit_description = $this->supplier_mars_unit_desc();
        $pro->pack_form_count = $this->units_per_case;
        $pro->unit_in_pack = $this->supplier_mars_unit_in_pack();
        $pro->MFG_shelf_life = $this->supplier_mars_MFG_shelf_life();
        $pro->supplier_product_number = $this->ITEM_NO;
        $pro->upc = $this->twelve_digit_unit_UPC;
        $pro->gtin = $this->GTIN_14_digit_case_UPC;
        $pro->weight = $this->gross_case_weight;
        $pro->length = $this->outside_case_dimensions_lx;
        $pro->width = $this->outside_case_dimensions_wx;
        $pro->height = $this->outside_case_dimensions_h;
        $pro->cost = $this->UNIT_PRICE_LIST_10_22_PALLETS;
        $pro->current_supplier = SupplierName($supp_id);
        $pro->queue_status = 'd';
        $pro->updated_by = Auth::user()->id;
        $pro->inserted_by = Auth::user()->id;
        $pro->updated_at = date('Y-m-d H:i:s');
        $pro->save();
        return $pro;
    }

    public function updateMasterProduct($draf_option){
        $master_pro = MasterProduct::where('upc',$this->UPC)->first();
        $check_if_que_exist = MasterProductQueue::where('upc',$this->UPC)->first();
        if($check_if_que_exist){
            $pro = $check_if_que_exist;
        }else{
            $pro = new MasterProductQueue();
        }

        $pro->ETIN = $master_pro->ETIN;

        $pro->parent_ETIN = $master_pro->parent_ETIN;
        $pro->upc = $this->ten_digit_unit_UPC;
        $pro->full_product_desc = $master_pro->full_product_desc;
        $pro->about_this_item = $master_pro->about_this_item;
        $pro->manufacturer = "Mars";

        $pro->brand = $this->supplier_mars_brand();
        $pro->flavor = $this->supplier_mars_flavor();
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $this->unit_weight;
        $pro->unit_description = $this->supplier_mars_unit_desc();
        $pro->pack_form_count = $this->units_per_case;
        $pro->product_category = $master_pro->product_category;
        $pro->product_subcategory1 = $master_pro->product_subcategory1;
        $pro->product_subcategory2 = $master_pro->product_subcategory2;
        $pro->product_subcategory3 = $master_pro->product_subcategory3;

        $pro->product_subcategory4 = $master_pro->product_subcategory4;
        $pro->product_subcategory5 = $master_pro->product_subcategory5;
        $pro->product_subcategory6 = $master_pro->product_subcategory6;
        $pro->product_subcategory7 = $master_pro->product_subcategory7;
        $pro->product_subcategory8 = $master_pro->product_subcategory8;
        $pro->product_subcategory9 = $master_pro->product_subcategory9;

        $pro->key_product_attributes_diet = $master_pro->key_product_attributes_diet;

        $pro->product_tags = $master_pro->product_tags;

        $pro->MFG_shelf_life = $this->supplier_mars_MFG_shelf_life();
        $pro->hazardous_materials = $master_pro->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        $pro->allergens = $master_pro->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature = $master_pro->product_temperature;
        $pro->supplier_product_number = $this->ITEM_NO;
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->upc = $this->twelve_digit_unit_UPC;
        $pro->gtin = $this->GTIN_14_digit_case_UPC;
        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;
        $pro->weight = $this->gross_case_weight;
        $pro->length = $this->outside_case_dimensions_lx;
        $pro->width = $this->outside_case_dimensions_wx;
        $pro->height = $this->outside_case_dimensions_h;
        $pro->country_of_origin = $master_pro->country_of_origin;
        $pro->package_information = $master_pro->package_information;
        $pro->supplier_status = $master_pro->supplier_status;
        $pro->cost = $this->UNIT_PRICE_LIST_10_22_PALLETS;
        $pro->new_cost = $master_pro->new_cost;
        $pro->new_cost_date = $master_pro->new_cost_date;
        $pro->consignment = $master_pro->consignment;
        $pro->status = $master_pro->status;
        $pro->etailer_availability = $master_pro->etailer_availability;
        $pro->dropship_available = $master_pro->dropship_available;
        $pro->channel_listing_restrictions = $master_pro->channel_listing_restrictions;
        $pro->POG_flag = $master_pro->POG_flag;
        $pro->warehouses_assigned = $master_pro->warehouses_assigned;
        $pro->status_date = $master_pro->status_date;
        $pro->lobs = $master_pro->lobs;
        $pro->current_supplier = $master_pro->current_supplier;
        $pro->alternate_ETINs = $master_pro->alternate_ETINs;
        $pro->product_listing_ETIN = $master_pro->product_listing_ETIN;
        $pro->unit_in_pack = $this->supplier_mars_unit_in_pack();
        $pro->manufacture_product_number = $master_pro->manufacture_product_number;
        $pro->supplier_product_number = $master_pro->supplier_product_number;
        $pro->total_ounces = $master_pro->total_ounces;
        $pro->is_edit = 1;
        $pro->is_approve = $master_pro->is_approve;
        $pro->approved_date = $master_pro->approved_date;
        if($draf_option == 'd'){
            $pro->queue_status = 'd';
        }else{
            $pro->queue_status = $master_pro->queue_status;
        }

        $pro->product_listing_name = $master_pro->product_listing_name;
        $pro->updated_by = Auth::user()->id;
        $pro->inserted_by = $master_pro->inserted_by;
        $pro->updated_at = date('Y-m-d H:i:s');
        $pro->save();
        DB::table('master_product')->where('id', $master_pro->id)->update([
            'product_edit_request' => 1
        ]);

        return $pro;
    }

    public function updateETIN($ETIN){
        $this->ETIN = $ETIN;
        $this->save();
    }
}
