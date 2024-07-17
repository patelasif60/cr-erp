<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLogs extends Model
{
    protected $table = 'user_logs';

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

    public function users(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
