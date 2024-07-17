<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMore3FieldsToSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            $table->text('f_my_each_pricing_date');
            $table->text('f_my_each_pricing_UOM');

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
