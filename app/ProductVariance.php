<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVariance extends Model
{
    protected $table = 'product_variance_report';
    
    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
}
