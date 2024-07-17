<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradavoIdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tradavo_ids', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100);
            $table->string('WI_ID', 100)->nullable();
            $table->string('PA_ID', 100)->nullable();
            $table->string('NV_ID', 100)->nullable();
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
        Schema::dropIfExists('tradavo_ids');
    }
}
