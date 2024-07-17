<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsToSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            $table->string('future_price_dot_promotion');
            $table->string('future_price_supplier_promotion');
            $table->string('future_price_buying_group_promotion');
            $table->string('future_price_swell_allowance');
            $table->string('each_my_pricing_UOM');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            //
        });
    }
}
