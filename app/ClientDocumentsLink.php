<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientDocumentsLink extends Model
{
    protected $table = "client_documents_links";

    protected $fillable = [
        'client_id',
        'url',
        'name',
        'description',
        'date',
    ];
}
