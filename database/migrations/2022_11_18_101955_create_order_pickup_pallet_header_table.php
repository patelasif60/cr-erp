<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPickupPalletHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pallet_header_non_person_orders', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->string('pallet_number')->nullable();
            $table->string('po_number')->nullable();
            $table->string('bol_number')->nullable();
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
        Schema::dropIfExists('order_pickup_pallet_header');
    }
}
