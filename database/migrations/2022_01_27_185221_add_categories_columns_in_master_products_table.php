<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoriesColumnsInMasterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->integer('product_subcategory4')->nullable();
            $table->integer('product_subcategory5')->nullable();
            $table->integer('product_subcategory6')->nullable();
            $table->integer('product_subcategory7')->nullable();
            $table->integer('product_subcategory8')->nullable();
            $table->integer('product_subcategory9')->nullable();
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->integer('product_subcategory4')->nullable();
            $table->integer('product_subcategory5')->nullable();
            $table->integer('product_subcategory6')->nullable();
            $table->integer('product_subcategory7')->nullable();
            $table->integer('product_subcategory8')->nullable();
            $table->integer('product_subcategory9')->nullable();
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->integer('product_subcategory4')->nullable();
            $table->integer('product_subcategory5')->nullable();
            $table->integer('product_subcategory6')->nullable();
            $table->integer('product_subcategory7')->nullable();
            $table->integer('product_subcategory8')->nullable();
            $table->integer('product_subcategory9')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_products', function (Blueprint $table) {
            //
        });
    }
}
