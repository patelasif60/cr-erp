<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\MasterProduct;
use App\MasterProductQueue;
use App\Brand;
use App\Supplier;
use App\ProductType;
use App\ItemFormDescription;
use App\UnitDescription;
use App\ETailer_availability;
use App\ProductTemperature;
use App\Manufacturer;
use App\SupplierStatus;
use App\CountryOfOrigin;
use App\ImageType;
use App\ProductTags;
use App\PropIngredients;
use App\Allergens;
use App\Categories;

class MasterProductUpdateScriptsController extends Controller
{
    //Brand
    public function update_mp_brand_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->brand) && !empty($master_product->brand)){
                $brand = Brand::where('brand',$master_product->brand)->first();
                if($brand){
                    MasterProduct::where('id',$master_product->id)->update(['brand' => $brand->id]);
                }
            }
        }
        dd("Master Product brand Column Updated");
    }

    //Supplier
    public function update_mp_supplier_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->current_supplier) && !empty($master_product->current_supplier)){
                $current_supplier = Supplier::where('name',$master_product->current_supplier)->first();
                if($current_supplier){
                    MasterProduct::where('id',$master_product->id)->update(['current_supplier' => $current_supplier->id]);
                }
            }
        }
        dd("Master Product current_supplier Column Updated");
    }

    //Item Form Description
    public function update_mp_item_form_desc_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->item_form_description) && !empty($master_product->item_form_description)){
                $item_form_description = ItemFormDescription::where('item_desc',$master_product->item_form_description)->first();
                if($item_form_description){
                    MasterProduct::where('id',$master_product->id)->update(['item_form_description' => $item_form_description->id]);
                }
            }
        }
        dd("Master Product item_form_description Column Updated");
    }

    //Product Type
    public function update_mp_product_type_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->product_type) && !empty($master_product->product_type)){
                $product_type = ProductType::where('product_type',$master_product->product_type)->first();
                if($product_type){
                    MasterProduct::where('id',$master_product->id)->update(['product_type' => $product_type->id]);
                }
            }
        }
        dd("Master Product product_type Column Updated");
    }

    //Unit Description
    public function update_mp_unit_desc_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->unit_description) && !empty($master_product->unit_description)){
                $unit_description = UnitDescription::where('unit_description',$master_product->unit_description)->first();
                if($unit_description){
                    MasterProduct::where('id',$master_product->id)->update(['unit_description' => $unit_description->id]);
                }
            }
        }
        dd("Master Product unit_description Column Updated");
    }

    //e-tailor availability
    public function update_mp_etailer_availability_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->etailer_availability) && !empty($master_product->etailer_availability)){
                $etailer_availability = ETailer_availability::where('etailer_availability',$master_product->etailer_availability)->first();
                if($etailer_availability){
                    MasterProduct::where('id',$master_product->id)->update(['etailer_availability' => $etailer_availability->id]);
                }
            }
        }
        dd("Master Product etailer_availability Column Updated");
    }

    //product_temperature
    public function update_mp_product_temperature_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->product_temperature) && !empty($master_product->product_temperature)){
                $product_temperature = ProductTemperature::where('product_temperature',$master_product->product_temperature)->first();
                if($product_temperature){
                    MasterProduct::where('id',$master_product->id)->update(['product_temperature' => $product_temperature->id]);
                }
            }
        }
        dd("Master Product product_temperature Column Updated");
    }

    //manufacturer
    public function update_mp_manufacturer_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->manufacturer) && !empty($master_product->manufacturer)){
                $manufacturer = Manufacturer::where('manufacturer_name',$master_product->manufacturer)->first();
                if($manufacturer){
                    MasterProduct::where('id',$master_product->id)->update(['manufacturer' => $manufacturer->id]);
                }
            }
        }
        dd("Master Product manufacturer Column Updated");
    }
    
    //supplier_status
    public function update_mp_supplier_status_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->supplier_status) && !empty($master_product->supplier_status)){
                $supplier_status = SupplierStatus::where('supplier_status',$master_product->supplier_status)->first();
                if($supplier_status){
                    MasterProduct::where('id',$master_product->id)->update(['supplier_status' => $supplier_status->id]);
                }
            }
        }
        dd("Master Product supplier_status Column Updated");
    }

    //country_of_origin
    public function update_mp_country_of_origin_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->country_of_origin) && !empty($master_product->country_of_origin)){
                $country_of_origin = CountryOfOrigin::where('country_of_origin',$master_product->country_of_origin)->first();
                if($country_of_origin){
                    MasterProduct::where('id',$master_product->id)->update(['country_of_origin' => $country_of_origin->id]);
                }
            }
        }
        dd("Master Product country_of_origin Column Updated");
    }

    //image_type
    public function update_mp_image_type_column(){
        foreach(DB::table('master_product_images')->get() as $master_product){
            if(isset($master_product->image_type) && !empty($master_product->image_type)){
                $image_type = ImageType::where('image_type',$master_product->image_type)->first();
                if($image_type){
                    DB::table('master_product_images')->where('id',$master_product->id)->update(['image_type' => $image_type->id]);
                }
            }
        }
        dd("Master Product image_type Column Updated");
    }
    
    //product_tags
    public function update_mp_product_tags_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->product_tags) && !empty($master_product->product_tags)){
                $tag_array = [];
                $product_tags_array = explode(',',$master_product->product_tags);
                foreach($product_tags_array as $tag){
                    $product_tags = ProductTags::where('tag',$tag)->first();
                    if($product_tags){
                        array_push($tag_array,$product_tags->id);
                    }
                }
                if(!empty($tag_array)){
                    $tag_string = implode(',',$tag_array);
                    MasterProduct::where('id',$master_product->id)->update(['product_tags' => $tag_string]);
                }
            }
        }
        dd("Master Product product_tags Column Updated");
    }

    //lobs   
    public function update_mp_lobs_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->lobs) && !empty($master_product->lobs)){
                $lobs_array = [];
                $product_lobs_array = explode(',',$master_product->lobs);
                foreach($product_lobs_array as $lobs){
                    $lobs = DB::table('clients')->where('company_name',$lobs)->first();
                    if($lobs){
                        array_push($lobs_array,$lobs->id);
                    }
                }
                if(!empty($lobs_array)){
                    $lobs_string = implode(',',$lobs_array);
                    MasterProduct::where('id',$master_product->id)->update(['lobs' => $lobs_string]);
                }
            }
        }
        dd("Master Product lobs Column Updated");
    }

    //prop_65_ingredient
    public function update_mp_prop_65_ingredient_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->prop_65_ingredient) && !empty($master_product->prop_65_ingredient)){
                $prop_array = [];
                $prop_65_ingredient_array = explode(',',$master_product->prop_65_ingredient);
                foreach($prop_65_ingredient_array as $prop){
                    $prop_65_ingredient = PropIngredients::where('prop_ingredients',$prop)->first();
                    if($prop_65_ingredient){
                        array_push($prop_array,$prop_65_ingredient->id);
                    }
                }
                if(!empty($prop_array)){
                    $prop_string = implode(',',$prop_array);
                    MasterProduct::where('id',$master_product->id)->update(['prop_65_ingredient' => $prop_string]);
                }
            }
        }
        dd("Master Product prop_65_ingredient Column Updated");
    }
    
    //allergens
    public function update_mp_allergens_column(){
        foreach(MasterProduct::all() as $master_product){
            if(isset($master_product->allergens) && !empty($master_product->allergens)){
                $Allergens_array = [];
                $pro_allergens_array = explode(',',$master_product->allergens);
                foreach($pro_allergens_array as $Allergens){
                    $allergens = Allergens::where('allergens',$Allergens)->first();
                    if($allergens){
                        array_push($Allergens_array,$allergens->id);
                    }
                }
                if(!empty($Allergens_array)){
                    $Allergens_string = implode(',',$Allergens_array);
                    MasterProduct::where('id',$master_product->id)->update(['allergens' => $Allergens_string]);
                }
            }
        }
        dd("Master Product allergens Column Updated");
    }

    //-------------------------------------------------------Master Product Queue--------------------------------------------------------------------

    //allergens
    public function update_mpq_allergens_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->allergens) && !empty($master_product->allergens)){
                $Allergens_array = [];
                $pro_allergens_array = explode(',',$master_product->allergens);
                foreach($pro_allergens_array as $Allergens){
                    $allergens = Allergens::where('allergens',$Allergens)->first();
                    if($allergens){
                        array_push($Allergens_array,$allergens->id);
                    }
                }
                if(!empty($Allergens_array)){
                    $Allergens_string = implode(',',$Allergens_array);
                    MasterProductQueue::where('id',$master_product->id)->update(['allergens' => $Allergens_string]);
                }
            }
        }
        dd("Master Product Queue allergens Column Updated");
    }

    //country_of_origin
    public function update_mpq_country_of_origin_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->country_of_origin) && !empty($master_product->country_of_origin)){
                $country_of_origin = CountryOfOrigin::where('country_of_origin',$master_product->country_of_origin)->first();
                if($country_of_origin){
                    MasterProductQueue::where('id',$master_product->id)->update(['country_of_origin' => $country_of_origin->id]);
                }
            }
        }
        dd("Master Product Queue country_of_origin Column Updated");
    }

    //prop_65_ingredient
    public function update_mpq_prop_65_ingredient_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->prop_65_ingredient) && !empty($master_product->prop_65_ingredient)){
                $prop_array = [];
                $prop_65_ingredient_array = explode(',',$master_product->prop_65_ingredient);
                foreach($prop_65_ingredient_array as $prop){
                    $prop_65_ingredient = PropIngredients::where('prop_ingredients',$prop)->first();
                    if($prop_65_ingredient){
                        array_push($prop_array,$prop_65_ingredient->id);
                    }
                }
                if(!empty($prop_array)){
                    $prop_string = implode(',',$prop_array);
                    MasterProductQueue::where('id',$master_product->id)->update(['prop_65_ingredient' => $prop_string]);
                }
            }
        }
        dd("Master Product Queue prop_65_ingredient Column Updated");
    }
    //e-tailor availability
    public function update_mpq_etailer_availability_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->etailer_availability) && !empty($master_product->etailer_availability)){
                $etailer_availability = ETailer_availability::where('etailer_availability',$master_product->etailer_availability)->first();
                if($etailer_availability){
                    MasterProductQueue::where('id',$master_product->id)->update(['etailer_availability' => $etailer_availability->id]);
                }
            }
        }
        dd("Master Product Queue etailer_availability Column Updated");
    }

    //supplier_status
    public function update_mpq_supplier_status_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->supplier_status) && !empty($master_product->supplier_status)){
                $supplier_status = SupplierStatus::where('supplier_status',$master_product->supplier_status)->first();
                if($supplier_status){
                    MasterProductQueue::where('id',$master_product->id)->update(['supplier_status' => $supplier_status->id]);
                }
            }
        }
        dd("Master Product Queue supplier_status Column Updated");
    }

    //product_tags
    public function update_mpq_product_tags_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->product_tags) && !empty($master_product->product_tags)){
                $tag_array = [];
                $product_tags_array = explode(',',$master_product->product_tags);
                foreach($product_tags_array as $tag){
                    $product_tags = ProductTags::where('tag',$tag)->first();
                    if($product_tags){
                        array_push($tag_array,$product_tags->id);
                    }
                }
                if(!empty($tag_array)){
                    $tag_string = implode(',',$tag_array);
                    MasterProductQueue::where('id',$master_product->id)->update(['product_tags' => $tag_string]);
                }
            }
        }
        dd("Master Product Queue product_tags Column Updated");
    }
    //lobs   
    public function update_mpq_lobs_column(){
        foreach(MasterProductQueue::all() as $master_product){
            if(isset($master_product->lobs) && !empty($master_product->lobs)){
                $lobs_array = [];
                $product_lobs_array = explode(',',$master_product->lobs);
                foreach($product_lobs_array as $lobs){
                    $lobs = DB::table('clients')->where('company_name',$lobs)->first();
                    if($lobs){
                        array_push($lobs_array,$lobs->id);
                    }
                }
                if(!empty($lobs_array)){
                    $lobs_string = implode(',',$lobs_array);
                    MasterProductQueue::where('id',$master_product->id)->update(['lobs' => $lobs_string]);
                }
            }
        }
        dd("Master Product Queue lobs Column Updated");
    }

    public function create_categories(){
        $result = DB::table('product_category')->get();
        if($result){
            foreach($result as $row){
                $Categories = new Categories();
                $Categories->name = $row->product_category;
                $Categories->sa_code = $row->sa_code;
                $Categories->level = 0;
                $Categories->save();
            }
        }
        dump("Done");
    }

    public function create_sub_category_1(){
        $result = DB::table('product_subcategory')->where('sub_category_1','!=','')->groupBy('sub_category_1')->get();
        if($result){
            foreach($result as $row){
                $get_cat = DB::table('product_category')->where('id',$row->product_category_id)->first();
                
                if($get_cat){
                    $parent = DB::table('categories')->where('name',$get_cat->product_category)->first();
                    if($parent){
                        $Categories = new Categories();
                        $Categories->name = $row->sub_category_1;
                        $Categories->sa_code = $row->sc1_sa_code;
                        $Categories->level = 1;
                        $Categories->parent_id = $parent->id;
                        $Categories->save();
                    }
                }

            }
        }
        dump("Done");
    }

    public function create_sub_category_2(){
        $result = DB::table('product_subcategory')->where('sub_category_2','!=','')->groupBy('sub_category_2')->get();
        if($result){
            foreach($result as $row){
                $get_cat = DB::table('product_category')->where('id',$row->product_category_id)->first();
                if($get_cat){
                    $parent = DB::table('categories')->where('name',$get_cat->product_category)->where('level',0)->first();
                    $parent_1 = DB::table('categories')->where('name',$row->sub_category_1)->where('level',1)->first();
                    // dump($parent);
                    // dump($parent_1);
                    // dump('-----');
                    if($parent && $parent_1){
                        $Categories = new Categories();
                        $Categories->name = $row->sub_category_2;
                        $Categories->sa_code = $row->sc2_sa_code;
                        $Categories->level = 2;
                        $Categories->parent_id = $parent_1->id;
                        $Categories->save();
                    }
                }

            }
        }
        dump("Done");
    }

    public function create_sub_category_3(){
        $result = DB::table('product_subcategory')->where('sub_category_3','!=','')->groupBy('sub_category_3')->get();
        if($result){
            foreach($result as $row){
                $get_cat = DB::table('product_category')->where('id',$row->product_category_id)->first();
                if($get_cat){
                    $parent = DB::table('categories')->where('name',$get_cat->product_category)->where('level',0)->first();
                    $parent_1 = DB::table('categories')->where('name',$row->sub_category_1)->where('level',1)->first();
                    $parent_2 = DB::table('categories')->where('name',$row->sub_category_2)->where('level',2)->first();
                    // dump($parent);
                    // dump($parent_1);
                    // dump($parent_2);
                    // dump('-----');
                    if($parent && $parent_1){
                        $Categories = new Categories();
                        $Categories->name = $row->sub_category_3;
                        $Categories->sa_code = $row->sc3_sa_code;
                        $Categories->level = 3;
                        $Categories->parent_id = $parent_2->id;
                        $Categories->save();
                    }
                }

            }
        }
        dump("Done");
    }

    //transfer
    public function update_mp_product_categories_column_transfer(){
       foreach(MasterProduct::all() as $master_product){
            if($master_product->product_category != NULL){
                $product_categories = DB::table('product_category')->find($master_product->product_category);
                if($product_categories && $product_categories->sa_code != NULL){
                    $category = DB::table('categories')->where('level',0)->where('sa_code',$product_categories->sa_code)->first();
                    if($category){
                        MasterProduct::where('id',$master_product->id)->update([
                            'product_category' => $category->id,
                        ]);
                    }
                }
            }
       }
       dd('done');
    }

    public function update_mp_product_subcategories_1_column_transfer(){
        foreach(MasterProduct::all() as $master_product){
             if($master_product->product_subcategory1 != NULL){
                 $product_subcategory = DB::table('product_subcategory')->where('sub_category_1',$master_product->product_subcategory1)->first();
                 if($product_subcategory && $product_subcategory->sc1_sa_code != NULL){
                     $category = DB::table('categories')->where('level',1)->where('sa_code',$product_subcategory->sc1_sa_code)->first();
                     if($category){
                         MasterProduct::where('id',$master_product->id)->update([
                             'product_subcategory1' => $category->id,
                         ]);
                     }
                 }
             }
        }
        dd('done');
     }

     public function update_mp_product_subcategories_2_column_transfer(){
        foreach(MasterProduct::all() as $master_product){
             if($master_product->product_subcategory2 != NULL){
                 $product_subcategory = DB::table('product_subcategory')->where('sub_category_2',$master_product->product_subcategory2)->first();
                 if($product_subcategory && $product_subcategory->sc2_sa_code != NULL){
                     $category = DB::table('categories')->where('level',2)->where('sa_code',$product_subcategory->sc2_sa_code)->first();
                     if($category){
                         MasterProduct::where('id',$master_product->id)->update([
                             'product_subcategory2' => $category->id,
                         ]);
                     }
                 }
             }
        }
        dd('done');
     }

     public function update_mp_product_subcategories_3_column_transfer(){
        foreach(MasterProduct::all() as $master_product){
             if($master_product->product_subcategory3 != NULL){
                 $product_subcategory = DB::table('product_subcategory')->where('sub_category_3',$master_product->product_subcategory3)->first();
                 if($product_subcategory && $product_subcategory->sc3_sa_code != NULL){
                     $category = DB::table('categories')->where('level',3)->where('sa_code',$product_subcategory->sc3_sa_code)->first();
                     if($category){
                         MasterProduct::where('id',$master_product->id)->update([
                             'product_subcategory3' => $category->id,
                         ]);
                     }
                 }
             }
        }
        dd('done');
     }

     //queue table

     public function update_mpq_product_categories_column_transfer(){
        foreach(MasterProductQueue::all() as $master_product){
             if($master_product->product_category != NULL){
                 $product_categories = DB::table('product_category')->find($master_product->product_category);
                 if($product_categories && $product_categories->sa_code != NULL){
                     $category = DB::table('categories')->where('level',0)->where('sa_code',$product_categories->sa_code)->first();
                     if($category){
                         MasterProductQueue::where('id',$master_product->id)->update([
                             'product_category' => $category->id,
                         ]);
                     }
                 }
             }
        }
        dd('done');
     }
 
     public function update_mpq_product_subcategories_1_column_transfer(){
         foreach(MasterProductQueue::all() as $master_product){
              if($master_product->product_subcategory1 != NULL){
                  $product_subcategory = DB::table('product_subcategory')->where('sub_category_1',$master_product->product_subcategory1)->first();
                  if($product_subcategory && $product_subcategory->sc1_sa_code != NULL){
                      $category = DB::table('categories')->where('level',1)->where('sa_code',$product_subcategory->sc1_sa_code)->first();
                      if($category){
                          MasterProductQueue::where('id',$master_product->id)->update([
                              'product_subcategory1' => $category->id,
                          ]);
                      }
                  }
              }
         }
         dd('done');
      }
 
      public function update_mpq_product_subcategories_2_column_transfer(){
         foreach(MasterProductQueue::all() as $master_product){
              if($master_product->product_subcategory2 != NULL){
                  $product_subcategory = DB::table('product_subcategory')->where('sub_category_2',$master_product->product_subcategory2)->first();
                  if($product_subcategory && $product_subcategory->sc2_sa_code != NULL){
                      $category = DB::table('categories')->where('level',2)->where('sa_code',$product_subcategory->sc2_sa_code)->first();
                      if($category){
                          MasterProductQueue::where('id',$master_product->id)->update([
                              'product_subcategory2' => $category->id,
                          ]);
                      }
                  }
              }
         }
         dd('done');
      }
 
      public function update_mpq_product_subcategories_3_column_transfer(){
         foreach(MasterProductQueue::all() as $master_product){
              if($master_product->product_subcategory3 != NULL){
                  $product_subcategory = DB::table('product_subcategory')->where('sub_category_3',$master_product->product_subcategory3)->first();
                  if($product_subcategory && $product_subcategory->sc3_sa_code != NULL){
                      $category = DB::table('categories')->where('level',3)->where('sa_code',$product_subcategory->sc3_sa_code)->first();
                      if($category){
                          MasterProductQueue::where('id',$master_product->id)->update([
                              'product_subcategory3' => $category->id,
                          ]);
                      }
                  }
              }
         }
         dd('done');
      }
}