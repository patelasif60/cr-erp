<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BillingNote extends Model
{
    protected $table = 'billing_notes_detail';
    /**
     * Enable timestamps.
     *
     * @var array
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

}
