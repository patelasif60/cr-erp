<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceGroupCostBlock extends Model
{
    protected $table = 'price_group_cost_block';

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
