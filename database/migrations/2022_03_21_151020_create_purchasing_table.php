<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchasing', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id')->nullable();
            $table->date('purchasing_date')->nullable();
            $table->string('order')->nullable();
            $table->string('invoice')->nullable();
            $table->string('bol')->nullable();
            $table->decimal('product_cost',8,2)->nullable();
            $table->decimal('delivery_inbound_fees',8,2)->nullable();
            $table->decimal('freight_shipping_charge',8,2)->nullable();
            $table->decimal('misc_acquisition_cost',8,2)->nullable();
            $table->decimal('surcharge_1',8,2)->nullable();
            $table->decimal('surcharge_2',8,2)->nullable();
            $table->decimal('surcharge_3',8,2)->nullable();
            $table->decimal('surcharge_4',8,2)->nullable();
            $table->decimal('surcharge_5',8,2)->nullable();
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
        Schema::dropIfExists('purchasing');
    }
}
