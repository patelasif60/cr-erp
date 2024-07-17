<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomClientOuterBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_client_outer_boxes', function (Blueprint $table) {
            $table->id();
            $table->integer('box_id');
            $table->integer('client_id');
            $table->string('channel_ids')->nullable();
            $table->string('product_ids');
            $table->string('transit_days');
            $table->string('max_item_count');
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
        Schema::dropIfExists('custom_client_outer_boxes');
    }
}
