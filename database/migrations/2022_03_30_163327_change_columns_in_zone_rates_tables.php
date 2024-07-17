<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnsInZoneRatesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ups_zone_rates_air', function (Blueprint $table) {
            $table->renameColumn('zone1', 'zone44');
            $table->renameColumn('zone9', 'zone45');
            $table->renameColumn('zone10', 'zone46');
        });

        Schema::table('ups_zone_rates_sure_post', function (Blueprint $table) {
            $table->renameColumn('zone1', 'zone44');
            $table->renameColumn('zone9', 'zone45');
            $table->renameColumn('zone10', 'zone46');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zone_rates_tables', function (Blueprint $table) {
            //
        });
    }
}
