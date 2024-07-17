<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPriceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_price', function (Blueprint $table) {
            $table->id();
            $table->string('client_id', 100)->default(null)->nullable();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('product_listing_name', 100)->default(null)->nullable();
            $table->string('current_price', 100)->default(null)->nullable();
            $table->string('new_price', 100)->default(null)->nullable();
            $table->string('new_price_date', 100)->default(null)->nullable();
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
        Schema::dropIfExists('product_price');
    }
}
