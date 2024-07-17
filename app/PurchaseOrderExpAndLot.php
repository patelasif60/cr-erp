<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderExpAndLot extends Model
{
    protected $table = 'purchase_order_exp_and_lot';

    public function GetReceivedLot($input){
        $bol_number = isset($input['bol_number']) ? $input['bol_number'] : NULL;
        $lot = isset($input['lot']) ? $input['lot'] : NULL;
        $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
        $total = 0;
        if($lot != '' && $bol_number != '' && $ETIN != ''){
            $put_away_qty = PutAway::where('etin',$ETIN)->where('bol_number',$bol_number)->where('lot',$lot)->where('transfered',1)->sum('quantity');
            $BPallet = BackStockPallet::where('bol_number',$bol_number)->whereNotNull('address')->pluck('id');
            $pt = BackStockPalletItem::where('ETIN',$ETIN)->whereIN('backstock_pallet_id',$BPallet)->where('lot',$lot)->sum('quantity');
            $total = $put_away_qty + $pt;
        }

        return $total;
    }
    
}
