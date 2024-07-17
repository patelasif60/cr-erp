<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientFulfilledStatusToOrderDetailsTable extends Migration
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
            'id' => 17,
            'status' => 'Client Fulfilled'
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
