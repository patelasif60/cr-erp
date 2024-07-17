<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagingCompontentsSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packaging_compontents_setting', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_packaging_material_id')->nullable();
            $table->unsignedInteger('child_packaging_materials_id')->nullable();
            $table->unsignedInteger('product_temperature_id')->nullable();
            $table->double('qty')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packaging_compontents_setting');
    }
}
