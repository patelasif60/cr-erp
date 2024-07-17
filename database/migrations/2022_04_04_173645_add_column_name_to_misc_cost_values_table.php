<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnNameToMiscCostValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('misc_cost_values', function (Blueprint $table) {
            $table->string('column_name')->nullable()->after('data_point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('misc_cost_values', function (Blueprint $table) {
            $table->dropColumn('column_name');
        });
    }
}
