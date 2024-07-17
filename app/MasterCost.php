<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterCost extends Model
{
   protected $table = 'master_cost';

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

    public function subCost()
    {
        return $this->hasMany(\App\SubCost::class, 'master_cost_id');
    }
}
