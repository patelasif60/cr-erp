<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\MasterProductTable;

class CycleCountDetail extends Model
{
    protected $table = 'cycle__count__detail';

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

    public function product_details(){
        return $this->belongsTo(MasterProductTable::class,'ETIN','ETIN');
    }
}
