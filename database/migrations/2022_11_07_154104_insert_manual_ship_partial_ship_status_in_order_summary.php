<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManualShipPartialShipStatusInOrderSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_summary', function (Blueprint $table) {
            DB::table('order_summary_status')->insert([
                [
                    'id' => 21,
                    'order_status' => 'manual_partial_shipped',
                    'order_status_name' => 'Manual - Partially Shipped'
                ],
                [
                    'id' => 22,
                    'order_status' => 'manual_shipped',
                    'order_status_name' => 'Manual - Shipped'
                ]
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_summary', function (Blueprint $table) {
            //
        });
    }
}
