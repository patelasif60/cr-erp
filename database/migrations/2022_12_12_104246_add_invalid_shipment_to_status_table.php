<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvalidShipmentToStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status', function (Blueprint $table) {
            //
        });

        DB::table('order_summary_status')->insert([
            [
                'id' => 25, 
                'order_status' => 'invalid_shipment_type', 
                'order_status_name' => 'Invalid Shipment Type'
            ]
        ]);
        DB::table('order_details_status')->insert([
            [
                'id' => 15, 
                'status' => 'Invalid Shipment Type'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status', function (Blueprint $table) {
            //
        });
    }
}
