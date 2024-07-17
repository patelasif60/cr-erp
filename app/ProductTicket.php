<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTicket extends Model
{
    protected $table = 'product_tickets';

    protected $fillable = [
        'subject',
        'description',
        'created_by',
        'master_product_id',
        'status',
        'product_type',
        'created_at',
        'updated_at'
    ];
}
