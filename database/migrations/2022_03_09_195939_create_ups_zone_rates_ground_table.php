<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsZoneRatesGroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_zone_rates_by_ground', function (Blueprint $table) {
            $table->id();
            $table->decimal('zone2')->nullable();
            $table->decimal('zone3')->nullable();
            $table->decimal('zone4')->nullable();
            $table->decimal('zone5')->nullable();
            $table->decimal('zone6')->nullable();
            $table->decimal('zone7')->nullable();
            $table->decimal('zone8')->nullable();
            $table->decimal('zone44')->nullable();
            $table->decimal('zone45')->nullable();
            $table->decimal('zone46')->nullable();
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
        Schema::dropIfExists('ups_zone_rates_sure_post');
    }
}
