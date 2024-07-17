<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOrderStatusValuesInProductListingFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_listing_filters', function (Blueprint $table) {
            //
        });
        DB::table('product_listing_filters')->where('id', '71')->update([
            'text_or_select' => 'Select',
            'select_table' => 'order_summary_status',
            'select_value_column' => 'id',
            'select_label_column' => 'order_status_name'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_listing_filters', function (Blueprint $table) {
            //
        });
    }
}
