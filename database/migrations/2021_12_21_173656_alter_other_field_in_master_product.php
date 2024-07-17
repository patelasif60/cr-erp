<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOtherFieldInMasterProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->index(['product_listing_name', 'manufacturer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->dropIndex(['product_listing_name', 'manufacturer']);
        });
    }
}
