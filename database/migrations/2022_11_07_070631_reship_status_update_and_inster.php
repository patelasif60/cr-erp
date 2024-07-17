<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReshipStatusUpdateAndInster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('order_details_status')->where('id',10)->update([
            'status' => 'Reship Assigned'
        ]);
        DB::table('order_details_status')->insert([
            [
                'id' => 11,
                'status' => 'Reship Picked'
            ],
            [
                'id' => 12,
                'status' => 'Reship Packed'
            ],
            [
                'id' => 13,
                'status' => 'Reship Shipped'
            ]
        ]);
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
