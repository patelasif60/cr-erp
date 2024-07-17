<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PickRestockItems extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pick_restock_items';

    protected $fillable = ['ETIN', 'backstock_address', 'qty', 'created_at', 'updated_at'];
}
