<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeParentEtinColumnLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->string('parent_ETIN', 255)->change();
        });
        Schema::table('master_product_history', function (Blueprint $table) {
            $table->string('parent_ETIN', 255)->change();
        });
        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->string('parent_ETIN', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
