<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipToCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ship_to_customer', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number', 100)->nullable();
            $table->string('customer_full_name', 100)->nullable();
            $table->string('customer_email', 100)->nullable();
            $table->string('customer_phone', 100)->nullable();
            $table->string('shipping_full_name', 100)->nullable();
            $table->string('shipping_address_type', 100)->nullable();
            $table->string('shipping_address1', 100)->nullable();
            $table->string('shipping_address2', 100)->nullable();
            $table->string('shipping_address3', 100)->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_postal_code', 100)->nullable();
            $table->string('shipping_country_code', 100)->nullable();
            $table->string('shipping_phone', 100)->nullable();
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
        Schema::dropIfExists('ship_to_customer');
    }
}
