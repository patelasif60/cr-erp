<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Manufacturer;

class Brand extends Model
{
    protected $table = 'brand';

    protected $fillable = [
        'brand',
        'manufacturer_id',
    ];

    public function brandList(){
        $result = Brand::orderBy('brand')->groupBy('brand')->get();
        foreach ($result as $brands){
			 $brand[] = $brands->brand;
		}
		return $brand;
    }

		 	
			
    public function manufacturer(){
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

   

}
