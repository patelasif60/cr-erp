<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BackStockPalletItem extends Model
{
    protected $table = 'backstock_pallet_items';

    public function products(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
    
}
