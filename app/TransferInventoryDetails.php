<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferInventoryDetails extends Model
{
    protected $table = 'transfer_inventory_details';

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

    public function product(){
        return $this->belongsTo(MasterProduct::class,'etin','ETIN');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
