<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierDryersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_dryers', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('etailer_stock_status', 100)->default(null)->nullable();
            $table->string('list_status', 100)->default(null)->nullable();
            $table->string('acquisition_cost', 100)->default(null)->nullable();
            $table->string('B', 100)->default(null)->nullable();
            $table->string('no', 100)->default(null)->nullable();
            $table->string('P', 100)->default(null)->nullable();
            $table->string('trak_num', 100)->default(null)->nullable();
            $table->string('archive', 100)->default(null)->nullable();
            $table->string('brand_name', 100)->default(null)->nullable();
            $table->string('sub_brand', 100)->default(null)->nullable();
            $table->string('im_gr', 100)->default(null)->nullable();
            $table->string('pack_description', 100)->default(null)->nullable();
            $table->string('fanc_name', 100)->default(null)->nullable();
            $table->string('std_ID', 100)->default(null)->nullable();
            $table->string('flavor_declaration', 100)->default(null)->nullable();
            $table->string('art_flv_clr_declar', 100)->default(null)->nullable();
            $table->string('vol_fl_oz', 100)->default(null)->nullable();
            $table->string('vol_fl_oz_tot', 100)->default(null)->nullable();
            $table->string('wt_pc_g', 100)->default(null)->nullable();
            $table->string('unts_cart', 100)->default(null)->nullable();
            $table->string('PCS_CS', 100)->default(null)->nullable();
            $table->string('bndl_cs_per_bndl', 100)->default(null)->nullable();
            $table->string('gallons', 100)->default(null)->nullable();
            $table->string('pint', 100)->default(null)->nullable();
            $table->string('quart', 100)->default(null)->nullable();
            $table->string('PACKVAR_FLOZ_DEC', 100)->default(null)->nullable();
            $table->string('PACKVAR_FLOZ_mL', 100)->default(null)->nullable();
            $table->string('PACKVAR_FLOZ_Servings', 100)->default(null)->nullable();
            $table->string('imrex', 100)->default(null)->nullable();
            $table->string('globe', 100)->default(null)->nullable();
            $table->string('UPC', 100)->default(null)->nullable();
            $table->string('consumer_code', 100)->default(null)->nullable();
            $table->string('case_code', 100)->default(null)->nullable();
            $table->string('kosher', 100)->default(null)->nullable();
            $table->string('type_kosher', 100)->default(null)->nullable();
            $table->string('y_not', 100)->default(null)->nullable();
            $table->string('claim_description', 100)->default(null)->nullable();
            $table->string('comparative_statement', 100)->default(null)->nullable();
            $table->string('disclosure_statement', 100)->default(null)->nullable();
            $table->string('previous_claim_and_disclosure', 100)->default(null)->nullable();
            $table->string('warning_statement', 100)->default(null)->nullable();
            $table->string('ingredient_statement', 100)->default(null)->nullable();
            $table->string('spacer1', 100)->default(null)->nullable();
            $table->string('spacer2', 100)->default(null)->nullable();
            $table->string('nutrition_facts_panel', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_dryers');
    }
}
