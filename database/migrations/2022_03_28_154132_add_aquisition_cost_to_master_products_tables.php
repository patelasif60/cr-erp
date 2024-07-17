<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAquisitionCostToMasterProductsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->decimal('acquisition_cost')->nullable()->after('cost');
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->decimal('acquisition_cost')->nullable()->after('cost');
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->decimal('acquisition_cost')->nullable()->after('cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->dropColumn('acquisition_cost');
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->dropColumn('acquisition_cost');
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->dropColumn('acquisition_cost');
        });
    }
}
