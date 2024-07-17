<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ProductListingFilter;
use DB;

class SmartFilter extends Model
{
    protected $table = 'smart_filters';

    protected $fillable = [
        'filter_name',
        'visible_filters',
        'visible_columns',
        'column_orders',
        'created_by',
        'created_at',
        'updated_at',
        'filter_preferences',
        'main_filter',
        'type'
    ];
    public function productListingFilterList($visible_filters)
    {
        $data =  DB::table('product_listing_filters')->select(\DB::raw("GROUP_CONCAT(label_name) as labelName"))->whereIN('id',explode(',',$visible_filters))->first();
        return $data->labelName;
    }
}
