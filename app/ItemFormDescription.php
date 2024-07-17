<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemFormDescription extends Model
{
    protected $table = 'item_from_description';

    protected $fillable = [
        'item_desc',
    ];

    public function itemFormDescList(){
        $result = ItemFormDescription::get();
        	 
		 foreach ($result as $itemsdescs){		 
			 $itemsdesc[] = $itemsdescs->item_desc;			 
		 }
		 return $itemsdesc;
    }


}
