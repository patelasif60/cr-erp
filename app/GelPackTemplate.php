<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GelPackTemplate extends Model
{
   protected $table = 'gel_pack_template';

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

    public function gelSubPack()
    {
        return $this->hasMany(\App\GelSubPack::class, 'gel_pack_template_id');
    }
}
