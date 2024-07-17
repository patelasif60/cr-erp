<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details_new', function (Blueprint $table) {
            $table->id();
            $table->string('etailer_order_number', 100)->nullable();
            $table->string('ETIN', 100)->nullable();
            $table->string('mp_line_number', 100)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('SA_product_name', 100)->nullable();
            $table->string('MPT_product_listing_name', 100)->nullable();
            $table->string('SA_unit_price', 100)->nullable();
            $table->string('channel_extended_price', 100)->nullable();
            $table->string('channel_product_price', 100)->nullable();
            $table->string('discount_name', 100)->nullable();
            $table->string('discount', 100)->nullable();
            $table->string('customer_paid_price', 100)->nullable();
            $table->string('qty', 100)->nullable();
            $table->string('fulfilled_qty', 100)->nullable();
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
        Schema::dropIfExists('order_details_new');
    }
}
