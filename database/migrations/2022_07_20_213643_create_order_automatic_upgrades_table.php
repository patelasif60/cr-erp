<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAutomaticUpgradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_automatic_upgrades', function (Blueprint $table) {
            $table->id();
            $table->integer('service_type_id')->nullable();
            $table->string('group_detail')->nullable();
            $table->string('transit_day')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('client_channel_configurations_ids')->nullable();
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
        Schema::dropIfExists('order_automatic_upgrades');
    }
}
