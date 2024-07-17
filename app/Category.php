<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SubCategory;

class Category extends Model
{
    // protected $table = 'product_category';

    // public function GetCategoryHierarchy(){
    //     $all_categories = Category::orderBy('product_category','ASC')->get();
    //     $result = [];
    //     if($all_categories){
    //         foreach($all_categories as $key => $row){
    //             $check_if_sub_category_1 = SubCategory::where('product_category_id',$row->id)->groupBy('sub_category_1')->get();
    //             if($check_if_sub_category_1){
    //                 foreach($check_if_sub_category_1 as $row_sub_cat_1){
    //                     $check_if_sub_category_2 = SubCategory::where('sub_category_1',$row_sub_cat_1->sub_category_1)->groupBy('sub_category_2')->get();
    //                     if($check_if_sub_category_2){
    //                         foreach($check_if_sub_category_2 as $row_sub_cat_2){
    //                             $check_if_sub_category_3 = SubCategory::where('sub_category_2',$row_sub_cat_2->sub_category_2)->groupBy('sub_category_3')->get();
    //                             $row_sub_cat_2->node = $check_if_sub_category_3;
    //                         }
    //                         $row_sub_cat_1->node = $check_if_sub_category_2;
    //                     }

    //                 }
    //                 $row->node = $check_if_sub_category_1;
    //             }
    //         }
    //     }
    //     return $all_categories;

    // }

	// public function GetCategoryList(){
	// 	$getcategory =  Category::orderBy('product_category')->get();
	// 	foreach ($getcategory as $categorys){
	// 		$categoryid[] = $categorys->id;
	// 		$categoryname[] = $categorys->product_category;
	// 		$category = array_combine( $categoryid, $categoryname );
	// 	}
	// 	return $category;
	// }
}
