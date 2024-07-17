<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientWarehouseAndFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_warehouse_and_fulfillments', function (Blueprint $table) {
            $table->id();
            $table->string('event')->nullable();
            $table->string('frequency')->nullable();
            $table->string('day_and_time')->nullable();
            $table->string('details')->nullable();
            $table->string('owner')->nullable();
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
        Schema::dropIfExists('client_warehouse_and_fulfillments');
    }
}
