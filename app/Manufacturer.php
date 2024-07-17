<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $table = 'manufacturer';

    protected $fillable = [
        'manufacturer_name',
    ];

    public function manufacturerList(){
        $result = Manufacturer::orderBy('manufacturer_name')->get();
        foreach ($result as $manufacturers){		 
			$manufacturer[] = $manufacturers->manufacturer_name;		 
		}
		return $manufacturer;
    }
		 
		
}
