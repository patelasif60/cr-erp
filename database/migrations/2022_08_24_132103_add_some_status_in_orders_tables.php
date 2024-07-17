<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeStatusInOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('order_details_status')->insert([
            array(
                'id' => 6,
                'status' => 'Shipped'
            )
        ]);

        DB::table('order_summary_status')->insert([
            array(
                'id' => 17,
                'order_status' => 'shipped',
                'order_status_name' => 'Shipped'
            ),
            array(
                'id' => 18,
                'order_status' => 'partially_shipped',
                'order_status_name' => 'Partially Shipped'
            ),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
