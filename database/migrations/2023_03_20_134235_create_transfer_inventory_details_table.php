<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferInventoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_inventory_details', function (Blueprint $table) {
            $table->id();
            $table->string('etin')->nullable();
            $table->string('current_upc')->nullable();
            $table->string('current_warehouse')->nullable();
            $table->string('current_location')->nullable();
            $table->string('quantity')->nullable();
            $table->integer('transfer_warehouse')->nullable();
            $table->integer('transfer_location')->nullable();
            $table->integer('user_id')->nullable();
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
        Schema::dropIfExists('transfer_inventory_details');
    }
}
