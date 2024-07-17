<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterBayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_bay', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('aisle_id');
            $table->unsignedInteger('bay');
            $table->unsignedInteger('shelf');
            $table->string('address');
            $table->string('type');
            $table->unsignedInteger('no_of_shelf');
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
        Schema::dropIfExists('master_bay');
    }
}
