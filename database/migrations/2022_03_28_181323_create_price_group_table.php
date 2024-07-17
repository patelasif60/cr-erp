<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_group', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable();
            $table->text('description')->nullable();
            $table->string('group_type')->nullable();
            $table->string('store_automator_id')->nullable();
            $table->string('lobs')->nullable();
            $table->string('group_formula')->nullable();
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
        Schema::dropIfExists('price_group');
    }
}
