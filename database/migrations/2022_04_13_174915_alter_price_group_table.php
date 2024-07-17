<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPriceGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('price_group', function (Blueprint $table) {
            $table->decimal('credit_card_fees',8,2)->nullable();
            $table->decimal('marketplace_fees',8,2)->nullable();
            $table->decimal('weight_multiplier',8,2)->nullable();
            $table->decimal('markup_price_group',8,2)->nullable();
            $table->decimal('markup_total_cost',8,2)->nullable();
            $table->decimal('markup_product_materials_cost',8,2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('price_group', function (Blueprint $table) {
            $table->dropColumn('credit_card_fees');
            $table->dropColumn('marketplace_fees');
            $table->dropColumn('weight_multiplier');
        });
    }
}
