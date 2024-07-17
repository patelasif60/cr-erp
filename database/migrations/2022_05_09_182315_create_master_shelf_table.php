<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterShelfTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_shelf', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aisle_id')->nullable();
            $table->unsignedBigInteger('bay_id')->nullable();
            $table->unsignedInteger('shelf')->nullable();
            $table->string('address')->nullable();
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
        Schema::dropIfExists('master_shelf');
    }
}
