<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Auth;
class SupplierDryers extends Model
{
    protected $fillable = [
        'sync_master',
        'ETIN'
    ];
    protected $table = 'supplier_dryers';

    public function supplier_dryers_product_desc(){
        $desc  = $this->flavor_declaration.'-'.$this->claim_description.'-'.$this->comparative_statement.'-'.$this->disclosure_statement.'-'.$this->warning_statement;
        return $desc;
    }

    public function supplier_dryers_about(){
        return $this->ingredient_statement;
    }

    public function supplier_dryers_manufacture(){
        $manufacture = Manufacturer::where('manufacturer_name','Dryers')->first();
        if($manufacture)
            return $this->manufacture->id;
        else
            return null;
    }

    public function supplier_dryers_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->supplier_dryers->brand_name.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }

    public function supplier_dryers_brand_name($brand_val){
        $brand = Brand::where('brand', 'like', '%'.$brand_val.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }

    public function supplier_dryers_flavor(){
        return $this->fanc_name;
    }

    public function supplier_dryers_prod_type(){
        return $this->std_ID;
    }

    public function supplier_dryers_unit_size(){
        return $this->vol_fl_oz;
    }

    public function supplier_dryers_pack_from_count(){
        return $this->PCS_CS;
    }

    public function supplier_dryers_category(){
        $category = Categories::where('name', 'like', '%'.$this->std_ID.'%')->first();
        if(!$category)
            return $this->std_ID;
        else
            return $category->name;
    }

    public function supplier_dryers_subcategory1(){
        $product_subcategory1 = SubCategory::where('sub_category_1', 'like', '%'.$this->std_ID.'%')->first();
        if($product_subcategory1){
            return $product_subcategory1->sub_category_1;
        }
        else{
            return $this->std_ID;
        }

    }

    //** Need to review ** //
    public function supplier_dryers_key_prod_attr(){
        $claim_description = ProductTags::where('tag','like','%'.$this->claim_description.'%')->first();
        if($claim_description){
            return $diet_type->tag;
        }
        else{
            return $this->claim_description;
        }

    }

    public function supplier_dryers_product_tags(){
        return $this->claim_description.'-'.$this->kosher;
    }

    public function supplier_dryers_ingredients(){
        return $this->ingredient_statement;
    }

    public function supplier_dryers_allergens(){
        $no_need_to_add_array = ['No Claims', 'NA', 'No', 'TBD', 'None'];
        if(in_array($this->warning_statement, $no_need_to_add_array)){
            return null;
        }
        else{
            return $this->warning_statement;
        }

    }

    public function supplier_dryers_product_temp(){
        return 'Frozen';
    }

    public function supplier_dryers_upc(){
        return $this->UPC;
    }

    public function supplier_dryers_units_in_pack(){
        return $this->unts_cart;
    }

    public function dryers_product_type($type){
        $product_type = '';
        $getType = DB::table('product_type')->select('product_type')->where('product_type',$type)->first();
        if($getType){
            $product_type = $getType->product_type;
        }
        return $product_type;
    }


    public function addMasterProduct($draf_option){

        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }
        $pro->ETIN = $pro->getETIN();
        $pro->upc = $this->UPC;
        $pro->full_product_desc = $this->supplier_dryers_product_desc();
        $pro->about_this_item = $this->ingredient_statement;
        $pro->manufacturer = "Dryer's";
        $pro->brand = $this->supplier_dryers_brand_name($this->brand_name);
        $pro->flavor = $this->fanc_name;
        $pro->product_type = $this->dryers_product_type($this->std_ID);
        $pro->unit_size = $this->vol_fl_oz;
        $pro->pack_form_count = $this->PCS_CS;
        $pro->product_category = $this->supplier_dryers_category();
        $pro->product_subcategory1 = $this->supplier_dryers_category();
        $pro->allergens = $this->supplier_dryers_allergens();
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->upc = $this->UPC;
        $pro->full_product_desc = $this->supplier_dryers_product_desc();
        $pro->about_this_item = $this->ingredient_statement;
        $pro->manufacturer = "Dryer's";
        $pro->brand = $this->supplier_dryers_brand_name($this->brand_name);
        $pro->flavor = $this->fanc_name;
        $pro->product_type = $this->dryers_product_type($this->std_ID);
        $pro->unit_size = $this->vol_fl_oz;
        $pro->pack_form_count = $this->PCS_CS;
        $pro->product_category = $this->supplier_dryers_category();
        $pro->product_subcategory1 = $this->supplier_dryers_category();
        $pro->allergens = $this->supplier_dryers_allergens();
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
        $pro->upc = $this->UPC;
        $pro->full_product_desc = $this->supplier_dryers_product_desc();
        $pro->about_this_item = $this->ingredient_statement;
        $pro->manufacturer = "Dryer's";

        $pro->brand = $this->supplier_dryers_brand_name($this->brand_name);
        $pro->flavor = $this->fanc_name;
        $pro->product_type = $this->dryers_product_type($this->std_ID);
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $this->vol_fl_oz;
        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $this->PCS_CS;
        $pro->product_category = $this->supplier_dryers_category();
        $pro->product_subcategory1 = $this->supplier_dryers_category();
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

        $pro->MFG_shelf_life = $master_pro->mfg_shelf_life;
        $pro->hazardous_materials = $master_pro->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        $pro->allergens = $this->supplier_dryers_allergens();

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature = $master_pro->product_temperature;
        $pro->supplier_product_number = $master_pro->supplier_product_number;
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->upc = $master_pro->upc_unit;
        $pro->gtin = $master_pro->gtin_unit;

        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->weight = $master_pro->weight_lbs_unit;
        $pro->length = $master_pro->length_lbs_unit;
        $pro->width = $master_pro->width_lbs_unit;
        $pro->height = $master_pro->height_lbs_unit;
        $pro->country_of_origin = $master_pro->country_of_origin;
        $pro->package_information = $master_pro->package_information;
        $pro->supplier_status = $master_pro->supplier_status;
        $pro->cost = $master_pro->cost;
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
}
