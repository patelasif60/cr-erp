<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderExpAndLot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_exp_and_lot', function (Blueprint $table) {
            $table->id();
            $table->string('po')->nullable();
            $table->integer('pd_id')->nullable();
            $table->string('ETIN')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('lot')->nullable();
            $table->string('qty')->nullable();
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
        Schema::dropIfExists('purchase_order_exp_and_lot');
    }
}
