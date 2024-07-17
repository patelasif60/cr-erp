<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LastETIN;

class MasterProductQueue extends Model
{
    protected $table = 'master_product_queue';
    protected $fillable = [
        'ETIN',
        'status'
    ];

    public function getETIN($product_temp = NULL){
        $ETIN = '';
        $last = NULL;
        $middle = NULL;

        $last_ETIN = LastETIN::latest('id')->first();
        // dd($last_ETIN);

        if($last_ETIN){
            $lastrec_master_etin_array = explode('-',$last_ETIN->last_etin);

            if($lastrec_master_etin_array[2] == 9999){
                $middle = (int)$lastrec_master_etin_array[1];
                $middle++;
                $last = 0001;
            }else{
                $middle = (int)$lastrec_master_etin_array[1];
                $last = (int)$lastrec_master_etin_array[2];
                $last++;
            }
        }
        else{
            $last = 0001;
            $middle = 1000;
            $ETIN = 'ETFZ-1000-0001';
        }

        $first_part = 'ETOT';

        if($product_temp){
            if($product_temp == "Frozen"){
                $first_part = 'ETFZ';
            } else if($product_temp == "Dry-Strong"){
                $first_part = 'ETDS';
            } else if($product_temp == "Refrigerated"){
                $first_part = 'ETRF';
            } else if($product_temp == "Beverages"){
                $first_part = 'ETBV';
            }  else if($product_temp == "Dry-Perishable"){
                $first_part = 'ETDP';
            }  else if($product_temp == "Dry-Fragile"){
                $first_part = 'ETDF';
            }  else if($product_temp == "Thaw & Serv"){
                $first_part = 'ETTS';
            }  else {
                $first_part = 'ETOT';
            }
        }
        if($last_ETIN){
            $last_ETIN->last_etin = $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
            $last_ETIN->save();
        }else{
            $last_ETIN = new LastETIN;
            $last_ETIN->last_etin = $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
            $last_ETIN->save();
        }


        return $first_part.'-'.str_pad($middle, 4, '0', STR_PAD_LEFT).'-'.str_pad($last, 4, '0', STR_PAD_LEFT);
    }
}
