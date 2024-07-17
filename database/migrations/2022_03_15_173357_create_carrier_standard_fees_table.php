<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierStandardFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_standard_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('carrier_id')->nullable();
            $table->string('weight_gt_50_lbs_1')->nullable();
            $table->string('weight_gt_50_lbs_2')->nullable();
            $table->string('weight_gt_50_lbs_3')->nullable();
            $table->string('length_girth_gt_105_in_1')->nullable();
            $table->string('length_girth_gt_105_in_2')->nullable();
            $table->string('length_girth_gt_105_in_3')->nullable();
            $table->string('length_gt_48_in_1')->nullable();
            $table->string('length_gt_48_in_2')->nullable();
            $table->string('length_gt_48_in_3')->nullable();
            $table->string('width_gt_30_in_1')->nullable();
            $table->string('width_gt_30_in_2')->nullable();
            $table->string('width_gt_30_in_3')->nullable();
            $table->string('packaging_exeptions_1')->nullable();
            $table->string('packaging_exeptions_2')->nullable();
            $table->string('packaging_exeptions_3')->nullable();
            $table->string('commercial_1')->nullable();
            $table->string('commercial_2')->nullable();
            $table->string('commercial_3')->nullable();
            $table->string('residential_1')->nullable();
            $table->string('residential_2')->nullable();
            $table->string('residential_3')->nullable();
            $table->string('commercial_ground')->nullable();
            $table->string('commercial_air')->nullable();
            $table->string('residential_ground')->nullable();
            $table->string('residential_air')->nullable();
            $table->string('commercial_extended_ground')->nullable();
            $table->string('commercial_extended_air')->nullable();
            $table->string('residential_extended_ground')->nullable();
            $table->string('residential_extended_air')->nullable();
            $table->string('residential_surcharge_ground')->nullable();
            $table->string('residential_surcharge_air')->nullable();
            $table->string('continental_us_ground')->nullable();
            $table->string('alaska_ground')->nullable();
            $table->string('hawaii_ground')->nullable();
            $table->string('dry_ice_surcharge_ground')->nullable();
            $table->string('dry_ice_surcharge_air')->nullable();
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
        Schema::dropIfExists('carrier_standard_fees');
    }
}
