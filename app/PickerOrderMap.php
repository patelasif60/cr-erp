<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickerOrderMap extends Model
{
    protected $table = 'picker_order_maps';

    protected $fillable = ['order_summary_id', 'user_id', 'created_at', 'updated_at'];
}
