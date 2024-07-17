<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplementalMptDataQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplemental_mpt_data_queue', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('length', 100)->nullable();
            $table->string('width', 100)->nullable();
            $table->string('height', 100)->nullable();
            $table->string('upc', 100)->nullable();
            $table->string('gtin', 100)->nullable();
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
        Schema::dropIfExists('supplemental_mpt_data_queue');
    }
}
