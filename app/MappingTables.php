<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MappingTables extends Model
{
    protected $table = 'mapping_between_tables';

    protected $fillable = ['type', 'map_data'];
}
