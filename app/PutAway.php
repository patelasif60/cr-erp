<?php

namespace App;
use App\MasterProduct;
use App\MasterShelf;
use App\PurchasingDetail;
use App\User;

use Illuminate\Database\Eloquent\Model;

class PutAway extends Model
{
    protected $table = 'put_away';

    protected $fillable = [
        'etin', 
        'location', 
        'quantity', 
        'summary_id',
        'bol_number',
        'transfered',
        'expected_qty',
        'user_id',
        'exp_date',
        'lot'
    ];

    public function product(){
        return $this->belongsTo(MasterProduct::class,'etin','ETIN');
    }
    public function purchasingDetail(){
        return $this->belongsTo(PurchasingDetail::class,'bol_number','bol_number');
    }
    public function masterShelf(){
        return $this->belongsTo(MasterShelf::class,'location','address');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
