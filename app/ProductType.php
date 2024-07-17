<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'product_type';

    protected $fillable = [
        'product_type',
    ];

    public function productTypeList(){
        $result = ProductType::get();
        foreach ($result as $producttypes){		 
			 $productTypeList[] = $producttypes->product_type;			 
		 }
		 return $productTypeList;
    }
}
