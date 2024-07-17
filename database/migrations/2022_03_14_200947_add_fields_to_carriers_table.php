<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->double('sure_post_per_package')->nullable();
            $table->string('sure_post_status')->nullable();
            $table->date('sure_post_effective_date')->nullable();
            $table->date('sure_post_end_date')->nullable();

            $table->double('ground_residential_per_package')->nullable();
            $table->string('ground_residential_status')->nullable();
            $table->date('ground_residential_effective_date')->nullable();
            $table->date('ground_residential_end_date')->nullable();

            $table->double('air_residential_per_package')->nullable();
            $table->string('air_residential_status')->nullable();
            $table->date('air_residential_effective_date')->nullable();
            $table->date('air_residential_end_date')->nullable();

            $table->double('additional_handling_per_package')->nullable();
            $table->string('additional_handling_status')->nullable();
            $table->date('additional_handling_effective_date')->nullable();
            $table->date('additional_handling_end_date')->nullable();

            $table->double('large_package_per_package')->nullable();
            $table->string('large_package_status')->nullable();
            $table->date('large_package_effective_date')->nullable();
            $table->date('large_package_end_date')->nullable();

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
            $table->dropColumn('sure_post_per_package');
            $table->dropColumn('sure_post_status');
            $table->dropColumn('sure_post_effective_date');
            $table->dropColumn('sure_post_end_date');

            $table->dropColumn('ground_residential_per_package');
            $table->dropColumn('ground_residential_status');
            $table->dropColumn('ground_residential_effective_date');
            $table->dropColumn('ground_residential_end_date');

            $table->double('air_residential_per_package');
            $table->string('air_residential_status');
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
}
