<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CountryOfOrigin extends Model
{
    protected $table = 'country_of_origin';
    protected $fillable = [
        'country_of_origin'
    ];

    public function CountryList(){
        $CountryList = CountryOfOrigin::orderBy('country_of_origin','ASC')->get();
        foreach ($CountryList as $countries){		 
			 $country[] = $countries->country_of_origin;			 
		 }
		 return $country;
    }	 
		 
}
