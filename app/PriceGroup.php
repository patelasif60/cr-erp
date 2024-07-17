<?php

namespace App;

use App\GroupFormula;
use Illuminate\Database\Eloquent\Model;

class PriceGroup extends Model
{
    protected $table = 'price_group';

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

    public function priceGroupCostBlock()
    {
        return $this->hasOne(\App\PriceGroupCostBlock::class, 'price_group_id');
    }

    public function group_formulas(){
        return $this->hasMany(GroupFormula::class,'group_id');
    }
}
