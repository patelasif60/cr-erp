<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVarianceReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variance_report', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->nullable();
            $table->string('warehouse_id', 100)->nullable();
            $table->string('qty', 100)->nullable();
            $table->string('reason', 100)->nullable();
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
        Schema::dropIfExists('product_variance_report');
    }
}
