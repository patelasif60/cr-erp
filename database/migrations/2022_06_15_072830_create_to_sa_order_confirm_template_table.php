<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToSaOrderConfirmTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('to_sa_order_confirm_template', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('qty_shipped', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('ship_method', 100)->nullable();
            $table->string('package_code', 100)->nullable();
            $table->string('quantity_cancelled', 100)->nullable();
            $table->string('cancellation_reason', 100)->nullable();
            $table->string('shipped_date', 100)->nullable();
            $table->string('shipping_cost', 100)->nullable();
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
        Schema::dropIfExists('to_sa_order_confirm_template');
    }
}
