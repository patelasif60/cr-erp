<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Auth;

class SupplierHershey extends Model
{
    protected $table = 'supplier_hersley';

    protected $fillable = [
        'sync_master',
        'ETIN'
    ];

    public function getBrand(){
        $data = DB::table('brand')->select('brand')->where('brand','LIKE','%'.$this->brand.'%')->first();
        if (!empty($data->brand)) {
            return $data->brand;
        }
        else{
            return "";
        }
    }

    public function getUnitSize(){
        $unit_size = NULL;
        if($this->pkg == "CA"){
            $desc = explode(',',$this->description);
            if(!empty($desc) && isset($desc[1])){
                $unit_size = trim($desc[1]);
            }
            else{
                $unit_size = "";
            }
            return $unit_size;
        }
        else{
            return "";
        }
    }

    public function getCategory(){
        $data = DB::table('categories')->where('name','LIKE','%'.$this->promoted_product_groups.'%')->first();
        if(!empty($data)){
            return $data->name;
        }
        else{
            return "";
        }
    }

    public function getProductTags(){
        $data = DB::table('product_tags')->where('tag','LIKE','%'.$this->promoted_product_groups.'%')->get();
        if(!empty($data)){
            $arr = [];
            foreach ($data as $key => $value) {
                $arr[$key]= $value;
            }

            if (!empty($arr)) {
                return implode(',',$arr);
            }
            else{
                return "";
            }
        }
    }

    public function gettemperature(){
        $data = DB::table('product_temp')->where('product_temperature','LIKE','%'.$this->promoted_product_groups.'%')->first();
        if(!empty($data)){
            return $data->product_temperature;
        }
        else{
            return "";
        }
    }

    public function getWeight(){
        if ($this->gross_wt_UOM == "OZ") {
            return ($this->gross_wt*0.0625);
        }
        else{
            return $this->gross_wt;
        }
    }

    public function hershey_brand($brand){
        if($brand != ''){
            $get_brand = DB::table('brand')->where('brand','LIKE','%'.$brand.'%')->first();
            if($get_brand){
                return $get_brand->brand;
            }
        }
        return NULL;
    }
    
    public function hershey_product_type($type){
        if($type != ''){
            $product_type = explode('-',$type);
            if(!empty($product_type) && isset($product_type[1])){
                $get_product_type = DB::table('product_type')->where('product_type','LIKE','%'.$product_type[1].'%')->first();
            }
            if($get_product_type){
                return $get_product_type->product_type;
            }
        }
        return NULL;
    }

    public function hershey_category($product_category){
        if($product_category != ''){
            $category = explode('-',$product_category);
            if(!empty($category) && isset($category[1])){
                $cat = DB::table('categories')->where('name','LIKE','%'.$category[1].'%')->first();
            }
            return $cat->id ?? 0;
        }
        return NULL;
    }

    public function hershey_unit_size($description,$pkg){
        $d = '';
        if($description != '' && $pkg == 'CA'){
            $desc = explode(',',$description);
            if(!empty($desc) && isset($desc[1])){
                $d = $desc[1]; 
            }
           return $d;
        }
        return NULL;
    }

    public function hershey_temp($temperature){
        $temp = '';
        if($temperature){
            $t = explode('-',$product_category);
            if(!empty($t) && isset($t[1])){
                $temps = DB::table('product_temp')->where('product_temperature','LIKE','%'.$t[1].'%')->first();
                if($temps){
                    $temp = $temps->product_temperature;
                }
            }
        }
        return $temp;
    }

    public function hershey_product_tags($tags){
        $tag_ = '';
        if($tags){
            $t = explode('-',$tags);
            if(!empty($t) && isset($t[1])){
                $tag = DB::table('product_tags')->where('tag','LIKE','%'.$t[1].'%')->first();
                if($tag){
                    $tag_ = $tag->id;
                }
            }
        }
        return $tag_;
    }

    public function hershey_upc($upc,$expanded_UPC){
        if($expanded_UPC == ''){
            return $upc;
        }else{
            return $expanded_UPC;
        }
    }

    public function hershey_weight($gross_wt,$gross_wt_uom){
        if($gross_wt == ''){
            if($gross_wt_uom == 'LB'){
                return $gross_wt;
            }
            if($gross_wt_uom == 'OZ'){
                return $gross_wt/16;
            }
            return $gross_wt;
        }
        return NULL;
    }

    public function hershey_length($dim_L_or_D,$dim_UOM){
        if($dim_L_or_D == ''){
            if($dim_UOM == 'IN'){
                return $dim_L_or_D;
            }
            if($dim_UOM == 'CM'){
                return $dim_L_or_D * 0.393701;
            }
            return $dim_L_or_D;
        }
        return NULL;
    }

    public function hershey_width($dim_L_or_D,$dim_UOM){
        if($dim_L_or_D == ''){
            if($dim_UOM == 'IN'){
                return $dim_L_or_D;
            }
            if($dim_UOM == 'CM'){
                return $dim_L_or_D * 0.393701;
            }
            return $dim_L_or_D;
        }
        return NULL;
    }

    public function hershey_height($dim_L_or_D,$dim_UOM){
        if($dim_L_or_D == ''){
            if($dim_UOM == 'IN'){
                return $dim_L_or_D;
            }
            if($dim_UOM == 'CM'){
                return $dim_L_or_D * 0.393701;
            }
            return $dim_L_or_D;
        }
        return NULL;
    }

    //PELON PELO RICO Tamarind Flavored Soft Candy, 1 oz., 24/12 ct., Display Ready Case

    public function addMasterProduct($draf_option){
        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }
        
        $pro->ETIN = $pro->getETIN();
        $pro->brand = $this->hershey_brand($this->brand);
        $pro->manufacturer = 'Hershey';
        $pro->product_type = $this->hershey_product_type($this->promoted_product_groups);
        $pro->product_tags = $this->hershey_product_tags($this->promoted_product_groups);
        $pro->unit_size = $this->hershey_unit_size($this->description,$this->pkg);
        $pro->product_category = $this->hershey_category($this->promoted_product_groups);
        $pro->product_subcategory1 = $this->hershey_category($this->promoted_product_groups);
        $pro->product_temperature = $this->hershey_temp($this->promoted_product_groups);
        $pro->supplier_product_number = $this->item_no;
        $pro->upc = $this->hershey_upc($this->UPC,$this->expanded_UPC);
        $pro->weight = $this->hershey_weight($this->gross_wt,$this->gross_wt_UOM);
        $pro->length = $this->hershey_length($this->dim_L_or_D,$this->dim_UOM);;
        $pro->width = $this->hershey_width($this->gross_wt,$this->gross_wt_UOM);
        $pro->height =$this->hershey_height($this->gross_wt,$this->gross_wt_UOM);
        $pro->cost = $this->price_sch_2_1000_5_999_lbs;
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->brand = $this->hershey_brand($this->brand);
        $pro->manufacturer = 'Hershey';
        $pro->product_type = $this->hershey_product_type($this->promoted_product_groups);
        $pro->product_tags = $this->hershey_product_tags($this->promoted_product_groups);
        $pro->unit_size = $this->hershey_unit_size($this->description,$this->pkg);
        $pro->product_category = $this->hershey_category($this->promoted_product_groups);
        $pro->product_subcategory1 = $this->hershey_category($this->promoted_product_groups);
        $pro->product_temperature = $this->hershey_temp($this->promoted_product_groups);
        $pro->supplier_product_number = $this->item_no;
        $pro->upc = $this->hershey_upc($this->UPC,$this->expanded_UPC);
        $pro->weight = $this->hershey_weight($this->gross_wt,$this->gross_wt_UOM);
        $pro->length = $this->hershey_length($this->dim_L_or_D,$this->dim_UOM);;
        $pro->width = $this->hershey_width($this->gross_wt,$this->gross_wt_UOM);
        $pro->height =$this->hershey_height($this->gross_wt,$this->gross_wt_UOM);
        $pro->cost = $this->price_sch_2_1000_5_999_lbs;
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
        $pro->brand = $this->hershey_brand($this->brand);
        $pro->manufacturer = 'Hershey';
        $pro->product_type = $this->hershey_product_type($this->promoted_product_groups);
        $pro->product_tags = $this->hershey_product_tags($this->promoted_product_groups);
        $pro->unit_size = $this->hershey_unit_size($this->description,$this->pkg);
        $pro->product_category = $this->hershey_category($this->promoted_product_groups);
        $pro->product_subcategory1 = $this->hershey_category($this->promoted_product_groups);
        $pro->product_temperature = $this->hershey_temp($this->promoted_product_groups);
        $pro->supplier_product_number = $this->item_no;
        $pro->upc = $this->hershey_upc($this->UPC,$this->expanded_UPC);
        $pro->weight = $this->hershey_weight($this->gross_wt,$this->gross_wt_UOM);
        $pro->length = $this->hershey_length($this->dim_L_or_D,$this->dim_UOM);;
        $pro->width = $this->hershey_width($this->gross_wt,$this->gross_wt_UOM);
        $pro->height =$this->hershey_height($this->gross_wt,$this->gross_wt_UOM);
        $pro->cost = $this->price_sch_2_1000_5_999_lbs;

        $pro->parent_ETIN = $master_pro->parent_ETIN;
        
        $pro->full_product_desc = $master_pro->full_product_description;
        $pro->about_this_item = $master_pro->about_this_item;

        $pro->flavor = $master_pro->flavor;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $master_pro->pack_form_count_product;
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

        $pro->MFG_shelf_life = $master_pro->MFG_shelf_life;
        $pro->hazardous_materials = $master_pro->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        // $pro->allergens = $master_pro->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->gtin = $master_pro->GTIN;

        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->country_of_origin = $master_pro->country_of_origin;
        $pro->package_information = $master_pro->package_information;
        $pro->supplier_status = $master_pro->supplier_status;
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
