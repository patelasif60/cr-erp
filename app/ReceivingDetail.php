<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PurchasingDetail;

class ReceivingDetail extends Model
{
    protected $table = 'receiving_details';

    protected $fillable = [
        'supplier_id', 
        'po', 
        'etin', 
        'damaged_sattaled_location',
        'damaged_sattaled',
        'qty_ordered', 
        'qty_fulfilled', 
        'qty_received', 
        'qty_damaged', 
        'qty_missing', 
        'summary_id', 
        'warehouse_id', 
        'bol_number',
        'qty_remaining',
        'recount'
    ];

    public function product(){
        return $this->belongsTo(MasterProduct::class,'etin','ETIN');
    }
    public function purchasingDetail(){
        return $this->belongsTo(PurchasingDetail::class,'bol_number','bol_number');
    }
    public function warehouse(){
        return $this->belongsTo(WareHouse::class,'warehouse_id','id');
    }
}
