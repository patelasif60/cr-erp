<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickedQtyForOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picked_lot_and_exp', function (Blueprint $table) {
            $table->id();
            $table->string('sub_order')->nullable();
            $table->string('ETIN')->nullable();
            $table->string('master_shelf_id')->nullable();
            $table->string('address')->nullable();
            $table->string('qty')->nullable();
            $table->string('lot')->nullable();
            $table->string('exp')->nullable();
            
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
        Schema::dropIfExists('picked_lot_and_exp');
    }
}
