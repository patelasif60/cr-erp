<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorOrderDetailStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details_status', function (Blueprint $table) {        
        });
        DB::table('order_details_status')->insert([
            'id' => 5,
            'status' => 'Error: WH Assignment'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_detail_status', function (Blueprint $table) {
            //
        });
    }
}
