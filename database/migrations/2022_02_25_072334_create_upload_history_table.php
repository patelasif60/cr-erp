<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload_history', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->integer('failed_products_count')->nullable();
            $table->integer('dublicate_product_count')->nullable();
            $table->text('failed_products')->nullable();
            $table->text('dublicate_product')->nullable();
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
        Schema::dropIfExists('upload_history');
    }
}
