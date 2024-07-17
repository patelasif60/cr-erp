<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PropIngredients extends Model
{
    protected $table = 'prop_ingredients';
    protected $fillable = [
        'prop_ingredients'
    ];

    public function PropIngredientsList(){
        $PropIngredients = PropIngredients::orderBy('prop_ingredients')->get();
        foreach ($PropIngredients as $prop_ingredientsget){
			$prop_ingredients[] = $prop_ingredientsget->prop_ingredients;		 
		}
		return $prop_ingredients;
    }
}
