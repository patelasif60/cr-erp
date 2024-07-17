<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            $table->string('my_case_pricing_UOM');
            $table->string('my_case_5000');
            $table->string('my_case_10000');
            $table->string('my_case_20000');
            $table->string('my_case_40000');

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
