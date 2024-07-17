<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTags extends Model
{
    protected $table = 'product_tags';
    protected $fillable = [
        'tag',
        'internal_external_flag'
    ];

    public function ProductTagsList(){
        $ProductTagsList = ProductTags::get();
		foreach ($ProductTagsList as $producttags){		 
			 $producttag[] = $producttags->tag;			 
		 }
        return $producttag;
    } 
		 
}
