<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnsFromCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn('weight_gt_50_lbs_1');
            $table->dropColumn('weight_gt_50_lbs_2');
            $table->dropColumn('weight_gt_50_lbs_3');
            $table->dropColumn('length_girth_gt_105_in_1');
            $table->dropColumn('length_girth_gt_105_in_2');
            $table->dropColumn('length_girth_gt_105_in_3');
            $table->dropColumn('length_gt_48_in_1');
            $table->dropColumn('length_gt_48_in_2');
            $table->dropColumn('length_gt_48_in_3');
            $table->dropColumn('width_gt_30_in_1');
            $table->dropColumn('width_gt_30_in_2');
            $table->dropColumn('width_gt_30_in_3');
            $table->dropColumn('packaging_exeptions_1');
            $table->dropColumn('packaging_exeptions_2');
            $table->dropColumn('packaging_exeptions_3');
            $table->dropColumn('commercial_1');
            $table->dropColumn('commercial_2');
            $table->dropColumn('commercial_3');
            $table->dropColumn('residential_1');
            $table->dropColumn('residential_2');
            $table->dropColumn('residential_3');
            $table->dropColumn('commercial_ground');
            $table->dropColumn('commercial_air');
            $table->dropColumn('residential_ground');
            $table->dropColumn('residential_air');
            $table->dropColumn('commercial_extended_ground');
            $table->dropColumn('commercial_extended_air');
            $table->dropColumn('residential_extended_ground');
            $table->dropColumn('residential_extended_air');
            $table->dropColumn('residential_surcharge_ground');
            $table->dropColumn('residential_surcharge_air');
            $table->dropColumn('continental_us_ground');
            $table->dropColumn('alaska_ground');
            $table->dropColumn('hawaii_ground');
            $table->dropColumn('dry_ice_surcharge_ground');
            $table->dropColumn('dry_ice_surcharge_air');
            $table->dropColumn('sure_post_per_package');
            $table->dropColumn('sure_post_status');
            $table->dropColumn('sure_post_effective_date');
            $table->dropColumn('sure_post_end_date');
            $table->dropColumn('ground_residential_per_package');
            $table->dropColumn('ground_residential_status');
            $table->dropColumn('ground_residential_effective_date');
            $table->dropColumn('ground_residential_end_date');
            $table->dropColumn('air_residential_per_package');
            $table->dropColumn('air_residential_status');
            $table->dropColumn('air_residential_effective_date');
            $table->dropColumn('air_residential_end_date');
            $table->dropColumn('additional_handling_per_package');
            $table->dropColumn('additional_handling_status');
            $table->dropColumn('additional_handling_effective_date');
            $table->dropColumn('additional_handling_end_date');
            $table->dropColumn('large_package_per_package');
            $table->dropColumn('large_package_status');
            $table->dropColumn('large_package_effective_date');
            $table->dropColumn('large_package_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carriers', function (Blueprint $table) {
            //
        });
    }
}
