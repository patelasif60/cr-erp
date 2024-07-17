<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class SupplierKehe extends Model
{
    protected $table = 'supplier_kehe';
	protected $fillable = ['sync_master','ETIN'];

    public function supplier_kehe_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->BRAND.'%')->first();
        if($brand)
            return $brand->brand;
        else
            //return null;

		//$lastrecord = Brand::orderBy('id', 'desc')->first();
		//$newmanufacture_id = ($lastrecord->id + 1);
		$insertbrand = new Brand;
		$insertbrand->brand = $this->BRAND;
		//$insertbrand->manufacturer_id = $newmanufacture_id;
		$insertbrand->save();

		return $this->BRAND;
    }

    public function supplier_kehe_unit_size(){
        $unit_size = UnitSize::where('abbreviation', 'like', '%'.strtolower($this->UOM).'%')->first();
        if($unit_size){
            return $this->SIZE.'-'.$unit_size->abbreviation;
        }
        else{
            return $this->SIZE;
        }
    }

    public function supplier_kehe_pack_from_count(){
        return $this->PACK;
    }

    public function supplier_kehe_category(){
        $category = Categories::where('name', 'like', '%'.$this->CATEGORY.'%')->first();
        if(!$category)
            return $this->CATEGORY;
        else
            return $category->name;
    }

    public function supplier_kehe_subcategory(){
        //no subcategory field in Kehe
    }

    public function supplier_kehe_supplier_prod_number(){
        return $this->item_number;
    }

    public function supplier_kehe_UPC(){
        return $this->UPC;
    }

    public function supplier_kehe_status(){
        //ITEM STATUS not in the db fieldlist
    }

    public function supplier_kehe_cost(){
        //CASE PRICE not in the db fieldlist
    }

    public function kehe_brand($brand){
        if($brand != ''){
            $get_brand = DB::table('brand')->where('brand','LIKE','%'.$brand.'%')->first();
            if($get_brand){
                return $get_brand->brand;
            }
        }
        return NULL;
    }

    public function kehe_unit_size($size,$uom){
        return $size.'-'.$uom;
    }

    public function kehe_pack_form_count($cp,$qty){
        if($qty != 0){
            return $cp/$qty;
        }
        return $cp;
    }

    public function kehe_category($category){
        $category = DB::table('categories')->where('name','LIKE','%'.$category.'%')->whereIN('level',[0,1,2])->first();
        return $category->id ?? 0;
    }

    public function addMasterProduct($draf_option){
        if($draf_option == ""){
            $pro = new MasterProduct();
        }else{
            $pro = new MasterProductQueue();
            $pro->queue_status = 'd';
        }
        $pro->ETIN = $pro->getETIN();
        $pro->brand = $this->kehe_brand($this->BRAND);
        $pro->unit_size = $this->kehe_unit_size($this->SIZE,$this->UOM);
        $pro->pack_form_count = $this->kehe_pack_form_count($this->CASEPACK,$this->QUANTITY);
        $pro->upc = $this->UPC;
        $pro->unit_in_pack = $this->QUANTITY;
        $pro->product_category = $this->kehe_category($this->CATEGORY);
        $pro->save();
        return $pro;
    }

    public function DraftMasterProduct($supp_id){
        $MasterProduct = new MasterProduct();
        $pro = new MasterProductQueue();

        $pro->ETIN = $MasterProduct->getETIN();
        $pro->supplier_product_number = $this->item_number;
        $pro->brand = $this->kehe_brand($this->BRAND);
        $pro->unit_size = $this->kehe_unit_size($this->SIZE,$this->UOM);
        $pro->pack_form_count = $this->kehe_pack_form_count($this->CASEPACK,$this->QUANTITY);
        $pro->upc = $this->UPC;
        $pro->unit_in_pack = $this->QUANTITY;
        $pro->product_category = $this->kehe_category($this->CATEGORY);
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
        $pro->manufacturer = $master_pro->manufacturer;

        $pro->brand = $this->kehe_brand($this->BRAND);
        $pro->flavor = $master_pro->flavor;
        $pro->product_type = $master_pro->product_type;
        $pro->item_form_description = $master_pro->item_form_description;
        $pro->total_ounces = $master_pro->total_ounces;
        $pro->unit_in_pack = $this->QUANTITY;


        $pro->unit_size = $this->kehe_unit_size($this->SIZE,$this->UOM);
        $pro->unit_description = $master_pro->unit_description;
        $pro->pack_form_count = $this->kehe_pack_form_count($this->CASEPACK,$this->QUANTITY);
        $pro->product_category = $this->kehe_category($this->CATEGORY);
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

        $pro->MFG_shelf_life = $master_pro->mfg_shelf_life;
        $pro->hazardous_materials = $master_pro->hazardous_materials;
        $pro->storage = $master_pro->storage;
        $pro->ingredients = $master_pro->ingredients;
        $pro->allergens = $master_pro->allergens;

        $pro->prop_65_flag = $master_pro->prop_65_flag;
        $pro->prop_65_ingredient = $master_pro->prop_65_ingredient;

        $pro->product_temperature = $master_pro->product_temperature;
        $pro->supplier_product_number = $master_pro->supplier_product_number;
        $pro->manufacture_product_number = $master_pro->manufacturer_product_number;
        $pro->upc = $this->UPC;
        $pro->gtin = $master_pro->gtin;

        $pro->GPC_code = $master_pro->GPC_code;
        $pro->GPC_class = $master_pro->GPC_class;
        $pro->HS_code = $master_pro->HS_code;

        $pro->weight = $master_pro->weight;
        $pro->length = $master_pro->length;
        $pro->width = $master_pro->width;
        $pro->height = $master_pro->height;
        $pro->country_of_origin =$master_pro->country_of_origin;
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
