<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BackStockPalletItem;
use App\RestockItem;

class Restock extends Model
{
    protected $table = 'restock';

    public function RestockItem(){
        return $this->hasMany(BackStockPalletItem::class,'restock_id');
    }
    public function restockItemwithETIN(){
        return $this->hasMany(RestockItem::class,'restock_id');
    }
}
