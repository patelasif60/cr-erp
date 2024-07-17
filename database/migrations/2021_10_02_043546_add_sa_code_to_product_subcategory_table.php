<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSaCodeToProductSubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_subcategory', function (Blueprint $table) {
            $table->string('sc1_sa_code')->nullable()->after('sub_category_1');
            $table->string('sc2_sa_code')->nullable()->after('sub_category_2');
            $table->string('sc3_sa_code')->nullable()->after('sub_category_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_subcategory', function (Blueprint $table) {
            //
        });
    }
}
