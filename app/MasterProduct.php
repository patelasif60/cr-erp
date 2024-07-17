<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\ImageType;
use App\LastETIN;
use App\ProductCategory;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ApproveRejectProductNotification;
use App\User;
use App\ClientChannelConfiguration;
use Auth;
use App\Allergens;
use App\ProductSubcategory;
use App\purchasing_details;
class MasterProduct extends Model
{
    protected $table = 'master_product';

    public function users(){
        return $this->belongsTo(User::class,'updated_by','id');
    }

    public function lobs(){
        // dd($this->all());
        // return $this->belongsTo(Client::class,'lobs','id')->select('clients.company_name');
    }

    public function supplier_dot(){
        return $this->belongsTo('App\SupplierDot', 'ETIN', 'ETIN');
    }
    public function supplier_dryers(){
        return $this->belongsTo('App\SupplierDryers', 'ETIN', 'ETIN');
    }
    public function supplier_kehe(){
        return $this->belongsTo('App\SupplierKehe', 'ETIN', 'ETIN');
    }
    public function supplier_mars(){
        return $this->belongsTo('App\SupplierMars', 'ETIN', 'ETIN');
    }
    public function supplier_miscellaneous(){
        return $this->belongsTo('App\SupplierMiscellaneous', 'ETIN', 'ETIN');
    }
    public function supplier_3pl(){
        return $this->belongsTo('App\ThreeplClientProduct', 'ETIN', 'ETIN');
    }
    public function supplier_nestle(){
        return $this->belongsTo('App\SupplierNestle', 'ETIN', 'ETIN');
    }
    public function masterShelf(){
        return $this->hasMany('App\MasterShelf', 'ETIN', 'ETIN');
    }

    //Create Product description
    public function product_description(){
        $prod_desc = $this->brand.' '.$this->flavor.' '.$this->product_type.', '.$this->unit_size.' '.$this->unit_description.' ('.$this->pack_form_count.'-'.$this->unit_in_pack.' '.$this->item_form_description.')';
        return $prod_desc;
    }

    //
    // Map with Supplier Dot ***
    //

    public function sp_dot_corporate_supplier(){
        $manufacture = Manufacturer::where('manufacturer_name', 'like', '%'.$this->supplier_dot->corporate_supplier.'%')->first();
        if($manufacture){
            return $this->manufacture->manufacturer_name;
        }
        else{
            return $this->supplier_dot->corporate_supplier;
        }

    }

    public function sp_dot_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->supplier_dot->brand.'%')->first();
        if($brand)
            return $brand->brand;
        else
            return $this->supplier_dot->brand;
    }

    public function sp_dot_unit_size(){
        $pack_size = $this->supplier_dot->pack_size;
        $arr_pack_size = explode('-', $pack_size);
        if(isset($arr_pack_size[1]))
        {
            return $arr_pack_size[1];
        }else{
            return NULL;
        }

    }

    public function sp_dot_pack_from_count(){
        $pack_size = $this->supplier_dot->pack_size;
        $arr_pack_size = explode('-', $pack_size);
        return $arr_pack_size[0];
    }

    public function sp_dot_category(){
        $category = Categories::where('name', 'like', '%'.$this->supplier_dot->IFDA_category.'%')->first();
        if(!$category)
            return $this->supplier_dot->IFDA_category;
        else
            return $category->name;
    }

    public function sp_dot_subcategory1(){
        $product_subcategory1 = Subcategory::where('sub_category_1', 'like', '%'.$this->supplier_dot->IFDA_class.'%')->first();
        if($product_subcategory1)
            return $this->product_subcategory1->sub_category_1;
        else
            return $this->supplier_dot->IFDA_class;
    }

    public function sp_dot_tags(){
        $diet_type = ProductTags::where('tag','like','%'.$this->supplier_dot->diet_type.'%')->first();
        if($diet_type){
            return $diet_type->tag;
        }
        else{
            return $this->supplier_dot->diet_type;
        }
    }

    public function sp_dot_MGF_Shelf_life(){
        return $this->supplier_dot->mfg_shelf_life;
    }

    public function sp_dot_hazMat_item(){
        return $this->supplier_dot->hazMat_item;
    }

    public function sp_dot_prod_temp(){
        $temp = ProductTemperature::where('product_temperature','like','%'.$this->supplier_dot->temperature.'%')->first();
        if ($temp) {
            return $temp->product_temperature;
        }
        else{
            return $this->supplier_dot->temperature;
        }
    }

    public function sp_dot_supplier_product_number(){
        return $this->supplier_dot->dot_item;
    }

    public function sp_dot_manufacture_product_number(){
        return $this->supplier_dot->manufacturer_item;
    }

    public function sp_dot_gtin(){
        return $this->supplier_dot->gtin;
    }

    public function sp_dot_gpc_code(){
        return $this->supplier_dot->GPC_code;
    }

    public function sp_dot_gpc_class(){
        return $this->supplier_dot->GPC_class;
    }

    public function sp_dot_weight(){
        return $this->supplier_dot->product_weight;
    }

    public function sp_dot_length(){
        return $this->supplier_dot->length;
    }

    public function sp_dot_width(){
        return $this->supplier_dot->width;
    }

    public function sp_dot_height(){
        return $this->supplier_dot->height;
    }

    public function sp_dot_country_of_origin(){
        return $this->supplier_dot->country_of_origin;
    }

    public function sp_dot_dropship_available(){
        //sunny needs to add
    }

    public function sp_dot_supplier_status(){
        return $this->supplier_dot->availability;
    }

    public function sp_dot_cost(){
        return $this->supplier_dot->my_case_5000;
    }

    public function sp_dot_new_cost(){
        return $this->supplier_dot->future_my_case_5000;
    }

    public function sp_dot_new_cost_date(){
        return $this->supplier_dot->f_my_each_pricing_date;
    }

    //
    // 3pl Client Product Mapping ***
    //

    public function supplier_3pl_full_prod_desc(){
        return $this->supplier_3pl->full_product_description;
    }
    public function supplier_3pl_about_this_item(){
        return $this->supplier_3pl->about_this_item;
    }
    public function supplier_3pl_manufacturer(){
        $manufacture = Manufacturer::where('manufacturer_name', 'like', '%'.$this->supplier_3pl->manufacturer.'%')->first();

        if($manufacture)
            return $manufacture->id;
        else
            return null;
    }
    public function supplier_3pl_brand(){
        $brand = Brand::where('brand', 'like', '%'.$this->supplier_3pl->brand.'%')->first();
        if($brand)
            return $brand->id;
        else
            return null;
    }
    public function supplier_3pl_unit_size(){
        $unit_size = UnitSize::where('unit_size', 'like', '%'.$this->supplier_3pl->unit_size.'%')->first();
        if($unit_size)
            return $unit_size->unit_size;
        else
            return null;
    }
    public function supplier_3pl_unit_desc(){
        $unit_desc = UnitDescription::where('unit_description', 'like', '%'.$this->supplier_3pl->unit_description.'%')->first();
        if($unit_desc)
            return $this->supplier_3pl->unit_description;
        else
            return null;
    }
    public function supplier_3pl_pack_form_count(){
        return $this->supplier_3pl->pack_form_count_product;
    }
    public function supplier_3pl_category(){
        $category = Categories::where('name', 'like', '%'.$this->supplier_3pl->name.'%')->first();
        if(!$category)
            return $this->supplier_3pl->product_category;
        else
            return $category->name;
    }
    public function supplier_3pl_subcategory1(){
        $product_subcategory1 = Subcategory::where('sub_category_1', 'like', '%'.$this->supplier_3pl->product_subcategory1.'%')->first();
        if($product_subcategory1)
            return $this->product_subcategory1->sub_category_1;
        else
            return $this->supplier_3pl->product_subcategory1;
    }
    public function supplier_3pl_subcategory2(){
        $product_subcategory2 = Subcategory::where('sub_category_2', 'like', '%'.$this->supplier_3pl->product_subcategory2.'%')->first();
        if($product_subcategory2)
            return $this->product_subcategory2->sub_category_2;
        else
            return $this->supplier_3pl->product_subcategory2;
    }
    public function supplier_3pl_subcategory3(){
        $product_subcategory3 = Subcategory::where('sub_category_3', 'like', '%'.$this->supplier_3pl->product_subcategory3.'%')->first();
        if($product_subcategory3)
            return $this->product_subcategory3->sub_category_3;
        else
            return $this->supplier_3pl->product_subcategory3;
    }
    public function supplier_3pl_units_in_pack(){
        return $this->supplier_3pl->units_in_pack;
    }
    public function supplier_3pl_key_product_attributes_diet(){
        return $this->supplier_3pl->key_product_attributes_diet;
    }
    public function supplier_3pl_mfg_shelf_life(){
        return $this->supplier_3pl->mfg_shelf_life;
    }
    public function supplier_3pl_hazardous_materials(){
        return $this->supplier_3pl->hazardous_materials;
    }
    public function supplier_3pl_storage(){
        return $this->supplier_3pl->storage;
    }
    public function supplier_3pl_ingredients(){
        return $this->supplier_3pl->ingredients;
    }
    public function supplier_3pl_allergens(){
        return $this->supplier_3pl->allergens;
    }
    public function supplier_3pl_product_temperature(){
        return $this->supplier_3pl->product_temperature;
    }
    public function supplier_3pl_product_number(){
        return $this->supplier_3pl->supplier_product_number;
    }
    public function supplier_3pl_manufacturer_product_number(){
        return $this->supplier_3pl->manufacturer_product_number;
    }
    public function supplier_3pl_upc_case(){
        return $this->supplier_3pl->upc_case;
    }
    public function supplier_3pl_gtin_case(){
        return $this->supplier_3pl->gtin_case;
    }
    public function supplier_3pl_weight_in_case(){
        return $this->supplier_3pl->weight_in_case;
    }
    public function supplier_3pl_length_in_case(){
        return $this->supplier_3pl->length_in_case;
    }
    public function supplier_3pl_width_in_case(){
        return $this->supplier_3pl->width_in_case;
    }
    public function supplier_3pl_height_in_case(){
        return $this->supplier_3pl->height_in_case;
    }
    public function supplier_3pl_country_of_origin(){
        return $this->supplier_3pl->country_of_origin;
    }
    public function supplier_3pl_package_information(){
        return $this->supplier_3pl->package_information;
    }
    public function supplier_3pl_consignment_restrictions(){
        return $this->supplier_3pl->consignment_restrictions;
    }
    public function supplier_3pl_supplier_status(){
        return $this->supplier_3pl->supplier_status;
    }
    public function supplier_3pl_cost(){
        return $this->supplier_3pl->cost;
    }
    public function supplier_3pl_new_cost(){
        return $this->supplier_3pl->new_cost;
    }
    public function supplier_3pl_new_cost_date(){
        return $this->supplier_3pl->new_cost_date;
    }
    public function supplier_3pl_consignment(){
        return $this->supplier_3pl->consignment;
    }

    //
    // Supplier Nestle Mapping ***
    //

    // Mapping with Supplier table
    public function supplier(){
        return $this->belongsTo('App\Supplier', 'client_supplier_id', 'id');
    }

    public function getETIN($product_temp = NULL,$type='product'){
        $ETIN = '';
        $last = NULL;
        $middle = NULL;

        $last_ETIN = LastETIN::where('type',$type)->latest('id')->first();


        if($last_ETIN){
            $lastrec_master_etin_array = explode('-',$last_ETIN->last_etin);

            if($lastrec_master_etin_array[2] == 9999){
                $middle = (int)$lastrec_master_etin_array[1];
                $middle++;
                $last = 0001;
            }else{
                $middle = (int)$lastrec_master_etin_array[1];
                $last = (int)$lastrec_master_etin_array[2];
                $last++;
            }
        }
        else{
            $last = 0001;
            $middle = 1000;
            $ETIN = 'ETFZ-1000-0001';
        }
        if($type == 'package')
        {
            $first_part = 'ETPM';
        }
        else{
            $first_part = 'ETOT';
        }

        if($product_temp){
            if($product_temp == "Frozen"){
                $first_part = 'ETFZ';
            } else if($product_temp == "Dry-Strong"){
                $first_part = 'ETDS';
            } else if($product_temp == "Refrigerated"){
                $first_part = 'ETRF';
            } else if($product_temp == "Beverages"){
                $first_part = 'ETBV';
            }  else if($product_temp == "Dry-Perishable"){
                $first_part = 'ETDP';
            }  else if($product_temp == "Dry-Fragile"){
                $first_part = 'ETDF';
            }  else if($product_temp == "Thaw & Serv"){
                $first_part = 'ETTS';
            }  else {
                $first_part = 'ETOT';
            }
        }
        if($last_ETIN){
            $last_ETIN->last_etin = $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
            $last_ETIN->save();
        }else{
            $last_ETIN = new LastETIN;
            $last_ETIN->last_etin = $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
            $last_ETIN->type = $type;
            $last_ETIN->save();
        }


        return $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function ValidateImages($input){
        // dd($input);
        $i = 1;
        $image_type = [];
        if(isset($input['image'])){
            foreach($input['image'] as $row_image){
                if(isset($row_image['img'])){
                    if(!empty($row_image['img']) && empty($row_image['image_type'])){
                        return [
                            'error' => true,
                            'msg' => 'Please Select Image Type at number: '.($i)
                        ];
                    }
                    if($row_image['image_type'] != ''){
                        $image_type_avail = DB::table('master_product_images')->where('ETIN',$input['ETIN'])->where('image_type',$row_image['image_type'])->first();
                        if($image_type_avail){
                            return [
                                'error' => true,
                                'msg' => 'Image Type of number: '.($i).' Already In Use',
                            ];
                        }
                        $image_type[] = $row_image['image_type'];
                    }
                    $i++;
                }
            }
        }

        if(count($image_type) > 0){
            if(count($image_type) !== count(array_unique($image_type))){
                return [
                    'error' => true,
                    'msg' => 'Image Type that you have selected must be unique for each image'
                ];
            }
        }

        return [
            'error' => false,
            'msg' => 'All God'
        ];
    }

    public function insertImageFzl($etin,$input){
        if(!Storage::disk('s3')->exists($etin)){
            $s3 = Storage::disk('s3')->makeDirectory($etin);
        }
        $s3 = \Storage::disk('s3');
        if(isset($input['image'])){
            foreach($input['image'] as $row_image){
                if(isset($row_image['img'])){
                    $image = $row_image['img'];
                    $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                    $img_type =$row_image['image_type'];
                    $file_name = $etin.'-'.$img_type.'.'.$ext;
                    $s3filePath = $etin. '/' . $file_name;
                    $s3->put($s3filePath, file_get_contents($image), 'public');
                    $imgurl = Storage::disk('s3')->url($s3filePath);
                    $insertImage['ETIN'] = $etin;
                    $insertImage['image_url'] = $imgurl;
                    $insertImage['image_text'] = $row_image['image_text'];
                    $insertImage['image_type'] = $img_type;
                    DB::table('master_product_images')->insert($insertImage);
                }
            }
        }
        return true;
    }

    public function insertImage($etin,$images,$image_type,$image_text){
        foreach($images as $key=>$image){
            if($image){
                $img_type = $now = Carbon::now()->timestamp;
                if(!Storage::disk('s3')->exists($etin)){
                    $s3 = Storage::disk('s3')->makeDirectory($etin);
                }
                $s3 = \Storage::disk('s3');
                $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
                if(!empty($image_type[$key])){
                    $i_type = ImageType::find($image_type[$key]);
                    $img_type =$i_type->image_type;
                }
                $file_name = $etin.'-'.$img_type.'.'.$ext;
                $s3filePath = $etin. '/' . $file_name;
                $s3->put($s3filePath, file_get_contents($image), 'public');
                $imgurl = Storage::disk('s3')->url($s3filePath);

                $insertImage['ETIN'] = $etin;
                $insertImage['image_url'] = $imgurl;
                $insertImage['image_text'] = $image_text[$key];
                $insertImage['image_type'] = !empty($image_type[$key]) ? $image_type[$key] : NULL;
                DB::table('master_product_images')->insert($insertImage);
            }
        }
        return true;
    }

    public function getMasterProductQueueNextId($productid){
        $nextProductId = NULL;
        $nextProduct =  DB::table('master_product_queue')->select('id')->where('id','>',$productid)->where('queue_status','e')->orderBy('id','asc')->first();
		if($nextProduct){
			$nextProductId = $nextProduct->id;
		}

        return $nextProductId;
    }

    public function getMasterProductQueuePrevId($productid){
		$prevProductId = NULL;
        $prevProduct = DB::table('master_product_queue')->select('id')->where('id','<',$productid)->where('queue_status','e')->orderBy('id','desc')->first();
		if($prevProduct){
			$prevProductId = $prevProduct->id;
		}
        return $prevProductId;
    }

    public function getDraftProductNextId($productid){
        $nextDraftProductId = NULL;
        $nextProduct =  DB::table('master_product_queue')->select('id')->where('id','>',$productid)->where('queue_status','d')->orderBy('id','asc')->first();
		if($nextProduct){
			$nextDraftProductId = $nextProduct->id;
		}

        return $nextDraftProductId;
    }

    public function getDraftProductPrevId($productid){
		$prevDraftProductId = NULL;
        $prevProduct = DB::table('master_product_queue')->select('id')->where('id','<',$productid)->where('queue_status','d')->orderBy('id','desc')->first();
		if($prevProduct){
			$prevDraftProductId = $prevProduct->id;
		}
        return $prevDraftProductId;
    }

    public function getMasterProductNextId($productid){
        $nextProductId = NULL;
        $nextProduct =  self::select('id')->where('id','>',$productid)->where('is_approve',0)->orderBy('id','asc')->first();
		if($nextProduct){
			$nextProductId = $nextProduct->id;
		}

        return $nextProductId;
    }

    public function getMasterProductPrevId($productid){
		$prevProductId = NULL;
        $prevProduct = self::select('id')->where('id','<',$productid)->where('is_approve',0)->orderBy('id','desc')->first();
		if($prevProduct){
			$prevProductId = $prevProduct->id;
		}
        return $prevProductId;
    }

    public function SlackDirectApproveProduct($id){

		$master_product_queue = DB::table('master_product_queue')->where('id',$id)->first();
        if(empty($master_product_queue)){
            $data_info = [
                'msg' => 'Your Product Is Already Approved OR Product Not Found',
                'error' => 1
            ];
		    $this->insertProcessLog('SlackDirectApproveProduct','Your Product Is Already Approved OR Product Not Found.');

            return $data_info;
        }
        // dd($master_product_queue);

		$master_product_id = $master_product_queue->master_product_id;
		$oldrecord = DB::table('master_product')->where('id',$master_product_queue->master_product_id)->first();

        $oldrecordarray = (array)$oldrecord;
        $oldrecordarray['id'] = null;
        $inserhistory = DB::table('master_product_history')->insert($oldrecordarray);
        $this->insertProcessLog('SlackDirectApproveProduct','Old Record Added To Master Product History.');

        $updatemaster = [];
        if($master_product_queue){
            $excluded_keys = ['id','created_at','updated_at','product_edit_request','is_approve','approved_date','master_product_id','queue_status'];

            $updatemaster['ETIN'] = $master_product_queue->ETIN;
            $updatemaster['parent_ETIN'] = $master_product_queue->parent_ETIN;
            $updatemaster['full_product_desc'] = $master_product_queue->full_product_desc;
            $updatemaster['about_this_item'] = $master_product_queue->about_this_item;
            $updatemaster['manufacturer'] = $master_product_queue->manufacturer;
            $updatemaster['brand'] = $master_product_queue->brand;
            $updatemaster['flavor'] = $master_product_queue->flavor;
            $updatemaster['product_type'] = $master_product_queue->product_type;
            $updatemaster['unit_size'] = $master_product_queue->unit_size;
            $updatemaster['unit_description'] = $master_product_queue->unit_description;
            $updatemaster['pack_form_count'] = $master_product_queue->pack_form_count;
            $updatemaster['item_form_description'] = $master_product_queue->item_form_description;
            $updatemaster['total_ounces'] = $master_product_queue->total_ounces;
            $updatemaster['product_category'] = $master_product_queue->product_category;
            $updatemaster['product_subcategory1'] = $master_product_queue->product_subcategory1;
            $updatemaster['product_subcategory2'] = $master_product_queue->product_subcategory2;
            $updatemaster['product_subcategory3'] = $master_product_queue->product_subcategory3;
            $updatemaster['product_subcategory4'] = $master_product_queue->product_subcategory3;
            $updatemaster['product_subcategory5'] = $master_product_queue->product_subcategory5;
            $updatemaster['product_subcategory6'] = $master_product_queue->product_subcategory6;
            $updatemaster['product_subcategory7'] = $master_product_queue->product_subcategory7;
            $updatemaster['product_subcategory8'] = $master_product_queue->product_subcategory8;
            $updatemaster['product_subcategory9'] = $master_product_queue->product_subcategory9;
            $updatemaster['key_product_attributes_diet'] = $master_product_queue->key_product_attributes_diet;
            $updatemaster['product_tags'] = $master_product_queue->product_tags;
            $updatemaster['MFG_shelf_life'] = $master_product_queue->MFG_shelf_life;
            $updatemaster['hazardous_materials'] = $master_product_queue->hazardous_materials;
            $updatemaster['storage'] = $master_product_queue->storage;
            $updatemaster['ingredients'] = $master_product_queue->ingredients;
            $updatemaster['allergens'] = $master_product_queue->allergens;
            $updatemaster['prop_65_flag'] = $master_product_queue->prop_65_flag;
            $updatemaster['prop_65_ingredient'] = $master_product_queue->prop_65_ingredient;
            $updatemaster['product_temperature'] = $master_product_queue->product_temperature;
            $updatemaster['supplier_status'] = $master_product_queue->supplier_status;
            $updatemaster['upc'] = $master_product_queue->upc;
            $updatemaster['gtin'] = $master_product_queue->gtin;
            $updatemaster['asin'] = $master_product_queue->asin;
            $updatemaster['GPC_code'] = $master_product_queue->GPC_code;
            $updatemaster['GPC_class'] = $master_product_queue->GPC_class;
            $updatemaster['HS_code'] = $master_product_queue->HS_code;
            $updatemaster['weight'] = $master_product_queue->weight;
            $updatemaster['length'] = $master_product_queue->length;
            $updatemaster['width'] = $master_product_queue->width;
            $updatemaster['height'] = $master_product_queue->height;
            $updatemaster['country_of_origin'] = $master_product_queue->country_of_origin;
            $updatemaster['package_information'] = $master_product_queue->package_information;
            $updatemaster['cost'] = $master_product_queue->cost;
            $updatemaster['new_cost'] = $master_product_queue->new_cost;
            $updatemaster['new_cost_date'] = $master_product_queue->new_cost_date;
            $updatemaster['status'] = $master_product_queue->status;
            $updatemaster['etailer_availability'] = $master_product_queue->etailer_availability;
            $updatemaster['dropship_available'] = $master_product_queue->dropship_available;
            $updatemaster['channel_listing_restrictions'] = $master_product_queue->channel_listing_restrictions;
            $updatemaster['POG_flag'] = $master_product_queue->POG_flag;
            $updatemaster['consignment'] = $master_product_queue->consignment;
            $updatemaster['warehouses_assigned'] = $master_product_queue->warehouses_assigned;
            $updatemaster['status_date'] = $master_product_queue->status_date;
            $updatemaster['lobs'] = $master_product_queue->lobs;
            $updatemaster['client_supplier_id'] = $master_product_queue->current_supplier;
            $updatemaster['alternate_ETINs'] = $master_product_queue->alternate_ETINs;
            $updatemaster['product_listing_ETIN'] = $master_product_queue->product_listing_ETIN;
            $updatemaster['unit_in_pack'] = $master_product_queue->unit_in_pack;
            $updatemaster['manufacture_product_number'] = $master_product_queue->manufacture_product_number;
            $updatemaster['supplier_product_number'] = $master_product_queue->supplier_product_number;
            $updatemaster['total_ounces'] = $master_product_queue->total_ounces;
            $updatemaster['is_edit'] = 1;
            $updatemaster['is_approve'] = $master_product_queue->is_approve;
            $updatemaster['approved_date'] = $master_product_queue->approved_date;

            $updatemaster['product_listing_name'] = $master_product_queue->product_listing_name;
            $updatemaster['product_edit_request'] = NULL;
            $updatemaster['updated_by'] = Auth::user()->id;

            // dd($updatemaster);
            $CMP = DB::table('master_product')->where('id',$master_product_queue->master_product_id)->first();
            if($CMP){
                $updatemaster['updated_at'] = date('Y-m-d H:i:s');
                DB::table('master_product')->where('id',$master_product_queue->master_product_id)->update($updatemaster);
                $this->MakeProductHistory([
                    'response' => Auth::user()->name.' approved changes',
                    'master_product_id' => $master_product_queue->master_product_id,
                    'action' => 'Approved'
                ]);
                $this->insertProcessLog('SlackDirectApproveProduct','Master Product Updated.');
                $this->insertProductHistoryForApprove($updatemaster,$CMP,$master_product_queue->master_product_id);

            }else{
                $updatemaster['created_at'] = date('Y-m-d H:i:s');
                $updatemaster['updated_at'] = date('Y-m-d H:i:s');
                $master_product_id = DB::table('master_product')->insertGetId($updatemaster);
                $this->MakeProductHistory([
                    'response' => Auth::user()->name.' created Product: '.$updatemaster['ETIN'],
                    'master_product_id' => $master_product_id,
                    'action' => 'Add'
                ]);
                $this->insertProcessLog('SlackDirectApproveProduct','Master Product Inserted.');
            }

        }

        $check_supplemental_mpt_data_queue = DB::table('supplemental_mpt_data_queue')->where('master_product_id',$id)->first();

        $update_suppliment = [];
        if($check_supplemental_mpt_data_queue){
            $excluded_keys = ['id','created_at','updated_at'];
            foreach($check_supplemental_mpt_data_queue as $key => $value) {
                if(!in_array($key,$excluded_keys)){
                    $update_suppliment[$key] = $value;
                }
            }
            $update_suppliment['master_product_id'] = $master_product_id;
            $check_supplemental_mpt_data = DB::table('supplemental_mpt_data')->where('master_product_id',$master_product_id)->first();
            if(!$check_supplemental_mpt_data){
                DB::table('supplemental_mpt_data')->insert($update_suppliment);
                $this->insertProcessLog('SlackDirectApproveProduct','Supplemental MPT Data Inserted.');
            }else{
                DB::table('supplemental_mpt_data')->where('master_product_id', $master_product_id)->update($update_suppliment);
                $this->insertProcessLog('SlackDirectApproveProduct','Supplemental MPT Data Updated.');

            }
        }

        $ETIN = $master_product_queue->ETIN;
        $proimage = [];
        $checkimage = DB::table('product_images_queue')->where('ETIN',$ETIN)->first();
        if($checkimage){
            $excluded_keys = ['id','created_at','updated_at'];
            foreach($checkimage as $key => $value) {
                if(!in_array($key,$excluded_keys)){
                    $proimage[$key] = $value;
                }
            }
            $CIP = DB::table('product_images')->where('ETIN',$ETIN)->first();
            if($CIP){
                DB::table('product_images')->where('ETIN',$updatemaster['ETIN'])->update($proimage);
                $this->insertProcessLog('SlackDirectApproveProduct','Product Images Updated.');

            }else{
                DB::table('product_images')->insert($proimage);
                $this->insertProcessLog('SlackDirectApproveProduct','Product Images Inserted.');
            }
        }
		// }

		DB::table('master_product_images')->where('ETIN',$master_product_queue->ETIN)->update(['ETIN' => $updatemaster['ETIN']]);
        $this->insertProcessLog('SlackDirectApproveProduct','Master Product Images Updated.');

		DB::table('master_product_queue')->where('ETIN',$ETIN)->delete();
        $this->insertProcessLog('SlackDirectApproveProduct','Product Removed To Queue.');

		// DB::table('product_inventory_queue')->where('ETIN',$ETIN)->delete();
        // $this->insertProcessLog('SlackDirectApproveProduct','Product Inventory Removed To Queue.');

		DB::table('product_images_queue')->where('ETIN',$ETIN)->delete();
        $this->insertProcessLog('SlackDirectApproveProduct','Product Images Removed To Queue.');

		DB::table('supplemental_mpt_data_queue')->where('master_product_id',$id)->delete();
        $this->insertProcessLog('SlackDirectApproveProduct','Supplemental MPT Data Removed To Queue.');


		$data_info = [
			'msg' => 'Product '.$ETIN.' is Approved',
			'error' => 0,
            'product_id' => $master_product_id
		];

        return $data_info;
	}

    public function sendApproveRejectNotificationForEdit($new_data,$old_data,$product_id){
        $data = [];
        if($old_data->ETIN != $new_data['ETIN']){
            $data['ETIN Old'] = $old_data->ETIN;
            $data['ETIN New'] = $new_data['ETIN'];
        }
        if($old_data->lobs != $new_data['lobs']){

            $old_lobs = explode(',',$old_data->lobs);
            $new_lobs = explode(',',$new_data['lobs']);
            $old_name_array = [];
            $new_name_array = [];
            foreach($old_lobs as $key=>$value){
                $lobs_name = clientName($value);
                array_push($old_name_array,$lobs_name);
            }
            foreach($new_lobs as $key=>$value){
                $lobs_name = clientName($value);
                array_push($new_name_array,$lobs_name);
            }

            $data['Lobs Old'] =  implode(',',$old_name_array);
            $data['Lobs New'] = implode(',',$new_name_array);
        }
        if($old_data->parent_ETIN != $new_data['parent_ETIN']){
            $data['Parent ETIN Old'] = $old_data->parent_ETIN;
            $data['Parent ETIN New'] = $new_data['parent_ETIN'];
        }
        if($old_data->product_listing_name != $new_data['product_listing_name']){
            $data['Product Listing Name Old'] = $old_data->product_listing_name;
            $data['Product Listing Name New'] = $new_data['product_listing_name'];
        }
        if($old_data->full_product_desc != $new_data['full_product_desc']){
            $data['Full Product Description Old'] = $old_data->full_product_desc;
            $data['Full Product Description New'] = $new_data['full_product_desc'];
        }
        if($old_data->manufacturer != $new_data['manufacturer']){
            $data['Manufacturer Old'] = $old_data->manufacturer;
            $data['Manufacturer New'] = $new_data['manufacturer'];
        }
        if($old_data->brand != $new_data['brand']){
            $data['Brand Old'] = $old_data->brand;
            $data['Brand New'] = $new_data['brand'];
        }
        if($old_data->unit_size != $new_data['unit_size']){
            $data['Unit Size Old'] = $old_data->unit_size;
            $data['Unit Size New'] = $new_data['unit_size'];
        }
        if($old_data->product_type != $new_data['product_type']){
            $data['Product Type Old'] = $old_data->product_type;
            $data['Product Type New'] = $new_data['product_type'];
        }
        if($old_data->unit_description != $new_data['unit_description']){
            $data['Unit Description Old'] = $old_data->unit_description;
            $data['Unit Description New'] = $new_data['unit_description'];
        }
        if($old_data->pack_form_count != $new_data['pack_form_count']){
            $data['Pack Form Count Old'] = $old_data->pack_form_count;
            $data['Pack Form Count New'] = $new_data['pack_form_count'];
        }
        if($old_data->unit_in_pack != $new_data['unit_in_pack']){
            $data['Unit In Pack Old'] = $old_data->unit_in_pack;
            $data['Unit In Pack New'] = $new_data['unit_in_pack'];
        }
        if($old_data->item_form_description != $new_data['item_form_description']){
            $data['Item Form Description Old'] = $old_data->item_form_description;
            $data['Item Form Description New'] = $new_data['item_form_description'];
        }
        if($old_data->total_ounces != $new_data['total_ounces']){
            $data['Total Ounces Old'] = $old_data->total_ounces;
            $data['Total Ounces New'] = $new_data['total_ounces'];
        }
        if($old_data->product_category != $new_data['product_category']){
            $data['Product Category Old'] = CategoryName($old_data->product_category);
            $data['Product Category New'] = CategoryName($new_data['product_category']);
        }
        if($old_data->product_subcategory1 != $new_data['product_subcategory1']){
            $data['Product Category1 Old'] = CategoryName($old_data->product_subcategory1);
            $data['Product Category1 New'] = CategoryName($new_data['product_subcategory1']);
        }
        if($old_data->product_subcategory2 != $new_data['product_subcategory2']){
            $data['Product Category2 Old'] = CategoryName($old_data->product_subcategory2);
            $data['Product Category2 New'] = CategoryName($new_data['product_subcategory2']);
        }
        if($old_data->product_subcategory3 != $new_data['product_subcategory3']){
            $data['Product Category3 Old'] = CategoryName($old_data->product_subcategory3);
            $data['Product Category3 New'] = CategoryName($new_data['product_subcategory3']);
        }


        if($old_data->product_subcategory4 != $new_data['product_subcategory4']){
            $data['Product Category4 Old'] = CategoryName($old_data->product_subcategory4);
            $data['Product Category4 New'] = CategoryName($new_data['product_subcategory4']);
        }

        if($old_data->product_subcategory5 != $new_data['product_subcategory5']){
            $data['Product Category5 Old'] = CategoryName($old_data->product_subcategory5);
            $data['Product Category5 New'] = CategoryName($new_data['product_subcategory5']);
        }

        if($old_data->product_subcategory6 != $new_data['product_subcategory6']){
            $data['Product Category6 Old'] = CategoryName($old_data->product_subcategory6);
            $data['Product Category6 New'] = CategoryName($new_data['product_subcategory6']);
        }

        if($old_data->product_subcategory7 != $new_data['product_subcategory7']){
            $data['Product Category7 Old'] = CategoryName($old_data->product_subcategory7);
            $data['Product Category7 New'] = CategoryName($new_data['product_subcategory7']);
        }

        if($old_data->product_subcategory8 != $new_data['product_subcategory8']){
            $data['Product Category8 Old'] = CategoryName($old_data->product_subcategory8);
            $data['Product Category8 New'] = CategoryName($new_data['product_subcategory8']);
        }

        if($old_data->product_subcategory9 != $new_data['product_subcategory9']){
            $data['Product Category9 Old'] = CategoryName($old_data->product_subcategory9);
            $data['Product Category9 New'] = CategoryName($new_data['product_subcategory9']);
        }


        if($old_data->key_product_attributes_diet != $new_data['key_product_attributes_diet']){
            $data['Key Product Attributes & Diet Old'] = $old_data->key_product_attributes_diet;
            $data['Key Product Attributes & Diet New'] = $new_data['key_product_attributes_diet'];
        }
        if($old_data->product_tags != $new_data['product_tags']){
            $old_product_tags = explode(',',$old_data->product_tags);
            $new_product_tags = explode(',',$new_data['product_tags']);
            $old_name_array = [];
            $new_name_array = [];
            foreach($old_product_tags as $key=>$value){
                $ptag_name = producttageName($value);
                array_push($old_name_array,$ptag_name);
            }
            foreach($new_product_tags as $key=>$value){
                $ptag_name = producttageName($value);
                array_push($new_name_array,$ptag_name);
            }
            $data['Product Tags Old'] = implode(',',$old_name_array);
            $data['Product Tags New'] = implode(',',$new_name_array);
        }
        if($old_data->MFG_shelf_life != $new_data['MFG_shelf_life']){
            $data['MFG Shelf Life Old'] = $old_data->MFG_shelf_life;
            $data['MFG Shelf Life New'] = $new_data['MFG_shelf_life'];
        }
        if($old_data->hazardous_materials != $new_data['hazardous_materials']){
            $data['Hazardous Materials Old'] = $old_data->hazardous_materials;
            $data['Hazardous Materials New'] = $new_data['hazardous_materials'];

        }if($old_data->storage != $new_data['storage']){
            $data['Storage Old'] = $old_data->storage;
            $data['Storage New'] = $new_data['storage'];
        }
        if($old_data->ingredients != $new_data['ingredients']){
            $data['Ingredients Old'] = $old_data->ingredients;
            $data['Ingredients New'] = $new_data['ingredients'];
        }
        if($old_data->allergens != $new_data['allergens']){
            $old_allergens = explode(',',$old_data->allergens);
            $new_allergens = explode(',',$new_data['allergens']);
            $old_name_array = [];
            $new_name_array = [];
            foreach($old_allergens as $key=>$value){
                $al_name = allergensName($value);
                array_push($old_name_array,$al_name);
            }
            foreach($new_allergens as $key=>$value){
                $al_name = allergensName($value);
                array_push($new_name_array,$al_name);
            }
            $data['Allergens Old'] = implode(',',$old_name_array);
            $data['Allergens New'] = implode(',',$new_name_array);
        }
        if($old_data->prop_65_flag != $new_data['prop_65_flag']){
            $data['Prop 65 Flag Old'] = $old_data->prop_65_flag;
            $data['Prop 65 Flag New'] = $new_data['prop_65_flag'];
        }
        if($old_data->prop_65_ingredient != $new_data['prop_65_ingredient']){

            $old_prop_65_ingredient = explode(',',$old_data->prop_65_ingredient);
            $new_prop_65_ingredient = explode(',',$new_data['prop_65_ingredient']);
            $old_name_array = [];
            $new_name_array = [];
            foreach($old_prop_65_ingredient as $key=>$value){
                $prop_65_ingredient_name = prop_65_name($value);
                array_push($old_name_array,$prop_65_ingredient_name);
            }
            foreach($new_prop_65_ingredient as $key=>$value){
                $prop_65_ingredient_name = prop_65_name($value);
                array_push($new_name_array,$prop_65_ingredient_name);
            }

            $data['Prop 65 Ingredient Old'] = implode(',',$old_name_array);
            $data['Prop 65 Ingredient New'] = implode(',',$new_name_array);
        }
        if($old_data->product_temperature != $new_data['product_temperature']){
            $data['Product Temperature Old'] = $old_data->product_temperature;
            $data['Product Temperature New'] = $new_data['product_temperature'];
        }
        if($old_data->supplier_product_number != $new_data['supplier_product_number']){
            $data['Supplier Product Number Old'] = $old_data->supplier_product_number;
            $data['Supplier Product Number New'] = $new_data['supplier_product_number'];
        }
        if($old_data->manufacture_product_number != $new_data['manufacture_product_number']){
            $data['Manufacturer Product Number Old'] = $old_data->manufacture_product_number;
            $data['Manufacturer Product Number New'] = $new_data['manufacture_product_number'];
        }
        if($old_data->upc != $new_data['upc']){
            $data['UPC Old'] = $old_data->upc;
            $data['UPC New'] = $new_data['upc'];
        }
        if($old_data->gtin != $new_data['gtin']){
            $data['GTIN Old'] = $old_data->gtin;
            $data['GTIN New'] = $new_data['gtin'];
        }
        if($old_data->asin != $new_data['asin']){
            $data['ASIN Old'] = $old_data->asin;
            $data['ASIN New'] = $new_data['asin'];
        }
        if($old_data->GPC_code != $new_data['GPC_code']){
            $data['GPC Code Old'] = $old_data->GPC_code;
            $data['GPC Code New'] = $new_data['GPC_code'];
        }
        if($old_data->GPC_class != $new_data['GPC_class']){
            $data['GPC Class Old'] = $old_data->GPC_class;
            $data['GPC Class New'] = $new_data['GPC_class'];
        }
        if($old_data->HS_code != $new_data['HS_code']){
            $data['HS Code Old'] = $old_data->HS_code;
            $data['HS Code New'] = $new_data['HS_code'];
        }
        if($old_data->weight != $new_data['weight']){
            $data['Weight Old'] = $old_data->weight;
            $data['Weight New'] = $new_data['weight'];
        }
        if($old_data->length != $new_data['length']){
            $data['Length Old'] = $old_data->length;
            $data['Length New'] = $new_data['length'];
        }
        if($old_data->width != $new_data['width']){
            $data['Width Old'] = $old_data->width;
            $data['Width New'] = $new_data['width'];
        }
        if($old_data->height != $new_data['height']){
            $data['Height Old'] = $old_data->height;
            $data['Height New'] = $new_data['height'];
        }
        if($old_data->country_of_origin != $new_data['country_of_origin']){
            $data['Country of Origin Old'] = countryName($old_data->country_of_origin);
            $data['Country of Origin New'] = countryName($new_data['country_of_origin']);
        }
        if($old_data->package_information != $new_data['package_information']){
            $data['Package Information Old'] = $old_data->package_information;
            $data['Package Information New'] = $new_data['package_information'];
        }
        if($old_data->cost != $new_data['cost']){
            $data['Cost Old'] = $old_data->cost;
            $data['Cost New'] = $new_data['cost'];
        }
        if($old_data->new_cost != $new_data['new_cost']){
            $data['New Cost Old'] = $old_data->new_cost;
            $data['New Cost New'] = $new_data['new_cost'];
        }
        if($old_data->new_cost_date != $new_data['new_cost_date']){
            $data['New Cost_date Old'] = $old_data->new_cost_date;
            $data['New Cost_date New'] = $new_data['new_cost_date'];
        }
        if($old_data->status != $new_data['status']){
            $data['Status Old'] = $old_data->status;
            $data['Status New'] = $new_data['status'];
        }
        if($old_data->etailer_availability != $new_data['etailer_availability']){
            $data['e-tailer Availability Old'] = etailerName($old_data->etailer_availability);
            $data['e-tailer Availability New'] = etailerName($new_data['etailer_availability']);
        }
        if($old_data->dropship_available != $new_data['dropship_available']){
            $data['Dropship Available Old'] = $old_data->dropship_available;
            $data['Dropship Available New'] = $new_data['dropship_available'];
        }
        if($old_data->channel_listing_restrictions != $new_data['channel_listing_restrictions']){
            $data['Channel Listing Restrictions Old'] = $old_data->channel_listing_restrictions;
            $data['Channel Listing Restrictions New'] = $new_data['channel_listing_restrictions'];
        }
        if($old_data->POG_flag != $new_data['POG_flag']){
            $data['POG Flag Old'] = $old_data->POG_flag;
            $data['POG F    lag New'] = $new_data['POG_flag'];
        }
        if($old_data->consignment != $new_data['consignment']){
            $data['Consignment Old'] = $old_data->consignment;
            $data['Consignment New'] = $new_data['consignment'];
        }
        if($old_data->warehouses_assigned != $new_data['warehouses_assigned']){
            $data['Warehouses Assigned Old'] = $old_data->warehouses_assigned;
            $data['Warehouses Assigned New'] = $new_data['warehouses_assigned'];
        }
        if($old_data->status_date != $new_data['status_date']){
            $data['Status Date Old'] = $old_data->status_date;
            $data['Status Date New'] = $new_data['status_date'];
        }
        if($old_data->client_supplier_id != $new_data['client_supplier_id']){
            $data['Current Supplier Old'] = $old_data->client_supplier_id;
            $data['Current Supplier New'] = $new_data['client_supplier_id'];
        }
        if($old_data->supplier_status != $new_data['supplier_status']){
            $data['Supplier Status Old'] = supplierStatusName($old_data->supplier_status);
            $data['Supplier Status New'] = supplierStatusName($new_data['supplier_status']);
        }
        if($old_data->about_this_item != $new_data['about_this_item']){
            $data['About This Item Old'] = $old_data->about_this_item;
            $data['About This Item New'] = $new_data['about_this_item'];
        }
        if($old_data->product_listing_ETIN != $new_data['product_listing_ETIN']){
            $data['Product Listing ETIN Old'] = $old_data->product_listing_ETIN;
            $data['Product Listing ETIN New'] = $new_data['product_listing_ETIN'];
        }
        if($old_data->alternate_ETINs != $new_data['alternate_ETINs']){
            $data['Alternate ETINs Old'] = $old_data->alternate_ETINs;
            $data['Alternate ETINs New'] = $new_data['alternate_ETINs'];
        }
        $url['product_request_url'] = url("/editmasterrequestview/$product_id");
        $url['approve_url'] = url("/SlackDirectApproveProduct/$product_id");
        $url['reject_url'] = url('/ApproveOrRejectProductRequest/'.$new_data["ETIN"].'/0');
        $for = "edit";
		Notification::send(User::first(), new ApproveRejectProductNotification($url,$for,$new_data['ETIN'],$new_data['product_listing_name'],$data));
    }

    public function sendApproveRejectNotificationForAdd($product_id,$ETIN,$product_listing_name){
        $url = url("/editmasterproduct/$product_id");
        $for = "add";
		Notification::send(User::first(), new ApproveRejectProductNotification($url,$for,$ETIN,$product_listing_name));
    }

    public function insertProductHistoryForApprove($new_data,$old_data,$master_product_id){
        $data = [];

        if($old_data->ETIN != $new_data['ETIN']){
            $data['ETIN Old'] = $old_data->ETIN;
            $data['ETIN New'] = $new_data['ETIN'];
        }
        if($old_data->lobs != $new_data['lobs']){
            $data['Lobs Old'] = $old_data->lobs;
            $data['Lobs New'] = $new_data['lobs'];
        }
        if($old_data->parent_ETIN != $new_data['parent_ETIN']){
            $data['Parent ETIN Old'] = $old_data->parent_ETIN;
            $data['Parent ETIN New'] = $new_data['parent_ETIN'];
        }
        if($old_data->product_listing_name != $new_data['product_listing_name']){
            $data['Product Listing Name Old'] = $old_data->product_listing_name;
            $data['Product Listing Name New'] = $new_data['product_listing_name'];
        }
        if($old_data->full_product_desc != $new_data['full_product_desc']){
            $data['Full Product Description Old'] = $old_data->full_product_desc;
            $data['Full Product Description New'] = $new_data['full_product_desc'];
        }
        if($old_data->manufacturer != $new_data['manufacturer']){
            $data['Manufacturer Old'] = $old_data->manufacturer;
            $data['Manufacturer New'] = $new_data['manufacturer'];
        }
        if($old_data->brand != $new_data['brand']){
            $data['Brand Old'] = $old_data->brand;
            $data['Brand New'] = $new_data['brand'];
        }
        if($old_data->unit_size != $new_data['unit_size']){
            $data['Unit Size Old'] = $old_data->unit_size;
            $data['Unit Size New'] = $new_data['unit_size'];
        }
        if($old_data->product_type != $new_data['product_type']){
            $data['Product Type Old'] = $old_data->product_type;
            $data['Product Type New'] = $new_data['product_type'];
        }
        if($old_data->unit_description != $new_data['unit_description']){
            $data['Unit Description Old'] = $old_data->unit_description;
            $data['Unit Description New'] = $new_data['unit_description'];
        }
        if($old_data->pack_form_count != $new_data['pack_form_count']){
            $data['Pack Form Count Old'] = $old_data->pack_form_count;
            $data['Pack Form Count New'] = $new_data['pack_form_count'];
        }
        if($old_data->unit_in_pack != $new_data['unit_in_pack']){
            $data['Unit In Pack Old'] = $old_data->unit_in_pack;
            $data['Unit In Pack New'] = $new_data['unit_in_pack'];
        }
        if($old_data->item_form_description != $new_data['item_form_description']){
            $data['Item Form Description Old'] = $old_data->item_form_description;
            $data['Item Form Description New'] = $new_data['item_form_description'];
        }
        if($old_data->total_ounces != $new_data['total_ounces']){
            $data['Total Ounces Old'] = $old_data->total_ounces;
            $data['Total Ounces New'] = $new_data['total_ounces'];
        }
        if($old_data->product_category != $new_data['product_category']){
            $data['Product Category Old'] = CategoryName($old_data->product_category);
            $data['Product Category New'] = CategoryName($new_data['product_category']);
        }
        if($old_data->product_subcategory1 != $new_data['product_subcategory1']){
            $data['Product Category1 Old'] = CategoryName($old_data->product_subcategory1);
            $data['Product Category1 New'] = CategoryName($new_data['product_subcategory1']);
        }
        if($old_data->product_subcategory2 != $new_data['product_subcategory2']){
            $data['Product Category2 Old'] = CategoryName($old_data->product_subcategory2);
            $data['Product Category2 New'] = CategoryName($new_data['product_subcategory2']);
        }
        if($old_data->product_subcategory3 != $new_data['product_subcategory3']){
            $data['Product Category3 Old'] = CategoryName($old_data->product_subcategory3);
            $data['Product Category3 New'] = CategoryName($new_data['product_subcategory3']);
        }


        if($old_data->product_subcategory4 != $new_data['product_subcategory4']){
            $data['Product Category4 Old'] = CategoryName($old_data->product_subcategory4);
            $data['Product Category4 New'] = CategoryName($new_data['product_subcategory4']);
        }

        if($old_data->product_subcategory3 != $new_data['product_subcategory3']){
            $data['Product Category5 Old'] = CategoryName($old_data->product_subcategory3);
            $data['Product Category5 New'] = CategoryName($new_data['product_subcategory3']);
        }

        if($old_data->product_subcategory6 != $new_data['product_subcategory6']){
            $data['Product Category6 Old'] = CategoryName($old_data->product_subcategory6);
            $data['Product Category6 New'] = CategoryName($new_data['product_subcategory6']);
        }

        if($old_data->product_subcategory7 != $new_data['product_subcategory7']){
            $data['Product Category7 Old'] = CategoryName($old_data->product_subcategory7);
            $data['Product Category7 New'] = CategoryName($new_data['product_subcategory7']);
        }

        if($old_data->product_subcategory8 != $new_data['product_subcategory8']){
            $data['Product Category8 Old'] = CategoryName($old_data->product_subcategory8);
            $data['Product Category8 New'] = CategoryName($new_data['product_subcategory8']);
        }

        if($old_data->product_subcategory9 != $new_data['product_subcategory9']){
            $data['Product Category9 Old'] = CategoryName($old_data->product_subcategory9);
            $data['Product Category9 New'] = CategoryName($new_data['product_subcategory9']);
        }

        if($old_data->key_product_attributes_diet != $new_data['key_product_attributes_diet']){
            $data['Key Product Attributes & Diet Old'] = $old_data->key_product_attributes_diet;
            $data['Key Product Attributes & Diet New'] = $new_data['key_product_attributes_diet'];
        }
        if($old_data->product_tags != $new_data['product_tags']){
            $data['Product Tags Old'] = $old_data->product_tags;
            $data['Product Tags New'] = $new_data['product_tags'];
        }
        if($old_data->MFG_shelf_life != $new_data['MFG_shelf_life']){
            $data['MFG Shelf Life Old'] = $old_data->MFG_shelf_life;
            $data['MFG Shelf Life New'] = $new_data['MFG_shelf_life'];
        }
        if($old_data->hazardous_materials != $new_data['hazardous_materials']){
            $data['Hazardous Materials Old'] = $old_data->hazardous_materials;
            $data['Hazardous Materials New'] = $new_data['hazardous_materials'];

        }if($old_data->storage != $new_data['storage']){
            $data['Storage Old'] = $old_data->storage;
            $data['Storage New'] = $new_data['storage'];
        }
        if($old_data->ingredients != $new_data['ingredients']){
            $data['Ingredients Old'] = $old_data->ingredients;
            $data['Ingredients New'] = $new_data['ingredients'];
        }
        if($old_data->allergens != $new_data['allergens']){
            $data['Allergens Old'] = $old_data->allergens;
            $data['Allergens New'] = $new_data['allergens'];
        }
        if($old_data->prop_65_flag != $new_data['prop_65_flag']){
            $data['Prop 65 Flag Old'] = $old_data->prop_65_flag;
            $data['Prop 65 Flag New'] = $new_data['prop_65_flag'];
        }
        if($old_data->prop_65_ingredient != $new_data['prop_65_ingredient']){
            $data['Prop 65 Ingredient Old'] = $old_data->prop_65_ingredient;
            $data['Prop 65 Ingredient New'] = $new_data['prop_65_ingredient'];
        }
        if($old_data->product_temperature != $new_data['product_temperature']){
            $data['Product Temperature Old'] = $old_data->product_temperature;
            $data['Product Temperature New'] = $new_data['product_temperature'];
        }
        if($old_data->supplier_product_number != $new_data['supplier_product_number']){
            $data['Supplier Product Number Old'] = $old_data->supplier_product_number;
            $data['Supplier Product Number New'] = $new_data['supplier_product_number'];
        }
        if($old_data->manufacture_product_number != $new_data['manufacture_product_number']){
            $data['Manufacturer Product Number Old'] = $old_data->manufacture_product_number;
            $data['Manufacturer Product Number New'] = $new_data['manufacture_product_number'];
        }
        if($old_data->upc != $new_data['upc']){
            $data['UPC Old'] = $old_data->upc;
            $data['UPC New'] = $new_data['upc'];
        }
        if($old_data->gtin != $new_data['gtin']){
            $data['GTIN Old'] = $old_data->gtin;
            $data['GTIN New'] = $new_data['gtin'];
        }
        if($old_data->asin != $new_data['asin']){
            $data['ASIN Old'] = $old_data->asin;
            $data['ASIN New'] = $new_data['asin'];
        }
        if($old_data->GPC_code != $new_data['GPC_code']){
            $data['GPC Code Old'] = $old_data->GPC_code;
            $data['GPC Code New'] = $new_data['GPC_code'];
        }
        if($old_data->GPC_class != $new_data['GPC_class']){
            $data['GPC Class Old'] = $old_data->GPC_class;
            $data['GPC Class New'] = $new_data['GPC_class'];
        }
        if($old_data->HS_code != $new_data['HS_code']){
            $data['HS Code Old'] = $old_data->HS_code;
            $data['HS Code New'] = $new_data['HS_code'];
        }
        if($old_data->weight != $new_data['weight']){
            $data['Weight Old'] = $old_data->weight;
            $data['Weight New'] = $new_data['weight'];
        }
        if($old_data->length != $new_data['length']){
            $data['Length Old'] = $old_data->length;
            $data['Length New'] = $new_data['length'];
        }
        if($old_data->width != $new_data['width']){
            $data['Width Old'] = $old_data->width;
            $data['Width New'] = $new_data['width'];
        }
        if($old_data->height != $new_data['height']){
            $data['Height Old'] = $old_data->height;
            $data['Height New'] = $new_data['height'];
        }
        if($old_data->country_of_origin != $new_data['country_of_origin']){
            $data['Country of Origin Old'] = $old_data->country_of_origin;
            $data['Country of Origin New'] = $new_data['country_of_origin'];
        }
        if($old_data->package_information != $new_data['package_information']){
            $data['Package Information Old'] = $old_data->package_information;
            $data['Package Information New'] = $new_data['package_information'];
        }
        if($old_data->cost != $new_data['cost']){
            $data['Cost Old'] = $old_data->cost;
            $data['Cost New'] = $new_data['cost'];
        }
        if($old_data->new_cost != $new_data['new_cost']){
            $data['New Cost Old'] = $old_data->new_cost;
            $data['New Cost New'] = $new_data['new_cost'];
        }
        if($old_data->new_cost_date != $new_data['new_cost_date']){
            $data['New Cost_date Old'] = $old_data->new_cost_date;
            $data['New Cost_date New'] = $new_data['new_cost_date'];
        }
        if($old_data->status != $new_data['status']){
            $data['Status Old'] = $old_data->status;
            $data['Status New'] = $new_data['status'];
        }
        if($old_data->etailer_availability != $new_data['etailer_availability']){
            $data['e-tailer Availability Old'] = $old_data->etailer_availability;
            $data['e-tailer Availability New'] = $new_data['etailer_availability'];
        }
        if($old_data->dropship_available != $new_data['dropship_available']){
            $data['Dropship Available Old'] = $old_data->dropship_available;
            $data['Dropship Available New'] = $new_data['dropship_available'];
        }
        if($old_data->channel_listing_restrictions != $new_data['channel_listing_restrictions']){
            $data['Channel Listing Restrictions Old'] = $old_data->channel_listing_restrictions;
            $data['Channel Listing Restrictions New'] = $new_data['channel_listing_restrictions'];
        }
        if($old_data->POG_flag != $new_data['POG_flag']){
            $data['POG Flag Old'] = $old_data->POG_flag;
            $data['POG Flag New'] = $new_data['POG_flag'];
        }
        if($old_data->consignment != $new_data['consignment']){
            $data['Consignment Old'] = $old_data->consignment;
            $data['Consignment New'] = $new_data['consignment'];
        }
        if($old_data->warehouses_assigned != $new_data['warehouses_assigned']){
            $data['Warehouses Assigned Old'] = $old_data->warehouses_assigned;
            $data['Warehouses Assigned New'] = $new_data['warehouses_assigned'];
        }
        if($old_data->status_date != $new_data['status_date']){
            $data['Status Date Old'] = $old_data->status_date;
            $data['Status Date New'] = $new_data['status_date'];
        }
        if($old_data->client_supplier_id != $new_data['client_supplier_id']){
            $data['Current Supplier Old'] = $old_data->client_supplier_id;
            $data['Current Supplier New'] = $new_data['client_supplier_id'];
        }
        if($old_data->supplier_status != $new_data['supplier_status']){
            $data['Supplier Status Old'] = $old_data->supplier_status;
            $data['Supplier Status New'] = $new_data['supplier_status'];
        }
        if($old_data->about_this_item != $new_data['about_this_item']){
            $data['About This Item Old'] = $old_data->about_this_item;
            $data['About This Item New'] = $new_data['about_this_item'];
        }
        if($old_data->product_listing_ETIN != $new_data['product_listing_ETIN']){
            $data['Product Listing ETIN Old'] = $old_data->product_listing_ETIN;
            $data['Product Listing ETIN New'] = $new_data['product_listing_ETIN'];
        }
        if($old_data->alternate_ETINs != $new_data['alternate_ETINs']){
            $data['Alternate ETINs Old'] = $old_data->alternate_ETINs;
            $data['Alternate ETINs New'] = $new_data['alternate_ETINs'];
        }

        DB::table('product_history')->insert([
            'master_product_id' => $master_product_id,
            'action' => 'approve',
            'response' => json_encode($data),
            'updated_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return true;
    }

    public function insertProductHistory($master_product_id,$action){
        $response = '';
        if($action == 'publish'){
            $response = 'Product Published Successfully';
        }
        if($action == 'add'){
            $response = 'Product Added Successfully';
        }
        DB::table('product_history')->insert([
            'master_product_id' => $master_product_id,
            'action' => $action,
            'response' => $response,
            'updated_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return true;
    }

    public function insertProcessLog($event,$log){
        DB::table('process_logs')->insert([
            'event' => $event,
            'log_title' => $log,
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function MakeProductHistory($response){
        // dd($response);
        $data = [];
        $ProductHistory = new ProductHistory;
        if(isset($response['old_data']) && isset($response['new_data'])){


            $old_data = $response['old_data'];
            $new_data = $response['new_data'];
            $exclude_array = [
                'id',
                'created_at',
                'updated_at',
                'is_approve',
                'approved_date',
                'is_edit',
                'updated_by'
            ];
            foreach($new_data as $key => $row){
                if(isset($old_data->$key)  && $old_data->$key != $new_data[$key]){
                    if(!in_array($key,$exclude_array)){
                        $key_new = ucfirst(str_replace('_',' ',$key));
                        $old_data_value = $old_data->$key;
                        $new_data_value = $new_data[$key];
                        if($key == 'lobs'){
                            $old_lobs = [];
                            $new_lobs = [];
                            foreach(explode(',',$old_data_value) as $row_old_lob_val){
                                $old_lobs[] = clientName($row_old_lob_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_lob_val){
                                $new_lobs[] = clientName($row_new_lob_val);
                            }
                            $old_data_value = implode(',',$old_lobs);
                            $new_data_value = implode(',',$new_lobs);
                        }

                        if($key == 'allergens'){
                            $old_allergens = [];
                            $new_allergens = [];
                            foreach(explode(',',$old_data_value) as $row_old_allergens_val){
                                $old_allergens[] = allergensName($row_old_allergens_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_allergens_val){
                                $new_allergens[] = allergensName($row_new_allergens_val);
                            }
                            $old_data_value = implode(',',$old_allergens);
                            $new_data_value = implode(',',$new_allergens);
                        }

                        if($key == 'country_of_origin'){
                            $old_country_of_origin = [];
                            $new_country_of_origin = [];
                            foreach(explode(',',$old_data_value) as $row_old_country_of_origin_val){
                                $old_country_of_origin[] = countryName($row_old_country_of_origin_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_country_of_origin_val){
                                $new_country_of_origin[] = countryName($row_new_country_of_origin_val);
                            }
                            $old_data_value = implode(',',$old_country_of_origin);
                            $new_data_value = implode(',',$new_country_of_origin);
                        }

                        if($key == 'prop_65_ingredient'){
                            $old_prop_65_ingredient = [];
                            $new_prop_65_ingredient = [];
                            foreach(explode(',',$old_data_value) as $row_old_prop_65_ingredient_val){
                                $old_prop_65_ingredient[] = prop_65_name($row_old_prop_65_ingredient_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_prop_65_ingredient_val){
                                $new_prop_65_ingredient[] = prop_65_name($row_new_prop_65_ingredient_val);
                            }
                            $old_data_value = implode(',',$old_prop_65_ingredient);
                            $new_data_value = implode(',',$new_prop_65_ingredient);
                        }

                        if($key == 'etailer_availability'){
                            $old_etailer_availability = [];
                            $new_etailer_availability = [];
                            foreach(explode(',',$old_data_value) as $row_old_etailer_availability_val){
                                $old_etailer_availability[] = EtailerAvailabilityName($row_old_etailer_availability_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_etailer_availability_val){
                                $new_etailer_availability[] = EtailerAvailabilityName($row_new_etailer_availability_val);
                            }
                            $old_data_value = implode(',',$old_etailer_availability);
                            $new_data_value = implode(',',$new_etailer_availability);
                        }

                        if($key == 'supplier_status'){
                            $old_supplier_status = [];
                            $new_supplier_status = [];
                            foreach(explode(',',$old_data_value) as $row_old_supplier_status_val){
                                $old_supplier_status[] = SupplierStatus($row_old_supplier_status_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_supplier_status_val){
                                $new_supplier_status[] = SupplierStatus($row_new_supplier_status_val);
                            }
                            $old_data_value = implode(',',$old_supplier_status);
                            $new_data_value = implode(',',$new_supplier_status);
                        }

                        if($key == 'product_tags'){
                            $old_product_tags = [];
                            $new_product_tags = [];
                            foreach(explode(',',$old_data_value) as $row_old_product_tags_val){
                                $old_product_tags[] = producttageName($row_old_product_tags_val);
                            }

                            foreach(explode(',',$new_data_value) as $row_new_product_tags_val){
                                $new_product_tags[] = producttageName($row_new_product_tags_val);
                            }
                            $old_data_value = implode(',',$old_product_tags);
                            $new_data_value = implode(',',$new_product_tags);
                        }

                        for($i=1;$i<=9;$i++){
                            $name = 'product_subcategory'.$i;
                            if($key == $name){
                                $old_data_value = CategoryName($old_data_value);
                                $new_data_value = CategoryName($new_data_value);

                                $data[$key_new.' Old'] = $old_data_value;
                                $data[$key_new.' New'] = $new_data_value;
                            }
                        }

                        if($key == 'product_category'){
                            $old_data_value = CategoryName($old_data_value);
                            $new_data_value = CategoryName($new_data_value);
                        }



                        $data[$key_new.' Old'] = $old_data_value;
                        $data[$key_new.' New'] = $new_data_value;


                    }
                }
            }

            // dd($data);
            if(count($data)>0){
                $ProductHistory->response = json_encode($data);
                $master_product_id = $response['master_product_id'];
                $action = $response['action'];
                $ProductHistory->master_product_id = $master_product_id;
                $ProductHistory->action = $action;
                $ProductHistory->updated_by = Auth::user()->id;
                $ProductHistory->created_at = date('Y-m-d H:i:s');
                $ProductHistory->updated_at = date('Y-m-d H:i:s');
                $ProductHistory->save();
            }

        }

        if(isset($response['response'])){
            $data['response'] = $response['response'];
            $ProductHistory->response = json_encode($data);
            $master_product_id = $response['master_product_id'];
            $action = $response['action'];
            $ProductHistory->master_product_id = $master_product_id;
            $ProductHistory->action = $action;
            $ProductHistory->updated_by = Auth::user()->id;
            $ProductHistory->created_at = date('Y-m-d H:i:s');
            $ProductHistory->updated_at = date('Y-m-d H:i:s');
            $ProductHistory->save();
        }


        return true;
    }

    public function transferTickets($master_product_queue_id,$master_product_id){
        DB::table('product_tickets')->where('master_product_id',$master_product_queue_id)->where('product_type','master_product_queue')->update([
            'master_product_id' => $master_product_id,
            'product_type' => 'master_product'
        ]);

        return true;
    }

    public function FilterProducts($request){
        $Query = "";
        $SELECT = "master_product.*, users.name as username";


        $sql = "SELECT $SELECT FROM master_product
                LEFT JOIN users ON users.id =  master_product.updated_by
                WHERE master_product.is_approve = 1 $Query";
    }
    public function productTag($id)
    {
        $data =  DB::table('product_tags')->select(\DB::raw("GROUP_CONCAT(tag) as tags"))->whereIN('id',explode(',',$id))->first();
        return $data->tags;
    }
    public function productImage($ETIN)
    {
		//dd($ETIN);
        $data =  DB::table('master_product_images')->select(\DB::raw("GROUP_CONCAT(image_url) as imageUrls"))->whereIN('ETIN',explode(',',$ETIN))->first();
        return $data->imageUrls;
    }
	public function productImageFromProductImageTable($ETIN)
    {
		//dd($ETIN);
        $data =  DB::table('product_images')->where('ETIN',$ETIN)->first();
		if(!$data){
			$images = array(
				'image_URL1' => null,
				'image_URL2' => null,
				'image_URL3' => null,
				'image_URL4' => null,
				'image_URL5' => null,
				'image_URL6' => null,
				'image_URL7' => null,
				'image_URL8' => null,
				'image_URL9' => null,
				'image_URL10' => null
			);
		} else {
			$images = array(
				'image_URL1' => $data->Image_URL1_Primary,
				'image_URL2' => $data->Image_URL2_Front,
				'image_URL3' => $data->Image_URL3_Back,
				'image_URL4' => $data->Image_URL4_Left,
				'image_URL5' => $data->Image_URL5_Right,
				'image_URL6' => $data->Image_URL6_Top,
				'image_URL7' => $data->Image_URL7_Bottom,
				'image_URL8' => $data->Image_URL8,
				'image_URL9' => $data->Image_URL9,
				'image_URL10' => $data->Image_URL10
			);
		}
        return $images;
    }
	
	public function getChannelName($channel_id){
		$name = null;
		$channel_name =  ClientChannelConfiguration::where('id',$channel_id)->pluck('channel');
		if(isset($channel_name[0])){
			$name = $channel_name[0];
		}
		return $name;
	}

    public function kit_products(){
        return $this->hasMany('App\MasterProductKitComponents', 'ETIN', 'ETIN');
    }

    public function GetProductInventory($sub_order_number = null, $warehouse = null){

        $all_locations = $this->masterShelf;
        if(!$all_locations){
            return [
                'error' => true,
                'message' => 'No location assigned to product: '.$this->ETIN .($sub_order_number != null ? ' ('.$sub_order_number.')' : '')
            ];
        }

        if($all_locations){
            foreach($all_locations as $row_location){
                
                if($warehouse !== ''){
                    $order_warehouse = isset($row_location->ailse->warehouse_name->warehouses) ? $row_location->ailse->warehouse_name->warehouses : NULL;
                    if(($warehouse == $order_warehouse) && $row_location->location_type_id == 1){
                        return [
                            'error' => false,
                            'message' => 'Success',
                            'data' => $row_location->toArray()
                        ];
                    }
                }else{
                    if($row_location->location_type_id == 1){
                        return [
                            'error' => false,
                            'message' => 'Success',
                            'data' => $row_location->toArray()
                        ];
                    }
                }
                
            }
        }
        
        return [
            'error' => true,
            'message' => 'No pick location found for product: '.$this->ETIN .($sub_order_number != null ? ' ('.$sub_order_number.')' : '').' '.$warehouse
        ];
    }
    public function lobsName(){
        $lobsArray = explode(',',$this->lobs);
        $client = Client::whereIn('id',$lobsArray)->pluck('company_name')->toArray();
        $result = implode(',',$client);
        return $result;
    }
    public function productCategory(){
        return $this->belongsTo(ProductCategory::class,'product_category','id');
    }
    public function productSubCategory1(){
        return $this->belongsTo(ProductSubcategory::class,'product_subcategory1','id');
    }
    public function productSubCategory2(){
        return $this->belongsTo(ProductSubcategory::class,'product_subcategory2','id');
    }
    public function productSubCategory3(){
        return $this->belongsTo(ProductSubcategory::class,'product_subcategory3','id');
    }
    public function tagsName(){
        $tagsName = explode(',',$this->product_tags);
        $tag = ProductTags::whereIn('id',$tagsName)->pluck('tag')->toArray();
        $result = implode(',',$tag);
        return $result;
    }
    public function allergensName(){
        $allergensName = explode(',',$this->allergens);
        $allergens = Allergens::whereIn('id',$allergensName)->pluck('allergens')->toArray();
        $result = implode(',',$allergens);
        return $result;
    }
}
