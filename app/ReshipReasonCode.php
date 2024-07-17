<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReshipReasonCode extends Model {

    protected $table = 'reship_reason_code';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = ['reason', 'code'];
}