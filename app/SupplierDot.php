<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\MasterProduct;
use App\MasterProductQueue;
use Auth;

class SupplierDot extends Model
{
    protected $fillable = [
        'ETIN',
        'etailer_stock_status',
        'list_status',
        'acquisition_cost',
        'corporate_supplier',
        'product_line',
        'brand',
        'availability',
        'dot_item',
        'manufacturer_item',
        'UPC',
        'dot_abbreviated_description',
        'item_description',
        'pack_size',
        'item_buying_group',
        'new_item',
        'proprietary',
        'nutritional_information',
        'image_available',
        'diet_type',
        'class_of_Trade',
        'temperature',
        'product_type',
        'country_of_origin',
        'IFDA_category',
        'IFDA_class',
        'GPC_code',
        'GPC_class',
        'category',
        'subcategory',
        'hazMat_item',
        'factory_direct_ship',
        'item_available_via_drop_ship',
        'mfg_shelf_life',
        'layer',
        'pallet',
        'shipping_weight',
        'product_weight',
        'shipping_each_weight',
        'product_each_weight',
        'cube',
        'width',
        'height',
        'minimum_order_quantity',
        'buy_in_multiples',
        'price_date_of_order',
        'FOB',
        'promo/allow',
        'current_my_price_date',
        'sync_master',
    ];

    protected $table = 'supplier_dot';

    public function masterProduct(){
        return $this->belongsTo('App\MasterProduct', 'ETIN', 'ETIN');
    }

    public function corporate_supplier(){
        return $this->masterProduct->manufacturer;
    }

    public function brand(){
        return $this->masterProduct->brand;
    }

    public function pack_size(){
        return $this->masterProduct->pack_form_count;
    }

    public function ifda_class(){
        return $this->masterProduct->product_category;
    }

    public function diat_type(){
        return $this->masterProduct->product_tags;
    }

    public function mfg_self_life(){
        return $this->masterProduct->MFG_shelf_life;
    }

    public function hazMat_item(){
        return $this->masterProduct->hazardous_materials;
    }

    public function temperature(){
        return $this->masterProduct->product_temperature;
    }

    public function dot_item(){
        return $this->masterProduct->supplier_product_number;
    }

    public function manufacturer_item(){
        return $this->masterProduct->supplier_product_number;
    }
//1-20 POUND
    public function dot_pack_size($pack_size){
        $abbr = '';
        $row1 = '';
        if($pack_size != ''){
            $row = explode('-',$pack_size);
            if(!empty($row)){
                if(isset($row[1])){
                    $explode = explode(' ',$row[1]);
                    if(!empty($explode) && isset($explode[1])){
                        $row1 = ucfirst(strtolower($explode[1]));
                    }
                }
                if($row1 != ''){
                   $unit = DB::table('unit_sizes')->where('unit','LIKE',$row1)->select('abbreviation')->first();
                   if($unit){
                     $abbr = $unit->abbreviation;
                   }
                }
            }
        }
        return $abbr;
    }

    public function dot_category($category){
        $category = DB::table('categories')->where('name','LIKE','%'.$category.'%')->whereIN('level',[0,1])->first();
        return $category->id ?? 0;
    }

    public function getEtailerAvailability(){
        $availability_id = DB::table('etailer_availability')->select('id')->where('etailer_availability','LIKE','%'.$this->availability.'%')->first();
        if ($availability_id){
            return $availability_id->id;
        }
        else{
            return false;
        }
    }

    public function dot_diet_type($diet_types){
        $tags_array = [];
        $tags_str = '';
        if($diet_types != ''){
            $diet_array = explode('; ',$diet_types);
            if($diet_array){
                foreach($diet_array as $diet){
                    $tag = DB::table('product_tags')->where('tag','LIKE','%'.$diet.'%')->first();
                    if($tag){
                        array_push($tags_array,$tag->id);
                    }
                }
            }
        }

        if(!empty($tags_array)){
            $tags_str = implode(',',$tags_array);
        }
        return $tags_str;
    }

    public function dot_temperature($temperature){
        $temp = '';
        if($temperature){
            $t = explode(' GOODS',$temperature);
            if(!empty($t) && isset($t[0])){
                $temps = DB::table('product_temp')->where('product_temperature','LIKE','%'.$t[0].'%')->first();
                if($temps){
                    $temp = $temps->product_temperature;
                }
            }
        }
        return $temp;
    }

    public function dot_country_of_origin($country){
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
        $pro->manufacturer = $this->corporate_supplier;
        $pro->brand = $this->brand;
        $pro->unit_size = $this->dot_pack_size($this->pack_size);
        $pro->unit_in_pack = explode('-',$this->units_in_pack)[0];
        $pro->product_category = $this->dot_category($this->IFDA_category);
        $pro->product_subcategory1 = $this->dot_category($this->IFDA_class);
        $pro->product_tags = $this->dot_diet_type($this->diet_type);
        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazMat_item;
        $pro->product_temperature = $this->dot_temperature($this->temperature);
        $pro->upc = $this->UPC;
        $pro->GPC_code = $this->GPC_code;
        $pro->GPC_class = $this->GPC_class;
        $pro->weight = $this->product_weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin = $this->dot_country_of_origin($this->country_of_origin);
        $pro->supplier_status = $this->availability;
        $pro->cost = $this->my_case_10000;
        $pro->new_cost = $this->my_case_5000;
        $pro->new_cost_date = $this->f_my_each_pricing_date;
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->supplier_product_number = $this->dot_item;
        $pro->manufacturer = $this->corporate_supplier;
        $pro->brand = $this->brand;
        $pro->unit_size = $this->dot_pack_size($this->pack_size);
        $pro->unit_in_pack = isset(explode('-',$this->units_in_pack)[0]) ? explode('-',$this->units_in_pack)[0] : '';
        $pro->product_category = $this->dot_category($this->IFDA_category);
        $pro->product_subcategory1 = $this->dot_category($this->IFDA_class);
        $pro->product_tags = $this->dot_diet_type($this->diet_type);
        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazMat_item;
        $pro->product_temperature = $this->dot_temperature($this->temperature);
        $pro->upc = $this->UPC;
        $pro->GPC_code = $this->GPC_code;
        $pro->GPC_class = $this->GPC_class;
        $pro->weight = $this->product_weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin = $this->dot_country_of_origin($this->country_of_origin);
        $pro->supplier_status = SupplierStatusID($this->availability);
        $pro->cost = $this->my_case_10000;
        $pro->new_cost = $this->my_case_5000;
        $pro->new_cost_date = $this->f_my_each_pricing_date;
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

        $pro->full_product_desc = $master_pro->full_product_desc;
        $pro->about_this_item = $master_pro->about_this_item;
        $pro->manufacturer = $this->corporate_supplier;

        $pro->brand = $this->brand;
        $pro->flavor = $master_pro->flavor;
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;


        $pro->unit_size = $this->dot_pack_size($this->pack_size);
        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $master_pro->pack_form_count_product;
        $pro->product_category = $this->category;
        $pro->product_subcategory1 = $this->product_subcategory1;
        $pro->product_subcategory2 = $master_pro->product_subcategory2;
        $pro->product_subcategory3 = $master_pro->product_subcategory3;

        $pro->product_subcategory4 = $master_pro->product_subcategory4;
        $pro->product_subcategory5 = $master_pro->product_subcategory5;
        $pro->product_subcategory6 = $master_pro->product_subcategory6;
        $pro->product_subcategory7 = $master_pro->product_subcategory7;
        $pro->product_subcategory8 = $master_pro->product_subcategory8;
        $pro->product_subcategory9 = $master_pro->product_subcategory9;

        $pro->key_product_attributes_diet = $master_pro->key_product_attributes_diet;

        $pro->product_tags = $this->dot_diet_type($this->diet_type);

        $pro->MFG_shelf_life = $this->mfg_shelf_life;
        $pro->hazardous_materials = $this->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        $pro->allergens = $master_pro->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature =$this->dot_temperature($this->temperature);
        $pro->supplier_product_number = $master_pro->supplier_product_number;
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->upc = $this->UPC;
        $pro->gtin = $master_pro->gtin_unit;

        $pro->GPC_code = $this->GPC_code;
        $pro->GPC_class = $this->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->weight = $this->product_weight;
        $pro->length = $this->length;
        $pro->width = $this->width;
        $pro->height = $this->height;
        $pro->country_of_origin =$this->dot_country_of_origin($this->country_of_origin);
        $pro->package_information = $master_pro->package_information;
        $pro->supplier_status = $this->availability;
        $pro->cost = $this->my_case_10000;
        $pro->new_cost = $this->my_case_5000;
        $pro->new_cost_date = $this->f_my_each_pricing_date;
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
        $pro->unit_in_pack = explode('-',$this->units_in_pack)[0];
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
