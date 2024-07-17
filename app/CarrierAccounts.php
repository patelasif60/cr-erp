<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Carrier;


class CarrierAccounts extends Model
{
    protected $table = 'carrier_accounts';
    protected $fillable = [
        'description',
        'carrier_id',
        'account_number',
        'api_key',
        'account_rules'
    ];
    public function carrier(){
        return $this->belongsTo(Carrier::class,'carrier_id','id');
    }
}
