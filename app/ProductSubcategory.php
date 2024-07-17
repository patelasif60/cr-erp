<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
   protected $table = 'product_subcategory';

     /**
     * Enable timestamps.
     *
     * @var array
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}