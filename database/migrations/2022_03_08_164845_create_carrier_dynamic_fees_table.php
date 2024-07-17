<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierDynamicFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_dynamic_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->date('effective_date')->nullable();
            $table->decimal('ground')->nullable();
            $table->decimal('air')->nullable();
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
        Schema::dropIfExists('carrier_dynamic_fees');
    }
}
