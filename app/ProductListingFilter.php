<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductListingFilter extends Model
{
    protected $table = 'product_listing_filters';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = [
        'label_name' , 'column_name', 'text_or_select', 'sorting_order', 'is_default', 'type'
    ];
}
