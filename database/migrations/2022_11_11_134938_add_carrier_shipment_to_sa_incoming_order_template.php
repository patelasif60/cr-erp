<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCarrierShipmentToSaIncomingOrderTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sa_incoming_order_template', function (Blueprint $table) {
            $table->string('carrier_type')->nullable();
            $table->string('shipment_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sa_incoming_order_template', function (Blueprint $table) {
            //
        });
    }
}
