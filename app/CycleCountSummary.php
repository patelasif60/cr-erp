<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\WareHouse;
use App\CycleCountDetail;

class CycleCountSummary extends Model
{
    protected $table = 'cycle_count_summary';
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

    public function warehouse(){
        return $this->belongsTo(WareHouse::class,'warehouse_id','id');
    }
    public function cycleCountDeatail(){
        return $this->hasMany(CycleCountDetail::class,'cycle_count_summary_id','id');
    }
}
