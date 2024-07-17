<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_date');
            $table->string('ship_date');
            $table->string('order_number');
            $table->string('customer_number');
            $table->string('customer_name');
            $table->string('order_source');
            $table->string('ship_to_name');
            $table->string('ship_to_address');
            $table->string('ship_to_address2');
            $table->string('ship_to_city');
            $table->string('ship_to_state');
            $table->string('ship_to_zip');
            $table->string('ship_to_country');
            $table->string('ETIN');
            $table->string('Product description');
            $table->string('quantity ordered');
            $table->string('quantity fulfilled');
            $table->string('customer unit  price');
            $table->string('customer extended price');
            $table->string('customer discount');
            $table->string('customer paid price');
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
        Schema::dropIfExists('order_detail');
    }
}
