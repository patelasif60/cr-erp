<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultZeroToQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receiving_details', function (Blueprint $table) {
            $table->integer('qty_ordered')->default(0)->change();
            $table->integer('qty_received')->default(0)->change();
            $table->integer('qty_damaged')->default(0)->change();
            $table->integer('qty_missing')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receiving_details', function (Blueprint $table) {
            //
        });
        DB::statement('ALTER TABLE `receiving_details` ALTER `qty_ordered` SET DEFAULT 0;');
        DB::statement('ALTER TABLE `receiving_details` ALTER `qty_received` SET DEFAULT 0;');
        DB::statement('ALTER TABLE `receiving_details` ALTER `qty_damaged` SET DEFAULT 0;');
        DB::statement('ALTER TABLE `receiving_details` ALTER `qty_missing` SET DEFAULT 0;');
    }
}
