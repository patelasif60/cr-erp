<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportsUserFilter extends Model
{
    protected $table = 'reports_user_filter';

    protected $fillable = [
        'created_by',
        'selected_filters',
        'report_type',
        'filter_ids'
    ];
}