<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('high_min')->nullable()->after('id');
            $table->string('high_max')->nullable()->after('high_min');
            $table->string('mid_min')->nullable()->after('high_max');
            $table->string('mid_max')->nullable()->after('mid_min');
            $table->string('low_min')->nullable()->after('mid_max');
            $table->string('low_max')->nullable()->after('low_min');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_history', function (Blueprint $table) {
            //
        });
    }
}
