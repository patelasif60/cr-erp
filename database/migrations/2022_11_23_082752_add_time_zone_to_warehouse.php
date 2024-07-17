<?php

use App\WareHouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeZoneToWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('time_zone')->nullable();
        });

        WareHouse::whereIn('warehouses', ['WI', 'OKC'])->update(['time_zone' => 'America/Chicago']);
        WareHouse::where('warehouses', 'NV')->update(['time_zone' => 'America/Los_Angeles']);
        WareHouse::where('warehouses', 'PA')->update(['time_zone' => 'America/New_York']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse', function (Blueprint $table) {
            //
        });
    }
}
