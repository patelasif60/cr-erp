<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickPackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pick_and_pack', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN')->nullable();
            $table->string('parent_ETIN')->nullable();
            $table->string('sub_order_number')->nullable();
            $table->string('pick_qty')->nullable();
            $table->string('pack_qty')->nullable();
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
        Schema::dropIfExists('order_pick_and_pack');
    }
}
