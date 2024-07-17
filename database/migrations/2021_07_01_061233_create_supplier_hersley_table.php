<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierHersleyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_hersley', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('etailer_stock_status', 100)->default(null)->nullable();
            $table->string('list_status', 100)->default(null)->nullable();
            $table->string('acquisition_cost', 100)->default(null)->nullable();
            $table->string('promoted_product_groups', 100)->default(null)->nullable();
            $table->string('brand', 100)->default(null)->nullable();
            $table->string('item_no', 100)->default(null)->nullable();
            $table->string('description', 100)->default(null)->nullable();
            $table->string('pkg', 100)->default(null)->nullable();
            $table->string('UPC', 100)->default(null)->nullable();
            $table->string('expanded_UPC', 100)->default(null)->nullable();
            $table->string('each_qty', 100)->default(null)->nullable();
            $table->string('box_qty', 100)->default(null)->nullable();
            $table->string('total_each_qty', 100)->default(null)->nullable();
            $table->string('price_sch_2_1000_5_999_lbs', 100)->default(null)->nullable();
            $table->string('price_sch_3_6000_24_999_lbs', 100)->default(null)->nullable();
            $table->string('price_sch_4_25000_lbs', 100)->default(null)->nullable();
            $table->string('net_wt', 100)->default(null)->nullable();
            $table->string('net_wt_UOM', 100)->default(null)->nullable();
            $table->string('gross_wt', 100)->default(null)->nullable();
            $table->string('gross_wt_UOM', 100)->default(null)->nullable();
            $table->string('dim_L_or_D', 100)->default(null)->nullable();
            $table->string('dim_W', 100)->default(null)->nullable();
            $table->string('dim_H', 100)->default(null)->nullable();
            $table->string('dim_UOM', 100)->default(null)->nullable();
            $table->string('cube', 100)->default(null)->nullable();
            $table->string('cube_UOM', 100)->default(null)->nullable();
            $table->string('cases_per_layer', 100)->default(null)->nullable();
            $table->string('layers_per_pallet', 100)->default(null)->nullable();
            $table->string('approx_pallet_wt', 100)->default(null)->nullable();
            $table->string('sale_status', 100)->default(null)->nullable();
            $table->string('PACKTYPE_SEQ', 100)->default(null)->nullable();
            $table->string('SEQ_LEVEL', 100)->default(null)->nullable();
            $table->string('SEQUENCE1', 100)->default(null)->nullable();
            $table->string('SEQUENCE2', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_hersley');
    }
}
