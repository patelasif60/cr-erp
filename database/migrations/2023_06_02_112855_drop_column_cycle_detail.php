<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnCycleDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cycle__count__detail', function (Blueprint $table) {
            $table->dropColumn('location_1');
            $table->dropColumn('location_qty_1');
            $table->dropColumn('location_2');
            $table->dropColumn('location_qty_2');
            $table->dropColumn('location_3');
            $table->dropColumn('location_qty_3');
            $table->dropColumn('location_4');
            $table->dropColumn('location_qty_4');
            $table->dropColumn('location_5');
            $table->dropColumn('location_qty_5');
            $table->dropColumn('location_6');
            $table->dropColumn('location_qty_6');
            $table->dropColumn('location_7');
            $table->dropColumn('location_qty_7');
            $table->dropColumn('location_8');
            $table->dropColumn('location_qty_8');
            $table->dropColumn('location_9');
            $table->dropColumn('location_qty_9');
            $table->dropColumn('location_10');
            $table->dropColumn('location_qty_10');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
