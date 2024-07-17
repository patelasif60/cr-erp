<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddHasBarcodeToPackagingMaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packaging_materials', function (Blueprint $table) {
            $table->integer('has_barcode')->default(1);
        });

        DB::table('packaging_materials')
            ->whereNull('scannable_barcode')
            ->orWhere('scannable_barcode', '')
            ->update(['has_barcode' => 0]);
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
