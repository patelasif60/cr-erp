<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IceChartTemplate extends Model
{
   protected $table = 'ice_chart_template';

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

    public function iceSubChart()
    {
        return $this->hasMany(\App\IceSubChart::class, 'ice_chart_template_id');
    }
}
