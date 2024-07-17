<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchasingSummary extends Model
{
    protected $table = 'purchasing_summaries';

    protected $fillable = ['supplier_id', 'purchasing_asn_date', 'order', 'invoice', 'bol', 
                            'product_cost', 'delivery_inbound_fees', 'freight_shipping_charge',
                            'misc_acquisition_cost', 'surcharge_1', 'surcharge_2', 'surcharge_3', 
                            'surcharge_4', 'surcharge_5', 'status', 'warehouse_id', 'po_status', 
                            'delivery_date', 'report_path', 'client_id'
                        ];
    
    
}
