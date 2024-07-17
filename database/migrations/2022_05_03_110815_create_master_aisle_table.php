<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterAisleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_aisle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('aisle_name');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('product_temp_id');
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
        Schema::dropIfExists('master_aisle');
    }
}
