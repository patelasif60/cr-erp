<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasterProductIdInSomeTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('product_images_queue', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('product_inventory', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('product_inventory_queue', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('supplemental_mpt_data', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

        Schema::table('supplemental_mpt_data_queue', function (Blueprint $table) {
            $table->integer('master_product_id')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
    }
}
