<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelProductTransitMaxCountToPackingMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_materials', function (Blueprint $table) {
            $table->string('channel_ids')->nullable();
            $table->string('product_ids')->nullable();
            $table->string('transit_days')->nullable();
            $table->string('max_item_count')->nullable( );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packaging_materials', function (Blueprint $table) {
            //
        });
    }
}
