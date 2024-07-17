<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientBillingNote extends Model
{

    protected $table = 'client_billing_notes';

    protected $guarded = ['id'];
}
