<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertDefaultDataInSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            DB::table('settings')->insert([
                'high_min' =>1,
                'high_max' => 24,
                'mid_min'=>25,
                'mid_max'=>50,
                'low_min' => 51,
                'low_max'=>100,
                'created_at' => '2023-05-18 23:57:43',
                'updated_at' => '2023-05-18 23:57:43'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
