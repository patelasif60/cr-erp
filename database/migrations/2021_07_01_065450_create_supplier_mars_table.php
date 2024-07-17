<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierMarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_mars', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('etailer_stock_status', 100)->default(null)->nullable();
            $table->string('list_status', 100)->default(null)->nullable();
            $table->string('acquisition_cost', 100)->default(null)->nullable();
            $table->string('brand', 100)->default(null)->nullable();
            $table->string('pack_type', 100)->default(null)->nullable();
            $table->string('product', 100)->default(null)->nullable();
            $table->string('ITEM_NO', 100)->default(null)->nullable();
            $table->string('12_digit_unit_UPC', 100)->default(null)->nullable();
            $table->string('10_digit_unit_UPC', 100)->default(null)->nullable();
            $table->string('GTIN_14_digit_case_UPC', 100)->default(null)->nullable();
            $table->string('PRICE_AND_WEIGHT_SCHEDULE_2_4_PALLETS', 100)->default(null)->nullable();
            $table->string('PRICE_AND_WEIGHT_SCHEDULE_4_10_PALLETS', 100)->default(null)->nullable();
            $table->string('PRICE_AND_WEIGHT_SCHEDULE_10_22_PALLETS', 100)->default(null)->nullable();
            $table->string('PRICE_AND_WEIGHT_SCHEDULE_22_24_PALLETS', 100)->default(null)->nullable();
            $table->string('UNIT_PRICE_LIST_10_22_PALLETS', 100)->default(null)->nullable();
            $table->string('trays_per_case', 100)->default(null)->nullable();
            $table->string('units_per_case', 100)->default(null)->nullable();
            $table->string('outside_case_dimensions_lx', 100)->default(null)->nullable();
            $table->string('outside_case_dimensions_wx', 100)->default(null)->nullable();
            $table->string('outside_case_dimensions_h', 100)->default(null)->nullable();
            $table->string('tray_dimensions_lx', 100)->default(null)->nullable();
            $table->string('tray_dimensions_wx', 100)->default(null)->nullable();
            $table->string('tray_dimensions_h', 100)->default(null)->nullable();
            $table->string('unit_dimensions_lx', 100)->default(null)->nullable();
            $table->string('unit_dimensions_wx', 100)->default(null)->nullable();
            $table->string('unit_dimensions_h', 100)->default(null)->nullable();
            $table->string('unit_weight', 100)->default(null)->nullable();
            $table->string('gross_case_weight', 100)->default(null)->nullable();
            $table->string('case_cube', 100)->default(null)->nullable();
            $table->string('pallet_pattern_tier', 100)->default(null)->nullable();
            $table->string('pallet_pattern_high', 100)->default(null)->nullable();
            $table->string('CS_per_pallet', 100)->default(null)->nullable();
            $table->string('weeks_best_before', 100)->default(null)->nullable();
            $table->string('first_ship_date', 100)->default(null)->nullable();
            $table->string('first_delivery_date', 100)->default(null)->nullable();
            $table->string('tray_UCC', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_mars');
    }
}
