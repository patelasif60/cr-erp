<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertManualReadyToPickInOrderSummary extends Migration
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
                    'id' => 23,
                    'order_status' => 'manual_ready_to_pick',
                    'order_status_name' => 'Manual - Ready To Pick'
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
