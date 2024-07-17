<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertOrderSummaryStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_summary_status', function (Blueprint $table) {
            $table->id();
            $table->string('order_status')->nullable();
            $table->string('order_status_name')->nullable();
            $table->timestamps();
        });

        DB::table('order_summary_status')->insert([
            array(
                'id' => 1,
                'order_status' => 'new',
                'order_status_name' => 'New'
            ),
            array(
                'id' => 2,
                'order_status' => 'ready_to_pick',
                'order_status_name' => 'Ready to Pick'
            ),
            array(
                'id' => 3,
                'order_status' => 'partially_picked',
                'order_status_name' => 'Partially Picked'
            ),
            array(
                'id' => 4,
                'order_status' => 'picked',
                'order_status_name' => 'Picked'
            ),
            array(
                'id' => 5,
                'order_status' => 'partially_packed',
                'order_status_name' => 'Partially Packed'
            ),
            array(
                'id' => 6,
                'order_status' => 'packed',
                'order_status_name' => 'Packed'
            ),
            array(
                'id' => 7,
                'order_status' => 'error_wh_assignment',
                'order_status_name' => 'Error: WH Assignment'
            ),
            array(
                'id' => 8,
                'order_status' => 'error_ETIN',
                'order_status_name' => 'Error: ETIN'
            ),
            array(
                'id' => 9,
                'order_status' => 'review_alt_ETIN',
                'order_status_name' => 'Review-Alt ETIN'
            ),
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
