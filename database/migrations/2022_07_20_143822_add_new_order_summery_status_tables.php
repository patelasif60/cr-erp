<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewOrderSummeryStatusTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('order_summary_status')->insert([
            array(
                'id' => 10,
                'order_status' => 'hold_operations',
                'order_status_name' => 'Hold Operations'
            ),
            array(
                'id' => 11,
                'order_status' => 'hold_scheduled',
                'order_status_name' => 'Hold Scheduled'
            ),
            array(
                'id' => 12,
                'order_status' => 'hold_severe_weather',
                'order_status_name' => 'Hold Severe Weather'
            ),
            array(
                'id' => 13,
                'order_status' => 'hold_payment',
                'order_status_name' => 'Hold-Payment'
            ),
            array(
                'id' => 14,
                'order_status' => 'review_alt_ETIN',
                'order_status_name' => 'Review Alt ETIN'
            ),
            array(
                'id' => 15,
                'order_status' => 'review_address',
                'order_status_name' => 'Review Address'
            ),
            array(
                'id' => 16,
                'order_status' => 'review_price',
                'order_status_name' => 'Review Price'
            ),
        ]);

        DB::table('product_listing_filters')->where('label_name','Order status')->update([
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
        //
    }
}
