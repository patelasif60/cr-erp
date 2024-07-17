<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubOrderShippingDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_order_shipping_details', function (Blueprint $table) {
            $table->id();
            $table->string('sub_order_number', 100);
            $table->string('ship_to_name', 100)->nullable();
            $table->string('ship_to_address_type', 100)->nullable();
            $table->string('ship_to_address1', 100)->nullable();
            $table->string('ship_to_address2', 100)->nullable();
            $table->string('ship_to_address3', 100)->nullable();
            $table->string('ship_to_city', 100)->nullable();
            $table->string('ship_to_state', 100)->nullable();
            $table->string('ship_to_zip', 100)->nullable();
            $table->string('ship_to_country', 100)->nullable();
            $table->string('ship_to_phone', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->string('delivery_notes', 100)->nullable();
            $table->string('customer_shipping_price', 100)->nullable();
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
        Schema::dropIfExists('sub_order_shipping_details');
    }
}
