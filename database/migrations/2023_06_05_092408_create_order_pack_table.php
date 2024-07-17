<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderPackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pack', function (Blueprint $table) {
            $table->id();
            $table->string('sub_order_number')->nullable();
            $table->string('ETIN')->nullable();
            $table->string('lot')->nullable();
            $table->date('exp')->nullable();
            $table->double('qty')->nullable();
            $table->integer('transfer')->nullable();
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
        Schema::dropIfExists('order_pack');
    }
}
