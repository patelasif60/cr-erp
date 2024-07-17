<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CsvHeader extends Model
{
    protected $table = 'csv_header';

    protected $fillable = ['client_id','header_name','upload_type','map_type','map_data','supplier_id'];
}
