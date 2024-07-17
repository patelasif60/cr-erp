<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOldStatusReleaseDateToOrderSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_summary', function (Blueprint $table) {
            $table->string('old_status')->nullable();
            $table->string('release_date')->nullable();
            $table->integer('must_ship_today')->default('0');
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
