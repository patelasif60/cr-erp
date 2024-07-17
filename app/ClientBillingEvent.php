<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientBillingEvent extends Model
{
    protected $table = 'client_billing_events';
    protected $guarded = ['id'];
}
