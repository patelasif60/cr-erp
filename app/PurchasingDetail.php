<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MasterProduct;
use App\WareHouse;
use App\Client;

class PurchasingDetail extends Model
{
    protected $table = 'purchasing_details';

    protected $fillable = ['supplier_id', 'po', 'etin', 'qty_ordered', 'qty_fulfilled', 'qty_received', 'qty_damaged', 
                            'qty_missing', 'asn_bol_shipped_qty','bol_number','status','reference','reference_date'];

    public function product(){
        return $this->belongsTo(MasterProduct::class,'etin','ETIN');
    }

    public function exp_and_lot(){
        return $this->hasMany(PurchaseOrderExpAndLot::class,'pd_id','id');
    }

    
    public function masterProduct(){
        return $this->belongsTo(MasterProduct::class,'etin','ETIN');
    }
    public function client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }
    public function warehouse(){
        return $this->belongsTo(WareHouse::class,'warehouse_id','id');
    }
}
