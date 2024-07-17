<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStatus extends Model
{
    protected $table = 'product_statuses';
    protected $fillable = [
        'product_status'
    ];

    public function productstatusList(){
        $result = ProductStatus::get()->toArray();
        return $result;
    }
}
