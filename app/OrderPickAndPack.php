<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPickAndPack extends Model
{
    protected $table = 'order_pick_and_pack';

    public function product() {
        return $this->belongsTo(MasterProduct::class, 'ETIN', 'ETIN');
    }
    public function sub_order() {
        return $this->belongsTo(OrderDetail::class, 'sub_order_number', 'sub_order_number');
    }
}
