<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\BackStockPalletItem;

class BackStockPallet extends Model
{
    protected $table = 'backstock_pallet';

    public function BackstockPalletItem(){
        return $this->hasMany(BackStockPalletItem::class,'backstock_pallet_id');
    }

    // public function PurchasingSummery(){
    //     return $this->hasOne(PurchasingDetail::class,'bol_number','bol_number')->latestOfMany();
    // }
}
