<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientChannelsInMasterProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_product', function (Blueprint $table) {
            $table->string('chanel_ids')->nullable();
        });

        Schema::table('master_product_queue', function (Blueprint $table) {
            $table->string('chanel_ids')->nullable();
        });

        Schema::table('master_product_history', function (Blueprint $table) {
            $table->string('chanel_ids')->nullable();
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
