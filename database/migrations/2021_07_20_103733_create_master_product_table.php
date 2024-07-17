<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_product', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('parent_ETIN', 100)->default(null)->nullable();
            $table->string('product_listing_name', 100)->default(null)->nullable();
            $table->string('full_product_desc', 100)->default(null)->nullable();
            $table->string('about_this_item', 100)->default(null)->nullable();
            $table->string('manufacturer', 100)->default(null)->nullable();
            $table->string('brand', 100)->default(null)->nullable();
            $table->string('flavor', 100)->default(null)->nullable();
            $table->string('product_name', 100)->default(null)->nullable();
            $table->string('unit_size', 100)->default(null)->nullable();
            $table->string('unit_description', 100)->default(null)->nullable();
            $table->string('pack_form_count', 100)->default(null)->nullable();
            $table->string('item_form_description', 100)->default(null)->nullable();
            $table->string('total_ounces', 100)->default(null)->nullable();
            $table->string('product_category', 100)->default(null)->nullable();
            $table->string('product_subcategory1', 100)->default(null)->nullable();
            $table->string('product_subcategory2', 100)->default(null)->nullable();
            $table->string('product_subcategory3', 100)->default(null)->nullable();
            $table->string('key_product_attributes_&_diet', 100)->default(null)->nullable();
            $table->string('product_tags', 100)->default(null)->nullable();
            $table->string('MFG_shelf_life', 100)->default(null)->nullable();
            $table->string('hazardous_materials', 100)->default(null)->nullable();
            $table->string('storage', 100)->default(null)->nullable();
            $table->string('ingredients', 100)->default(null)->nullable();
            $table->string('allergens', 100)->default(null)->nullable();
            $table->string('prop_65_flag', 100)->default(null)->nullable();
            $table->string('prop_65_ingredient', 100)->default(null)->nullable();
            $table->string('product_temperature', 100)->default(null)->nullable();
            $table->string('manufacturer_product_number', 100)->default(null)->nullable();
            $table->string('upc', 100)->default(null)->nullable();
            $table->string('gtin', 100)->default(null)->nullable();
            $table->string('asin', 100)->default(null)->nullable();
            $table->string('GPC_code', 100)->default(null)->nullable();
            $table->string('GPC_class', 100)->default(null)->nullable();
            $table->string('HS_code', 100)->default(null)->nullable();
            $table->string('weight_lbs', 100)->default(null)->nullable();
            $table->string('length_in', 100)->default(null)->nullable();
            $table->string('width_in', 100)->default(null)->nullable();
            $table->string('height_in', 100)->default(null)->nullable();
            $table->string('country_of_origin', 100)->default(null)->nullable();
            $table->string('package_information', 100)->default(null)->nullable();
            $table->string('cost', 100)->default(null)->nullable();
            $table->string('new_cost', 100)->default(null)->nullable();
            $table->string('new_cost_date', 100)->default(null)->nullable();
            $table->string('status', 100)->default(null)->nullable();
            $table->string('availability', 100)->default(null)->nullable();
            $table->string('dropship_available', 100)->default(null)->nullable();
            $table->string('channel_listing_restrictions', 100)->default(null)->nullable();
            $table->string('POG_flag', 100)->default(null)->nullable();
            $table->string('consignment', 100)->default(null)->nullable();
            $table->string('warehouses_assigned', 100)->default(null)->nullable();
            $table->string('status_date', 100)->default(null)->nullable();
            $table->string('lobs', 100)->default(null)->nullable();
            $table->string('current_supplier', 100)->default(null)->nullable();
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
        Schema::dropIfExists('master_product');
    }
}
