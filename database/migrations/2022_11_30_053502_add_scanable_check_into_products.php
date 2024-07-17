<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScanableCheckIntoProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->integer('scanable_or_not')->default(1);
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->integer('scanable_or_not')->default(1);
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->integer('scanable_or_not')->default(1);
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
