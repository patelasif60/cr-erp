<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryAdjustmentReport extends Model
{
    protected $table = 'inventry_adjustment_report';

    protected $fillable = [
        'ETIN',
        'location',
        'starting_qty',
        'ending_qty',
        'total_change',
        'user',
        'warehouse',
        'reference',
        'reference_value',
        'reference_description'
    ];
    public function masterShelf(){
        return $this->belongsTo(MasterShelf::class,'location','address');
    }
    public function product(){
        return $this->belongsTo(MasterProduct::class,'ETIN','ETIN');
    }
    public function users(){
        return $this->belongsTo(User::class,'user','id');
    }



}
