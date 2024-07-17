<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOsDValuesInProductListingFilters extends Migration
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
        DB::table('product_listing_filters')->where('id', '67')->update([
            'text_or_select' => 'Select',
            'select_table' => 'order_summary',
            'select_value_column' => 'order_source',
            'select_label_column' => 'order_source'
        ]);
        DB::table('product_listing_filters')->where('id', '68')->update([
            'text_or_select' => 'Select',
            'select_table' => 'order_summary',
            'select_value_column' => 'ship_to_state',
            'select_label_column' => 'ship_to_state'
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
