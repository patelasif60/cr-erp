<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_num', 100)->nullable();
            $table->string('warehouse_id', 100)->nullable();
            $table->string('order_id', 100)->nullable();
            $table->string('ETIN', 100)->nullable();
            $table->string('shipped_qty', 100)->nullable();
            $table->string('shipping_carrier', 100)->nullable();
            $table->string('shipping_label_creation_time', 100)->nullable();
            $table->string('ship_date', 100)->nullable();
            $table->string('ship_day', 100)->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('box_used', 100)->nullable();
            $table->string('packing_paper', 100)->nullable();
            $table->string('bubble_wrap', 100)->nullable();
            $table->string('air_pillows', 100)->nullable();
            $table->string('cooler_used', 100)->nullable();
            $table->string('dry_ice_block_Lb', 100)->nullable();
            $table->string('dry_ice_pallet_Lb', 100)->nullable();
            $table->string('freezer_pack_qty', 100)->nullable();
            $table->string('shipping_cost', 100)->nullable();
            $table->string('transit_expected', 100)->nullable();
            $table->string('transit_actual', 100)->nullable();
            $table->string('delivery_date', 100)->nullable();
            $table->string('delivery_day', 100)->nullable();
            $table->string('etailer_weight', 100)->nullable();
            $table->string('picker_name', 100)->nullable();
            $table->string('packer_name', 100)->nullable();
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
        Schema::dropIfExists('order_packages');
    }
}
