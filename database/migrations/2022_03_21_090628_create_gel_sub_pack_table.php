<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGelSubPackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gel_sub_pack', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('gel_pack_template_id')->nullable();
            $table->unsignedInteger('packaging_materials_id')->nullable();
            $table->double('1day_block',8,2)->nullable();
            $table->double('1day_pellet',8,2)->nullable();
            $table->double('1day_1lb_pack',8,2)->nullable();
            $table->double('1day_2lb_pack',8,2)->nullable();
            $table->double('2day_block',8,2)->nullable();
            $table->double('2day_pellet',8,2)->nullable();
            $table->double('2day_1lb_pack',8,2)->nullable();
            $table->double('2day_2lb_pack',8,2)->nullable();
            $table->double('3day_block',8,2)->nullable();
            $table->double('3day_pellet',8,2)->nullable();
            $table->double('3day_1lb_pack',8,2)->nullable();
            $table->double('3day_2lb_pack',8,2)->nullable();
            $table->double('4day_block',8,2)->nullable();
            $table->double('4day_pellet',8,2)->nullable();
            $table->double('4day_1lb_pack',8,2)->nullable();
            $table->double('4day_2lb_pack',8,2)->nullable();
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
        Schema::dropIfExists('gel_sub_pack');
    }
}
