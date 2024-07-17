<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMore4FieldsToSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            $table->text('f_my_each_5000');
            $table->text('f_my_each_10000');
            $table->text('f_my_each_20000');
            $table->text('f_my_each_40000');
            $table->text('terms');
            $table->text('broker_information');
            $table->text('supplier_website');
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
