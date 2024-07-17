<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterShelfLotAndExpiry extends Model
{
    protected $table = 'master_shelf_lot_and_expiry';

    public function GetMasterShelfLotAndExp($input){
        $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
        $address = isset($input['address']) ? $input['address'] : NULL;
        $lot = isset($input['lot']) ? $input['lot'] : NULL;

        if($ETIN != '' && $address != '' && $lot != ''){
            $MSLE = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->where('lot',$lot)->first();
            if($MSLE){
                return $MSLE->toArray();
            }
        }

        return false;
    }

    public function GetMasterShelfLotAndExpOfETINAndAddress($input){
        $ETIN = isset($input['ETIN']) ? $input['ETIN'] : NULL;
        $address = isset($input['address']) ? $input['address'] : NULL;
        

        if($ETIN != '' && $address != ''){
            $MSLE = MasterShelfLotAndExpiry::where('ETIN',$ETIN)->where('address',$address)->orderBy('exp_date')->get();
            if($MSLE){
                return $MSLE->toArray();
            }
        }

        return [];
    }
}
