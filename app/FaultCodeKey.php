<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaultCodeKey extends Model {

    protected $table = 'fault_code_key';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = ['fault', 'code'];
}