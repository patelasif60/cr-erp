<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierNestleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_nestle', function (Blueprint $table) {
            $table->id();
            $table->string('notes', 100)->default(null)->nullable();
            $table->string('description', 100)->default(null)->nullable();
            $table->string('material_number', 100)->default(null)->nullable();
            $table->string('pack_size', 100)->default(null)->nullable();
            $table->string('sales_org', 100)->default(null)->nullable();
            $table->string('distribution_channel', 100)->default(null)->nullable();
            $table->string('retrieving_data', 100)->default(null)->nullable();
            $table->string('16_digit_code', 100)->default(null)->nullable();
            $table->string('EDI_UA_code', 100)->default(null)->nullable();
            $table->string('consumer_unit_code', 100)->default(null)->nullable();
            $table->string('PLA_B0_40000', 100)->default(null)->nullable();
            $table->string('PLA_B1_10000_39999', 100)->default(null)->nullable();
            $table->string('PLA_B2_5000_9999', 100)->default(null)->nullable();
            $table->string('PLA_B3_2000_4999', 100)->default(null)->nullable();
            $table->string('ttl_shelf_life', 100)->default(null)->nullable();
            $table->string('gross_wt_order_unit_specs', 100)->default(null)->nullable();
            $table->string('cube_order_unit_specs', 100)->default(null)->nullable();
            $table->string('length_order_unit_specs', 100)->default(null)->nullable();
            $table->string('Width_order_unit_specs', 100)->default(null)->nullable();
            $table->string('height_order_unit_specs', 100)->default(null)->nullable();
            $table->string('CPL_pallet_specs', 100)->default(null)->nullable();
            $table->string('LPP_pallet_specs', 100)->default(null)->nullable();
            $table->string('ttl_cs_pallet_specs', 100)->default(null)->nullable();
            $table->string('length_pallet_specs', 100)->default(null)->nullable();
            $table->string('width_pallet_specs', 100)->default(null)->nullable();
            $table->string('height_pallet_specs', 100)->default(null)->nullable();
            $table->string('length_consumer_unit_dimensions', 100)->default(null)->nullable();
            $table->string('width_consumer_unit_dimensions', 100)->default(null)->nullable();
            $table->string('height_consumer_unit_dimensions', 100)->default(null)->nullable();
            $table->string('country_of_origin', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_nestle');
    }
}
