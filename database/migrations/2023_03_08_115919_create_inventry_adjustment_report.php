<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventryAdjustmentReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventry_adjustment_report', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN')->nullable();
            $table->string('location')->nullable();
            $table->string('starting_qty')->nullable();
            $table->string('ending_qty')->nullable();
            $table->string('total_change')->nullable();
            $table->string('user')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('reference')->nullable();
            $table->string('reference_value')->nullable();
            $table->text('reference_description')->nullable();
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
        Schema::dropIfExists('inventry_adjustment_report');
    }
}
