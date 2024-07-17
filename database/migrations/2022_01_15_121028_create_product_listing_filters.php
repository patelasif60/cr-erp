<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductListingFilters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_listing_filters', function (Blueprint $table) {
            $table->id();
            $table->string('label_name')->nullable();
            $table->string('column_name')->nullable();
            $table->string('text_or_select')->nullable();
            $table->string('select_table')->nullable();
            $table->string('select_value_column')->nullable();
            $table->string('select_label_column')->nullable();
            $table->integer('sorting_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_listing_filters');
    }
}
