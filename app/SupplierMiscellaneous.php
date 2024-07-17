<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class SupplierMiscellaneous extends Model
{
    protected $table = 'supplier_miscellaneous';
    protected $fillable = ['sync_master','ETIN'];

    public function supplier_miscellaneous_prod_desc(){
        return $this->full_product_description;
    }
    public function supplier_miscellaneous_about(){
        return $this->about_this_item;
    }

    public function supplier_miscellaneous_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->supplier_miscellaneous->brand.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }

    public function getBrand($brand_name){
        $brand = Brand::where('brand', 'like', '%'.$brand_name.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }

    public function getManufacturer($manufacturer){

        $data = Manufacturer::where('manufacturer_name', 'like', '%'.$manufacturer.'%')->first();

        if($data)
            return $data->id;
        else
            return null;
    }
    public function supplier_miscellaneous_manufacture(){
        $manufacture = Manufacturer::where('manufacturer_name', 'like', '%'.$this->supplier_miscellaneous->manufacturer.'%')->first();
        if($manufacture)
            return $manufacture->id;
        else
            return null;
    }

    public function supplier_miscellaneous_unitsize($unit_size){
        $data = UnitSize::where('unit', 'like', '%'.$unit_size.'%')->first();
        if($data)
            return $data->abbreviation;
        else
            return null;
    }

    public function supplier_miscellaneous_unit_desc($desc){
        $unit_desc = UnitDescription::where('unit_description', 'like', '%'.$desc.'%')->first();
        if($unit_desc)
            return $this->unit_description;
        else
            return null;
    }

    public function supplier_miscellaneous_pack_count(){
        return $this->pack_form_count;
    }

    public function supplier_miscellaneous_units_in_pack(){
        return $this->units_in_pack;
    }

    public function supplier_miscellaneous_category(){
        $category = Categories::where('name', 'like', '%'.$this->product_category.'%')->first();
        if(!$category)
            return $this->product_category;
        else
            return $category->id;
    }

    public function supplier_miscellaneous_subcategory1(){
        $product_subcategory1 = Subcategory::where('sub_category_1', 'like', '%'.$this->product_subcategory1.'%')->first();
        if($product_subcategory1)
            return $this->product_subcategory1->sub_category_1;
        else
            return $this->product_subcategory1;
    }

    public function supplier_miscellaneous_subcategory2(){
        $product_subcategory2 = Subcategory::where('sub_category_2', 'like', '%'.$this->product_subcategory2.'%')->first();
        if($product_subcategory2)
            return $this->product_subcategory2->sub_category_2;
        else
            return $this->product_subcategory2;
    }

    public function supplier_miscellaneous_subcategory3(){
        $product_subcategory3 = Subcategory::where('sub_category_3', 'like', '%'.$this->product_subcategory3.'%')->first();
        if($product_subcategory3)
            return $this->product_subcategory3->sub_category_3;
        else
            return $this->product_subcategory3;
    }

    public function supplier_miscellaneous_prod_attr(){
        return $this->key_product_attributes_diet;
    }
    public function supplier_miscellaneous_mfg(){
        return $this->MFG_shelf_life;
    }
    public function supplier_miscellaneous_hazardous_materials(){
        return $this->hazardous_materials;
    }
    public function supplier_miscellaneous_storage(){
        return $this->storage;
    }
    public function supplier_miscellaneous_ingredients(){
        return $this->ingredients;
    }
    public function supplier_miscellaneous_allergens(){
        return $this->allergens;
    }
    public function supplier_miscellaneous_product_temperature(){
        return $this->product_temperature;
    }
    public function supplier_miscellaneous_supplier_product_number(){
        return $this->supplier_product_number;
    }
    public function supplier_miscellaneous_manufacturer_product_number(){
        return $this->manufacturer_product_number;
    }
    public function supplier_miscellaneous_UPC(){
        return $this->UPC;
    }
    public function supplier_miscellaneous_GTIN(){
        return $this->GTIN;
    }
    public function supplier_miscellaneous_weight(){
        return $this->weight;
    }
    public function supplier_miscellaneous_length(){
        return $this->length;
    }
    public function supplier_miscellaneous_width(){
        return $this->width;
    }

    public function supplier_miscellaneous_height(){
        return $this->height;
    }
    public function supplier_miscellaneous_country_of_origin($country_of_origin){
        $data = DB::table('country_of_origin')->where('country_of_origin','like','%'.$country_of_origin.'%')->first();

        if($data){
            return $data->country_of_origin ?? '';
        }
        else{
            return '';
        }
    }



    public function supplier_miscellaneous_package_information(){
        return $this->package_information;
    }
    public function supplier_miscellaneous_supplier_status(){
        return $this->supplier_status;
    }
    public function supplier_miscellaneous_cost(){
        return $this->cost;
    }

    public function supplier_miscellaneous_new_cost(){
        return $this->new_cost;
    }

    public function supplier_miscellaneous_new_cost_date(){
        return $this->new_cost_date;
    }

    public function misc_brand($brand){
        if($brand != ''){
            $get_brand = DB::table('brand')->where('brand','LIKE','%'.$brand.'%')->first();
            if($get_brand){
                return $get_brand->brand;
            }
        }
        return NULL;
    }
    public function misc_manufacturer($manufacturer_name){
        if($manufacturer_name != ''){
            $manufacturer = DB::table('manufacturer')->where('manufacturer_name','LIKE','%'.$manufacturer_name.'%')->first();
            if($manufacturer){
                return $manufacturer->manufacturer_name;
            }
        }
        return NULL;
    }

    public function misc_category($category){
        $category = DB::table('categories')->where('name','LIKE','%'.$category.'%')->whereIN('level',[0,1,2,3])->first();
        return $category->id ?? 0;
    }

    public function misc_temperature($temperature){
        $temp = '';
        if($temperature){
            $temps = DB::table('product_temp')->where('product_temperature','LIKE','%'.$temperature.'%')->first();
            if($temps){
                $temp = $temps->product_temperature;
            }
        }
        return $temp;
    }

    public function misc_country_of_origin($country){
        $c = '';
        if($country != ''){
            $con = DB::table('country_of_origin')->where('country_of_origin','LIKE',$country)->first();
            if($con){
                $c = $con->id;
            }
        }
        return $c;
    }

    public function addMasterProduct($draf_option){
        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }

        $pro->ETIN = $pro->getETIN();
        $pro->full_product_desc = $this->full_product_description;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->misc_manufacturer($this->manufacturer);
        $pro->brand = $this->misc_brand($this->brand);
        $pro->product_category = $this->misc_category($this->product_category);
        $pro->product_subcategory1 = $this->misc_category($this->product_subcategory1);
        $pro->product_subcategory2 = $this->misc_category($this->product_subcategory2);
        $pro->product_subcategory3 = $this->misc_category($this->product_subcategory3);
        $pro->MFG_shelf_life = $this->MFG_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        $pro->allergens = $this->allergens;
        $pro->product_temperature =$this->misc_temperature($this->product_temperature);
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->UPC;
        $pro->gtin = $this->GTIN;
        $pro->weight = $this->weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin =$this->misc_country_of_origin($this->country_of_origin);
        $pro->package_information = $this->package_information;
        $pro->supplier_status = $this->supplier_status;
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->full_product_desc = $this->full_product_description;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->misc_manufacturer($this->manufacturer);
        $pro->brand = $this->misc_brand($this->brand);
        $pro->product_category = $this->misc_category($this->product_category);
        $pro->product_subcategory1 = $this->misc_category($this->product_subcategory1);
        $pro->product_subcategory2 = $this->misc_category($this->product_subcategory2);
        $pro->product_subcategory3 = $this->misc_category($this->product_subcategory3);
        $pro->MFG_shelf_life = $this->MFG_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        $pro->allergens = allergensID($this->allergens);
        $pro->product_temperature =$this->misc_temperature($this->product_temperature);
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->UPC;
        $pro->gtin = $this->GTIN;
        $pro->weight = $this->weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin =$this->misc_country_of_origin($this->country_of_origin);
        $pro->package_information = $this->package_information;
        $pro->supplier_status = $this->supplier_status;
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
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

        $pro->full_product_desc = $this->full_product_description;
        $pro->about_this_item = $this->about_this_item;
        $pro->manufacturer = $this->misc_manufacturer($this->manufacturer);

        $pro->brand = $this->misc_brand($this->brand);
        $pro->flavor = $master_pro->flavor;
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $master_pro->unit_size;
        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $master_pro->pack_form_count_product;
        $pro->product_category = $this->misc_category($this->product_category);
        $pro->product_subcategory1 = $this->misc_category($this->product_subcategory1);
        $pro->product_subcategory2 = $this->misc_category($this->product_subcategory2);
        $pro->product_subcategory3 = $this->misc_category($this->product_subcategory3);

        $pro->product_subcategory4 = $master_pro->product_subcategory4;
        $pro->product_subcategory5 = $master_pro->product_subcategory5;
        $pro->product_subcategory6 = $master_pro->product_subcategory6;
        $pro->product_subcategory7 = $master_pro->product_subcategory7;
        $pro->product_subcategory8 = $master_pro->product_subcategory8;
        $pro->product_subcategory9 = $master_pro->product_subcategory9;

        $pro->key_product_attributes_diet = $master_pro->key_product_attributes_diet;

        $pro->product_tags = $master_pro->product_tags;

        $pro->MFG_shelf_life = $this->MFG_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $this->storage;
        $pro->ingredients = $this->ingredients;
        // $pro->allergens = $this->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature =$this->misc_temperature($this->product_temperature);
        $pro->supplier_product_number = $this->supplier_product_number;
        $pro->manufacture_product_number = $this->manufacturer_product_number;
        $pro->upc = $this->UPC;
        $pro->gtin = $this->GTIN;

        $pro->GPC_code = $this->GPC_code;
        $pro->GPC_class = $this->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->weight = $this->weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin =$this->misc_country_of_origin($this->country_of_origin);
        $pro->package_information = $this->package_information;
        $pro->supplier_status = $this->supplier_status;
        $pro->cost = $this->cost;
        $pro->new_cost = $this->new_cost;
        $pro->new_cost_date = $this->new_cost_date;
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
