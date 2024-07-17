<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;
class ThreeplClientProduct extends Model
{
    protected $table = "3pl_client_product";

    public function master_product(){
        return $this->belongsTo('App\MasterProduct', 'upc', 'upc_case');
    }

    public function addMasterProduct(){

        $pro = new MasterProduct();
        $pro->ETIN = $pro->getETIN();
        $pro->full_product_desc = $this->product_description;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->manufacturer;
        $pro->unit_size = $this->unit_size;
        $pro->brand = $this->brand;
        $pro->unit_description = $this->unit_description;
        $pro->pack_form_count = $this->pack_form_count_product;
        $pro->product_category = CategoryID($this->category);
        $pro->product_subcategory1 = CategoryID($this->product_subcategory1);
        $pro->product_subcategory2 = CategoryID($this->product_subcategory2);
        $pro->product_subcategory3 = CategoryID($this->product_subcategory3);
        $pro->key_product_attributes_diet = $this->key_product_attributes_diet;
        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        $pro->allergens = allergensID($this->allergens);
        $pro->product_temperature = $this->product_temperature;
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->upc_case;
        $pro->gtin = $this->gtin_unit;
        $pro->weight = $this->weight_lbs_unit;
        $pro->length = $this->length_lbs_unit;
        $pro->width = $this->width_lbs_unit;
        $pro->height = $this->height_lbs_unit;
        $pro->country_of_origin = countryID($this->country_of_origin);
        $pro->package_information = $this->package_information;
        $pro->supplier_status = SupplierStatusID($this->supplier_status);
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
        $pro->consignment = $this->consignment;
        $pro->lobs = $this->client_id;
        $pro->updated_by = Auth::user()->id;
        $pro->inserted_by = Auth::user()->id;
        $pro->updated_at = date('Y-m-d H:i:s');
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct(){
        $MasterProduct = new MasterProduct();

        $pro = new MasterProductQueue();
        $pro->ETIN = $MasterProduct->getETIN();
        $pro->full_product_desc = $this->product_full_prod_desc;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->manufacturer;
        $pro->brand = $this->brand;
        $pro->unit_size = $this->unit_size;
        $pro->unit_description = $this->unit_description;
        $pro->pack_form_count = $this->pack_form_count_product;
        $pro->product_category = CategoryID($this->category);
        $pro->product_subcategory1 = CategoryID($this->product_subcategory1);
        $pro->product_subcategory2 = CategoryID($this->product_subcategory2);
        $pro->product_subcategory3 = CategoryID($this->product_subcategory3);
        $pro->etailer_availability = CategoryID($this->product_etailer_stock_status);
        $pro->lobs = $this->client_id;
        $pro->key_product_attributes_diet = $this->key_product_attributes_diet;

        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        $pro->allergens = allergensID($this->allergens);
        $pro->product_temperature = $this->product_temperature;
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->upc_case;
        $pro->gtin = $this->gtin_unit;


        $pro->weight = $this->weight_lbs_unit;
        $pro->length = $this->length_lbs_unit;
        $pro->width = $this->width_lbs_unit;
        $pro->height = $this->height_lbs_unit;
        $pro->country_of_origin = countryID($this->country_of_origin);
        $pro->package_information = $this->package_information;
        $pro->supplier_status = SupplierStatusID($this->supplier_status);
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
        $pro->consignment = $this->consignment;
        $pro->queue_status = 'd';
        $pro->updated_by = Auth::user()->id;
        $pro->inserted_by = Auth::user()->id;
        $pro->updated_at = date('Y-m-d H:i:s');
        $pro->save();
        return $pro;
    }

    public function updateMasterProduct($draf_option){

        $master_pro = MasterProduct::where('upc',$this->upc_unit)->first();
        $check_if_que_exist = MasterProductQueue::where('upc',$this->upc_unit)->first();
        if($check_if_que_exist){
            $pro = $check_if_que_exist;
        }else{
            $pro = new MasterProductQueue();
        }


        $pro->ETIN = $master_pro->ETIN;

        $pro->parent_ETIN = $master_pro->parent_ETIN;

        $pro->full_product_desc = $this->product_full_prod_desc;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->manufacturer;

        $pro->brand = $master_pro->brand;
        $pro->flavor = $master_pro->flavor;
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $this->unit_size;
        $pro->unit_description = $this->unit_description;
        $pro->pack_form_count = $this->pack_form_count_product;
        $pro->product_category = $this->category;
        $pro->product_subcategory1 = $this->product_subcategory1;
        $pro->product_subcategory2 = $this->product_subcategory2;
        $pro->product_subcategory3 = $this->product_subcategory3;

        $pro->product_subcategory4 = $master_pro->product_subcategory4;
        $pro->product_subcategory5 = $master_pro->product_subcategory5;
        $pro->product_subcategory6 = $master_pro->product_subcategory6;
        $pro->product_subcategory7 = $master_pro->product_subcategory7;
        $pro->product_subcategory8 = $master_pro->product_subcategory8;
        $pro->product_subcategory9 = $master_pro->product_subcategory9;

        $pro->key_product_attributes_diet = $this->key_product_attributes_diet;

        $pro->product_tags = $master_pro->product_tags;

        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        $pro->allergens = $this->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature = $this->product_temperature;
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->upc_case;
        $pro->gtin = $this->gtin_unit;

        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->weight = $this->weight_lbs_unit;
        $pro->length = $this->length_lbs_unit;
        $pro->width = $this->width_lbs_unit;
        $pro->height = $this->height_lbs_unit;
        $pro->country_of_origin = $this->country_of_origin;
        $pro->package_information = $this->package_information;
        $pro->supplier_status = $this->supplier_status;
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
        $pro->consignment = $this->consignment;


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
        $pro->unit_in_pack = $master_pro->unit_in_pack;
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

    public function AddthreePLProduct($row){

    }

   
    
    
}
