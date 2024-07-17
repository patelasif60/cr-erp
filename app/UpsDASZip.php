<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UpsDASZip extends Model
{   
    protected $guarded = ['id'];

    protected $table = 'ups_das_zip';

    protected $fillable = ['das_zip','das_ext_zip'];
}
