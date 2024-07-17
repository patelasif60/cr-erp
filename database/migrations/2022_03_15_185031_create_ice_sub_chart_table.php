<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIceSubChartTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ice_sub_chart', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ice_chart_template_id')->nullable();
            $table->unsignedInteger('packaging_materials_id')->nullable();
            $table->double('1day_block',8,2)->nullable();
            $table->double('1day_pellet',8,2)->nullable();
            $table->double('2day_block',8,2)->nullable();
            $table->double('2day_pellet',8,2)->nullable();
            $table->double('3day_block',8,2)->nullable();
            $table->double('3day_pellet',8,2)->nullable();
            $table->double('4day_block',8,2)->nullable();
            $table->double('4day_pellet',8,2)->nullable();
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
        Schema::dropIfExists('ice_sub_chart');
    }
}
