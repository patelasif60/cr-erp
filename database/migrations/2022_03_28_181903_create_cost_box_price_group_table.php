<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCostBoxPriceGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_group_cost_block', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('price_group_id')->nullable();
            $table->json('cost_block')->nullable();
            $table->json('sub_cost_ids')->nullable();
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
        Schema::dropIfExists('price_group_cost_block');
    }
}
