<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpirationLotManagement extends Model
{
    protected $table = 'expiration_lot_management';

    protected $fillable = [
        'address',
        'warehouse_id',
        'upc',
        'qty_delivered',
        'qty_current',
        'received_date',
        'expiration_date',
        'production_date',
        'lot_id'
    ];
}
