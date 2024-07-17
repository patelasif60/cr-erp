<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderDetailsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details_status', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->timestamps();
        });
        DB::table('order_details_status')->insert([
            array(
                'id' => 1,
                'status' => 'Ready to pick'
            ),
            array(
                'id' => 2,
                'status' => 'Assigned'
            ),
            array(
                'id' => 3,
                'status' => 'Picked'
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
        Schema::dropIfExists('table_order_details_status');
    }
}
