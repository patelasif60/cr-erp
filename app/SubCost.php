<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCost extends Model
{
    protected $table = 'sub_cost';

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

    public function masterCost()
    {
        return $this->belongsTo(MasterCost::class,'master_cost_id');
    }
}
