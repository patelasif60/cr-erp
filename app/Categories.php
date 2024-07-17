<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'parent_id',
        'name',
        'sa_code',
        'level',
    ];

    public function CategoryFromTopToBottom($parent, $array = []){

        $query = Categories::where('parent_id',$parent);
        $result = $query->get()->toArray();
        if(!empty($result)){
            foreach($result as $key => $row_cat){
                $row_cat['nodes'] = $this->CategoryFromTopToBottom($row_cat['id']);
                $result[$key] = $row_cat;
            }
        }
        return $result;
    }

    public function GetCategoryParentHirarchy($cat_id,$array = []){
        $all_values = [];

        $cat_info = $this->GetCatInfo($cat_id);
        if($cat_info){
            $parent = $cat_info['parent_id'];

            array_unshift($array,$cat_info);
            if($parent > 0){
              $all_values = $this->GetCategoryParentHirarchy($parent,$array);
            }else{
                return $array;
            }
        }
        return $all_values;

    }

    public function GetCatInfo($cat_id,$lang = NULL){

        $query = Categories::where('id',$cat_id);
        $result = $query->first();
        if($result){
            return $result->toArray();
        }
    }

    public function GetCategoryList(){
		$category =  Category::orderBy('name')->pluck('name','id')->toArray();
		return $category;
	}
}
