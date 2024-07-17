<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitDescription extends Model
{
    protected $table = 'unit_desc';
    protected $fillable = [
        'unit_description'
    ];

    public function UnitDescriptionList(){
        $UnitDescriptionList = UnitDescription::get();	 
		 foreach ($UnitDescriptionList as $unitdescs){		 
			 $unitdesc[] = $unitdescs->unit_description;			 
		 }
		 return $unitdesc;
    }
}
