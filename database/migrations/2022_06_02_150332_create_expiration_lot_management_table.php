<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpirationLotManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expiration_lot_management', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->integer('warehouse_id')->nullable();
            $table->string('upc')->nullable();
            $table->integer('qty_delivered')->nullable();
            $table->integer('qty_current')->nullable();
            $table->date('received_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('production_date')->nullable();
            $table->string('lot_id')->nullable();
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
        Schema::dropIfExists('expiration_lot_management');
    }
}
