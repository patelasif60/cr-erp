<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProductHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_product_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ETIN');
            $table->string('parent_ETIN')->default(null)->nullable();
			$table->string('lobs')->default(null)->nullable();
            $table->string('product_listing_name')->default(null)->nullable();
            $table->text('full_product_desc')->default(null)->nullable();
            $table->text('about_this_item')->default(null)->nullable();
            $table->string('manufacturer')->default(null)->nullable();
            $table->string('brand')->default(null)->nullable();
            $table->string('flavor')->default(null)->nullable();
            $table->string('product_type')->default(null)->nullable();
            $table->string('unit_size')->default(null)->nullable();
            $table->string('unit_description')->default(null)->nullable();
            $table->string('pack_form_count')->default(null)->nullable();
            $table->string('unit_in_pack')->default(null)->nullable();
            $table->string('item_form_description')->default(null)->nullable();
            $table->string('total_ounces')->default(null)->nullable();
            $table->string('product_category')->default(null)->nullable();
            $table->string('product_subcategory1')->default(null)->nullable();
            $table->string('product_subcategory2')->default(null)->nullable();
            $table->string('product_subcategory3')->default(null)->nullable();
            $table->string('key_product_attributes_diet')->default(null)->nullable();
            $table->string('product_tags')->default(null)->nullable();
            $table->string('MFG_shelf_life')->default(null)->nullable();
            $table->string('hazardous_materials')->default(null)->nullable();
            $table->string('storage')->default(null)->nullable();
            $table->text('ingredients')->default(null)->nullable();
            $table->string('allergens')->default(null)->nullable();
            $table->string('prop_65_flag')->default(null)->nullable();
            $table->string('prop_65_ingredient')->default(null)->nullable();
            $table->string('product_temperature')->default(null)->nullable();
            $table->string('supplier_product_number')->default(null)->nullable();
            $table->string('manufacture_product_number')->default(null)->nullable();
            $table->string('upc')->default(null)->nullable();
            $table->string('gtin')->default(null)->nullable();
            $table->string('asin')->default(null)->nullable();
            $table->string('GPC_code')->default(null)->nullable();
            $table->string('GPC_class')->default(null)->nullable();
            $table->string('HS_code')->default(null)->nullable();
            $table->string('weight')->default(null)->nullable();
            $table->string('length')->default(null)->nullable();
            $table->string('width')->default(null)->nullable();
            $table->string('height')->default(null)->nullable();
            $table->string('country_of_origin')->default(null)->nullable();
            $table->string('package_information')->default(null)->nullable();
            $table->string('cost')->default(null)->nullable();
            $table->string('new_cost')->default(null)->nullable();
            $table->string('new_cost_date')->default(null)->nullable();
            $table->string('status')->default(null)->nullable();
            $table->string('etailer_availability')->default(null)->nullable();
            $table->string('dropship_available')->default(null)->nullable();
            $table->string('channel_listing_restrictions')->default(null)->nullable();
            $table->string('POG_flag')->default(null)->nullable();
            $table->string('consignment')->default(null)->nullable();
            $table->string('warehouses_assigned')->default(null)->nullable();
            $table->string('status_date')->default(null)->nullable();            
            $table->string('current_supplier')->default(null)->nullable();
            $table->string('supplier_status')->default(null)->nullable();
            $table->string('product_listing_ETIN')->default(null)->nullable();
            $table->string('alternate_ETINs')->default(null)->nullable();
            $table->boolean('is_approve')->default(null)->nullable();
            $table->boolean('is_edit')->default(null)->nullable();
            $table->string('cancel_reason')->default(null)->nullable();
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
        Schema::dropIfExists('master_product_history');
    }
}
