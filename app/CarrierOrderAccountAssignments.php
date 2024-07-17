<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Carrier;
use App\Client;


class CarrierOrderAccountAssignments extends Model
{
    protected $table = 'carrier_order_account_assignments';

    public function Client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }
    
    public function dry_wi_carrier_name(){
        return $this->belongsTo(Carrier::class,'dry_wi_carrier_id','id');
    }

    public function dry_wi_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'dry_wi_account_id','id');
    }

    public function dry_nv_carrier_name(){
        return $this->belongsTo(Carrier::class,'dry_nv_carrier_id','id');
    }

    public function dry_nv_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'dry_nv_account_id','id');
    }

    public function dry_ok_carrier_name(){
        return $this->belongsTo(Carrier::class,'dry_ok_carrier_id','id');
    }

    public function dry_ok_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'dry_ok_account_id','id');
    }

    public function dry_pa_carrier_name(){
        return $this->belongsTo(Carrier::class,'dry_pa_carrier_id','id');
    }

    public function dry_pa_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'dry_pa_account_id','id');
    }



    public function frozen_wi_carrier_name(){
        return $this->belongsTo(Carrier::class,'frozen_wi_carrier_id','id');
    }

    public function frozen_wi_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'frozen_wi_account_id','id');
    }

    public function frozen_nv_carrier_name(){
        return $this->belongsTo(Carrier::class,'frozen_nv_carrier_id','id');
    }

    public function frozen_nv_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'frozen_nv_account_id','id');
    }

    public function frozen_ok_carrier_name(){
        return $this->belongsTo(Carrier::class,'frozen_ok_carrier_id','id');
    }

    public function frozen_ok_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'frozen_ok_account_id','id');
    }

    public function frozen_pa_carrier_name(){
        return $this->belongsTo(Carrier::class,'frozen_pa_carrier_id','id');
    }

    public function frozen_pa_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'frozen_pa_account_id','id');
    }


    public function refrigerated_wi_carrier_name(){
        return $this->belongsTo(Carrier::class,'refrigerated_wi_carrier_id','id');
    }

    public function refrigerated_wi_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'refrigerated_wi_account_id','id');
    }

    public function refrigerated_nv_carrier_name(){
        return $this->belongsTo(Carrier::class,'refrigerated_nv_carrier_id','id');
    }

    public function refrigerated_nv_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'refrigerated_nv_account_id','id');
    }

    public function refrigerated_ok_carrier_name(){
        return $this->belongsTo(Carrier::class,'refrigerated_ok_carrier_id','id');
    }

    public function refrigerated_ok_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'refrigerated_ok_account_id','id');
    }

    public function refrigerated_pa_carrier_name(){
        return $this->belongsTo(Carrier::class,'refrigerated_pa_carrier_id','id');
    }

    public function refrigerated_pa_account_name(){
        return $this->belongsTo(CarrierAccounts::class,'refrigerated_pa_account_id','id');
    }
}
