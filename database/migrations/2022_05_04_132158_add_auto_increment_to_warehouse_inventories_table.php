<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoIncrementToWarehouseInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        // Schema::table('warehouse_inventories', function (Blueprint $table) {   
        //     $table->dropColumn('id');
        // });

        // Schema::table('warehouse_inventories', function (Blueprint $table) {
        //     $table->unsignedInteger('id', true)->first();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_inventories', function (Blueprint $table) {
            //
        });
    }
}