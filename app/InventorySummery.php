<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MasterProduct;

class InventorySummery extends Model
{
    protected $table = 'inventory_summery';

    protected $fillable = [
        'ETIN',
        'parent_ETIN',
        'wi_qty',
        'wi_each_qty',
        'wi_orderable_qty',
        'wi_fulfilled_qty',
        'wi_open_order_qty',
        'pa_qty',
        'pa_each_qty',
        'pa_orderable_qty',
        'pa_fulfilled_qty',
        'pa_open_order_qty',
        'nv_qty',
        'nv_each_qty',
        'nv_orderable_qty',
        'nv_fulfilled_qty',
        'nv_open_order_qty',
        'okc_qty',
        'okc_each_qty',
        'okc_orderable_qty',
        'okc_fulfilled_qty',
        'okc_open_order_qty'
    ];
    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
}
