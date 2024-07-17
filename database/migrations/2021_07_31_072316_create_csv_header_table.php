<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCsvHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_header', function (Blueprint $table) {
            $table->id();
            $table->string('header_name', 100)->default(null)->nullable();
            $table->string('upload_type', 100)->default(null)->nullable();
            $table->string('map_type', 100)->default(null)->nullable();
            $table->string('map_data', 100)->default(null)->nullable();
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
        Schema::dropIfExists('csv_header');
    }
}
