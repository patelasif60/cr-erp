<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderReshipFault extends Model {

    protected $table = 'order_reship_fault';

    public $timestamps = true;

    protected $guarded = ['id'];

    protected $fillable = ['sub_order_number', 'fault_code_id', 'reship_reason_id'];
}