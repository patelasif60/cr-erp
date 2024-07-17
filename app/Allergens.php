<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allergens extends Model
{
    protected $table = 'allergens';

    protected $fillable = [
        'allergens',
    ];

    public function allergensList(){
        $result = Allergens::orderBy('allergens')->get();
        foreach ($result as $row_allergens){		 
			 $allergens[] = $row_allergens->allergens;			 
		 }
		 return $allergens;
    }
}