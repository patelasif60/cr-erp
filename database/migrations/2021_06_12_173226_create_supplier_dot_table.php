<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_dot', function (Blueprint $table) {
            $table->id();
            $table->string('ETIN');
            $table->string('etailer_stock_status');
            $table->string('list_status');
            $table->string('acquisition_cost');
            $table->string('corporate_supplier');
            $table->string('product_line');
            $table->string('brand');
            $table->string('availability');
            $table->string('dot_item');
            $table->string('manufacturer_item');
            $table->string('GTIN/UPC');
            $table->string('dot_abbreviated_description');
            $table->string('item_description');
            $table->string('pack_size');
            $table->string('item_buying_group');
            $table->string('new_item');
            $table->string('proprietary');
            $table->string('nutritional_information');
            $table->string('image_available');
            $table->string('diet_type');
            $table->string('class_of_Trade');
            $table->string('temperature');
            $table->string('product_type');
            $table->string('country_of_origin');
            $table->string('IFDA_category');
            $table->string('IFDA_class');
            $table->string('GPC_code');
            $table->string('GPC_class');
            $table->string('category');
            $table->string('subcategory');
            $table->string('hazMat_item');
            $table->string('factory_direct_ship');
            $table->string('item_available_via_drop_ship');
            $table->string('mfg_shelf_life');
            $table->string('layer');
            $table->string('pallet');
            $table->string('shipping_weight');
            $table->string('product_weight');
            $table->string('shipping_each_weight');
            $table->string('product_each_weight');
            $table->string('cube');
            $table->string('length');
            $table->string('width');
            $table->string('height');
            $table->string('minimum_order_quantity');
            $table->string('buy_in_multiples');
            $table->string('price_date_of_order');
            $table->string('FOB');
            $table->string('promo/allow');
            $table->string('current_my_price_date');
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
        Schema::dropIfExists('supplier_dot');
    }
}
