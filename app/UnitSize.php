<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitSize extends Model
{
    protected $table = "unit_sizes";
    protected $fillable = [
        'unit',
        'abbreviation'
    ];

    public function UnitSizeList(){
        $UnitSize = UnitSize::get();
		foreach ($UnitSize as $unitsizes){		 
			 $unitname[] = $unitsizes->unit;			 
			 $unitabb[] = $unitsizes->abbreviation;
			$unitsizelist = array_combine( $unitabb, $unitname );
		 }
        return $unitsizelist;
    }
	
	public function getUnitOfContent($abbr){
		$unitOfContent = UnitSize::select('unit_of_content')->where('abbreviation',$abbr)->first();
		return $unitOfContent;
	}
}
