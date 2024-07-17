<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackedStatusToOrderDetailsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_details_status', function (Blueprint $table) {
            //
        });
        DB::table('order_details_status')->insert([
            array(
                'id' => 4,
                'status' => 'Packed'
            )
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_details_status', function (Blueprint $table) {
            //
        });
    }
}
