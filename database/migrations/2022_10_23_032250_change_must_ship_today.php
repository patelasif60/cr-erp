<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMustShipToday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_summary', function (Blueprint $table) {
            $table->dropColumn('must_ship_today');
        });

        Schema::table('order_summary', function (Blueprint $table) {
            $table->string('must_ship_today')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_summary', function (Blueprint $table) {
            //
        });
    }
}
