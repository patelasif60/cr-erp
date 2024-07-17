<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountAssignments extends Model
{
    protected $table = 'account_assignments';

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
