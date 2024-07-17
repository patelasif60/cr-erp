<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePickerConfigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picker_configrations', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('order_processing_id')->nullable();
            $table->string('batch_max_until_2pm')->nullable();
            $table->string('batch_max_2pm_to_4pm')->nullable();
            $table->string('batch_max_after_4pm')->nullable();
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
        Schema::dropIfExists('picker_configrations');
    }
}
