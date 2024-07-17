<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuOrderExclusion extends Model
{
    protected $table = 'sku_order_exclusion';

    protected $guarded = ['id'];

    protected $fillable = ['client_id', 'channel_id', 'sku', 'master_product_id'];

    public function product()
    {
        return $this->belongsTo(MasterProduct::class,'master_product_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function channel() 
    {
        return $this->belongsTo(ClientChannelConfiguration::class,'channel_id');
    }
}
