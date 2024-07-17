<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\AisleMaster;
use DB;
class BayMaster extends Model
{
    protected $table = 'master_bay';

    protected $fillable = [ 'aisle_id','bay', 'shelf', 'type' ];

    public function aisle_name(){
        return $this->belongsTo(AisleMaster::class,'aisle_id','id');
    }
    public function shelfs(){
        return $this->hasMany(\App\MasterShelf::class, 'bay_id');
    }
}
