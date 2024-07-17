<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasingSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_id')->nullable();
            $table->string('purchasing_asn_date')->nullable();
            $table->string('order')->nullable();
            $table->string('invoice')->nullable();
            $table->string('bol')->nullable();
            $table->string('product_cost')->nullable();
            $table->string('delivery_inbound_fees')->nullable();
            $table->string('freight_shipping_charge')->nullable();
            $table->string('misc_acquisition_cost')->nullable();
            $table->string('surcharge_1')->nullable();
            $table->string('surcharge_2')->nullable();
            $table->string('surcharge_3')->nullable();
            $table->string('surcharge_4')->nullable();
            $table->string('surcharge_5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchasing_summaries');
    }
}
