<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $table = 'product_subcategory';
    protected $fillable = [
        'product_category_id',
        'sub_category_1',
        'sc1_sa_code',
        'sub_category_2',
        'sc2_sa_code',
        'sub_category_3',
        'sc3_sa_code'

    ];
}
