<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHoldStatusOrderDetailsSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details_summary', function (Blueprint $table) {
            //
        });
        DB::table('order_details_status')->insert([
            'id' => 18,
            'status' => 'Hold'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details_summary', function (Blueprint $table) {
            //
        });
    }
}
