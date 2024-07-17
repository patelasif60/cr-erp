<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
class SupplierNestle extends Model
{
    protected $table = 'supplier_nestle';
    protected $fillable = ['sync_master','ETIN'];
    public function supplier_nestle_manufacture(){
        return "Nestle";
    }

    public function supplier_nestle_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->brand.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }

    public function getSupplierBrand($desc){
        if(isset($desc)){
            $raw_desc = explode('-',$desc);
        }
        if(isset($raw_desc[0])){
            return $raw_desc[0];
        }
        else{
            return '';
        }
    }

    // public function getSupplierBrand($desc){
    //     if(isset($desc)){
    //         $raw_desc = explode('-',$desc);
    //     }
    //     if(isset($raw_desc[0])){
    //         return $raw_desc[0];
    //     }
    //     else{
    //         return '';
    //     }
    // }

    public function supplier_nestle_unit_size(){
        $unit_size = UnitSize::all();
        $desc = $this->description;
        $raw_desc = [];

        $desc_arr = explode('-', $desc);
        $desc_unit_size = $desc_arr[count($desc_arr)-1];

        if($desc_unit_size != ''){
            $raw_desc = explode('X',$desc_unit_size);
        }
        if(!empty($raw_desc) && isset($raw_desc[1])){
            return $raw_desc[1];
        }
        else{
            return null;
        }

        // if(in_array($desc_unit_size,$unit_size))
        //     return $desc_unit_size;
        // else
        //     return null;
    }

    public function getPackFormCount($desc){
        $desc_arr = explode('-', $desc);
        $desc_unit_size = $desc_arr[count($desc_arr)-1];

        if($desc_unit_size != ''){
            $raw_desc = explode('X',$desc_unit_size);
        }
        if(isset($raw_desc[0])){
            return $raw_desc[0];
        }
        else{
            return null;
        }
    }
    public function supplier_nestle_pack_from_count(){
        //sunny need to add
    }
    public function supplier_nestle_units_in_pack(){
        return $this->units_in_pack;
    }
    public function supplier_nestle_MGF_shelf_life(){
        return $this->ttl_shelf_life;
    }
    public function supplier_nestle_prod_temp(){
        return 'Frozen';
    }
    public function supplier_nestle_prod_number(){
        return $this->material_number;
    }
    public function supplier_nestle_GTIN(){
        return $this->consumer_unit_code; //review sunny
    }
    public function supplier_nestle_weight(){
        return $this->gross_wt_order_unit_specs;
    }
    public function supplier_nestle_length(){
        return $this->length_order_unit_specs;
    }
    public function supplier_nestle_width(){
        return $this->Width_order_unit_specs;
    }
    public function supplier_nestle_height(){
        return $this->height_order_unit_specs;
    }
    public function supplier_nestle_country_of_origin(){
        return $this->country_of_origin;
    }
    public function supplier_nestle_cost(){
        return $this->PLA_B3_2000_4999;
    }
    public function supplier_nestle_new_cost(){
        return $this->PLA_B3_2000_4999_new_price;
    }
    public function supplier_nestle_new_cost_date(){
        return $this->new_cost_date;
    }

    public function addMasterProduct($draf_option){
        // dd($this->pack_size);
        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }

        $pro->ETIN = $pro->getETIN();
        $pro->manufacturer = "Nestle";
        $pro->brand = $this->getSupplierBrand($this->description);
        $pro->unit_size = $this->supplier_nestle_unit_size();
        $pro->pack_form_count = $this->getPackFormCount($this->description);
        $pro->unit_in_pack = $this->pack_size;
        $pro->MFG_shelf_life = $this->ttl_shelf_life;
        $pro->product_temperature = "Frozen";
        $pro->supplier_product_number = $this->material_number;
        $pro->weight = $this->gross_wt_order_unit_specs;
        $pro->length = $this->length_order_unit_specs;
        $pro->width = $this->Width_order_unit_specs;
        $pro->height = $this->height_order_unit_specs;
        $pro->country_of_origin = CategoryID($this->country_of_origin);
        $pro->cost = $this->PLA_B3_2000_4999_new_price;
        $pro->new_cost_date = $this->new_price_date;
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->manufacturer = "Nestle";
        $pro->brand = $this->getSupplierBrand($this->description);
        $pro->unit_size = $this->supplier_nestle_unit_size();
        $pro->pack_form_count = $this->getPackFormCount($this->description);
        $pro->unit_in_pack = $this->pack_size;
        $pro->MFG_shelf_life = $this->ttl_shelf_life;
        $pro->product_temperature = "Frozen";
        $pro->supplier_product_number = $this->material_number;
        $pro->weight = $this->gross_wt_order_unit_specs;
        $pro->length = $this->length_order_unit_specs;
        $pro->width = $this->Width_order_unit_specs;
        $pro->height = $this->height_order_unit_specs;
        $pro->country_of_origin = $this->country_of_origin;
        $pro->cost = $this->PLA_B3_2000_4999_new_price;
        $pro->new_cost_date = $this->new_price_date;
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
        $pro->manufacturer = "Nestle";

        $pro->brand = $this->getSupplierBrand($this->description);
        $pro->flavor = $master_pro->flavor;
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $this->supplier_nestle_unit_size();
        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $this->getPackFormCount($this->description);
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

        $pro->MFG_shelf_life = $this->ttl_shelf_life;
        $pro->hazardous_materials = $master_pro->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        $pro->allergens = $master_pro->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature = "Frozen";
        $pro->supplier_product_number = $this->material_number;
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->upc = $this->twelve_digit_unit_UPC;
        $pro->gtin = $this->GTIN_14_digit_case_UPC;
        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;
        $pro->weight = $this->gross_wt_order_unit_specs;
        $pro->length = $this->length_order_unit_specs;
        $pro->width = $this->Width_order_unit_specs;
        $pro->height = $this->height_order_unit_specs;
        $pro->country_of_origin = $this->country_of_origin;
        $pro->cost = $this->PLA_B3_2000_4999_new_price;
        $pro->new_cost_date = $this->new_price_date;
        $pro->package_information = $master_pro->package_information;
        $pro->supplier_status = $master_pro->supplier_status;
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
        $pro->unit_in_pack = $this->pack_size;
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
}
