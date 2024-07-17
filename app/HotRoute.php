<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotRoute extends Model
{
    protected $table = 'hot_routes';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $fillable = [
        'wh_id', 'carrier_id', 'zip', 'transit_days', 'cut_off_time'
    ];

    public function warehouse() {
        return $this->belongsTo(WareHouse::class,'wh_id','id');
    }
    
    public function carrier() {
        return $this->belongsTo(Carrier::class,'carrier_id','id');
    }
}
