<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestockItem extends Model
{
    protected $table = 'restock_items';

    public function products(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
    
}
