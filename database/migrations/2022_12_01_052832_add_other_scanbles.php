<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherScanbles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->renameColumn('scanable_or_not','upc_scanable');
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->renameColumn('scanable_or_not','upc_scanable');
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->renameColumn('scanable_or_not','upc_scanable');
        });




        Schema::table('master_product', function (Blueprint $table) {
            $table->integer('gtin_scanable')->default(1);
            $table->integer('unit_upc_scanable')->default(1);
            $table->integer('unit_gtin_scanable')->default(1);
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->integer('gtin_scanable')->default(1);
            $table->integer('unit_upc_scanable')->default(1);
            $table->integer('unit_gtin_scanable')->default(1);
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->integer('gtin_scanable')->default(1);
            $table->integer('unit_upc_scanable')->default(1);
            $table->integer('unit_gtin_scanable')->default(1);
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
