<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCarrierPeakSurchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('carrier_peak_surchrges');

        Schema::create('carrier_peak_surchrges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('status')->nullable();
            $table->date('effective_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('sure_post')->nullable();
            $table->decimal('ground_residential')->nullable();
            $table->decimal('air_residential')->nullable();
            $table->decimal('additional_handling')->nullable();
            $table->decimal('large_package_gt_50_lbs')->nullable();
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
        Schema::dropIfExists('carrier_peak_surchrges');
    }
}
