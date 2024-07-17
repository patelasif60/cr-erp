<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTemperature extends Model
{
    protected $table = 'product_temp';
    protected $fillable = [
        'product_temperature'
    ];

    public function ProductTemperatureList(){
        $ProductTemperatureList = ProductTemperature::get();
		foreach ($ProductTemperatureList as $ProductTemperature){		 
			 $producttemp[] = $ProductTemperature->product_temperature;			 
		 }
        return $producttemp;
    }
}
