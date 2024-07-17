<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpsZipZoneByWH extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $table = 'ups_zip_zone_wh';
}
