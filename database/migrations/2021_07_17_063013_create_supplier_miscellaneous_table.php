<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierMiscellaneousTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_miscellaneous', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_ID', 100)->default(null)->nullable();
            $table->string('ETIN', 100)->default(null)->nullable();
            $table->string('etailer_stock_status', 100)->default(null)->nullable();
            $table->string('list_status', 100)->default(null)->nullable();
            $table->string('acquisition_cost', 100)->default(null)->nullable();
            $table->string('product_description', 100)->default(null)->nullable();
            $table->string('full_product_description', 100)->default(null)->nullable();
            $table->string('about_this_item', 100)->default(null)->nullable();
            $table->string('manufacturer', 100)->default(null)->nullable();
            $table->string('brand', 100)->default(null)->nullable();
            $table->string('unit_size', 100)->default(null)->nullable();
            $table->string('unit_description', 100)->default(null)->nullable();
            $table->string('pack_form_count', 100)->default(null)->nullable();
            $table->string('product_category', 100)->default(null)->nullable();
            $table->string('product_subcategory1', 100)->default(null)->nullable();
            $table->string('product_subcategory2', 100)->default(null)->nullable();
            $table->string('product_subcategory3', 100)->default(null)->nullable();
            $table->string('key_product_attributes_diet', 100)->default(null)->nullable();
            $table->string('MFG_shelf_life', 100)->default(null)->nullable();
            $table->string('hazardous_materials', 100)->default(null)->nullable();
            $table->string('storage', 100)->default(null)->nullable();
            $table->string('ingredients', 100)->default(null)->nullable();
            $table->string('allergens', 100)->default(null)->nullable();
            $table->string('product_temperature', 100)->default(null)->nullable();
            $table->string('supplier_product_number', 100)->default(null)->nullable();
            $table->string('manufacturer_product_number', 100)->default(null)->nullable();
            $table->string('UPC', 100)->default(null)->nullable();
            $table->string('GTIN', 100)->default(null)->nullable();
            $table->string('weight', 100)->default(null)->nullable();
            $table->string('length', 100)->default(null)->nullable();
            $table->string('width', 100)->default(null)->nullable();
            $table->string('height', 100)->default(null)->nullable();
            $table->string('country_of_origin', 100)->default(null)->nullable();
            $table->string('package_information', 100)->default(null)->nullable();
            $table->string('supplier_status', 100)->default(null)->nullable();
            $table->string('cost', 100)->default(null)->nullable();
            $table->string('new_cost', 100)->default(null)->nullable();
            $table->string('new_cost_date', 100)->default(null)->nullable();
            $table->string('misc_1_description', 100)->default(null)->nullable();
            $table->string('misc_1_data', 100)->default(null)->nullable();
            $table->string('misc_2_description', 100)->default(null)->nullable();
            $table->string('misc_2_data', 100)->default(null)->nullable();
            $table->string('misc_3_description', 100)->default(null)->nullable();
            $table->string('misc_3_data', 100)->default(null)->nullable();
            $table->string('misc_4_description', 100)->default(null)->nullable();
            $table->string('misc_4_data', 100)->default(null)->nullable();
            $table->string('misc_5_description', 100)->default(null)->nullable();
            $table->string('misc_5_data', 100)->default(null)->nullable();
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
        Schema::dropIfExists('supplier_miscellaneous');
    }
}
