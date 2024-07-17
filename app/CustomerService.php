<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    protected $fillable = [
        'client_id',
        'is_phone_etailer',
        'phone_etailer_notes',
        'is_email_etailer',
        'email_etailer_notes',
        'is_live_chat_etailer',
        'live_chat_etailer_notes',
        'is_miscellaneous_etailer',
        'miscellaneous_etailer_notes',
    ];
}
