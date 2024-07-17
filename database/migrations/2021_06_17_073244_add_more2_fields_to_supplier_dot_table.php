<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMore2FieldsToSupplierDotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('supplier_dot', function (Blueprint $table) {
            $table->string('my_each_5000');
            $table->string('my_each_10000');
            $table->string('my_each_20000');
            $table->string('my_each_40000');

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
