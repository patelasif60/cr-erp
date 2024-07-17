<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldIsApprovedInMasterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->timestamp('approved_date')->after('is_approve')->nullable();
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->timestamp('approved_date')->after('is_approve')->nullable();
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->timestamp('approved_date')->after('is_approve')->nullable();
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
            //
        });
    }
}
