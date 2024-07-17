<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierStatus extends Model
{
    protected $table = 'supplier_status';

    protected $fillable = [
        'supplier_status',
    ];

    public function supplierStatusList(){
        $result = SupplierStatus::get()->toArray();
        return $result;
    }
}
