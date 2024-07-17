<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receiving_details', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id')->nullable();
            $table->string('po')->nullable();
            $table->string('etin')->nullable();
            $table->integer('qty_ordered')->nullable();
            $table->integer('qty_received')->nullable();
            $table->integer('qty_damaged')->nullable();
            $table->integer('qty_missing')->nullable();
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
        Schema::dropIfExists('receiving_details');
    }
}
