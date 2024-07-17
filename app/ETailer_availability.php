<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ETailer_availability extends Model
{
    protected $table = 'etailer_availability';

    protected $fillable = [
        'etailer_availability',
    ];

    public function etailerList(){
        $result = ETailer_availability::get();
        foreach ($result as $etailerlist){		 
			 $etailers[] = $etailerlist->etailer_availability;			 
		 }
		 return $etailers;
    }
}
