<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSummaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_summary', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_date');
            $table->string('ship_date');
            $table->string('order_number');
            $table->string('customer_number');
            $table->string('order_source');
            $table->string('box1_used');
            $table->string('box1_QTY');
            $table->string('box2_used');
            $table->string('box2_QTY');
            $table->string('box3_used');
            $table->string('box3_QTY');
            $table->string('box4_used');
            $table->string('box4_QTY');
            $table->string('box5_used');
            $table->string('box5_QTY');
            $table->string('packing_paper');
            $table->string('bubble_wrap');
            $table->string('dry_ice_block_lb');
            $table->string('dry_ice_pellet_lb');
            $table->string('freezer_pack_QTY');
            $table->string('shipping_cost');
            $table->string('transit_expected');
            $table->string('transit_actual');
            $table->string('etailer_weight');
            $table->string('carrier_reported_weight');
            $table->string('customer_order_price');
            $table->string('customer_discount');
            $table->string('customer_paid_price');
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
        Schema::dropIfExists('order_summary');
    }
}
