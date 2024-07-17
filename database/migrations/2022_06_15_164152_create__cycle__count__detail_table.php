<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCycleCountDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycle__count__detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cycle_count_summary_id')->nullable();
            $table->string('ETIN')->nullable();
            $table->double('total_on_hand', 8, 2)->nullable();
            $table->double('total_counted', 8, 2)->nullable();
            $table->double('total_expired', 8, 2)->nullable();
            $table->string('location_1')->nullable();
            $table->double('location_qty_1',8, 2)->nullable();
            $table->string('location_2')->nullable();
            $table->double('location_qty_2',8, 2)->nullable();
            $table->string('location_3')->nullable();
            $table->double('location_qty_3',8, 2)->nullable();
            $table->string('location_4')->nullable();
            $table->double('location_qty_4',8, 2)->nullable();
            $table->string('location_5')->nullable();
            $table->double('location_qty_5',8, 2)->nullable();
            $table->string('location_6')->nullable();
            $table->double('location_qty_6',8, 2)->nullable();
            $table->string('location_7')->nullable();
            $table->double('location_qty_7',8, 2)->nullable();
            $table->string('location_8')->nullable();
            $table->double('location_qty_8',8, 2)->nullable();
            $table->string('location_9')->nullable();
            $table->double('location_qty_9',8, 2)->nullable();
            $table->string('location_10')->nullable();
            $table->double('location_qty_10',8, 2)->nullable();
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
        Schema::dropIfExists('_cycle__count__detail');
    }
}
