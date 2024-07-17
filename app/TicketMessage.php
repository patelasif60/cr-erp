<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $table = 'ticket_messages';

    protected $fillable = [
        'ticket_id',
        'message',
        'send_by',
        'created_at',
        'updated_at',
    ];
}
