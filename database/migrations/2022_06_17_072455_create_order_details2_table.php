<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetails2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 100)->nullable();
            $table->string('ETIN', 100)->nullable();
            $table->string('SA_line_number', 100)->nullable();
            $table->string('SA_sku', 100)->nullable();
            $table->string('channel_product_name', 100)->nullable();
            $table->string('etailer_product_name', 100)->nullable();
            $table->string('channel_unit_price', 100)->nullable();
            $table->string('channel_extended_price', 100)->nullable();
            $table->string('etailer_channel_price', 100)->nullable();
            $table->string('discount_name', 100)->nullable();
            $table->string('customer_discount', 100)->nullable();
            $table->string('customer_paid_price', 100)->nullable();
            $table->string('quantity_ordered', 100)->nullable();
            $table->string('quantity_fulfilled', 100)->nullable();
            $table->string('ETIN_flag', 10)->nullable();
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
        Schema::dropIfExists('order_details2');
    }
}
