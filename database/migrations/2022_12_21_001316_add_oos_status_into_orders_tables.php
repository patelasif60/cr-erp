<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOosStatusIntoOrdersTables extends Migration
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
                'id' => 26,
                'order_status' => 'error_oos',
                'order_status_name' => 'Error: OOS'
            )
        ]);

        DB::table('order_details_status')->insert([
            array(
                'id' => 16,
                'status' => 'Error: OOS'
            )
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
