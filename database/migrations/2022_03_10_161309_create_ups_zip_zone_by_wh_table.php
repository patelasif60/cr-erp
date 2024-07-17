<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpsZipZoneByWhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_zip_zone_by_wh', function (Blueprint $table) {
            $table->id();
            $table->string('state')->nullable();
            $table->decimal('zip_3')->nullable();
            $table->decimal('zone_wi')->nullable();
            $table->decimal('transit_days_wi')->nullable();
            $table->decimal('zone_pa')->nullable();
            $table->decimal('transit_days_pa')->nullable();
            $table->decimal('zone_nv')->nullable();
            $table->decimal('transit_days_nv')->nullable();
            $table->decimal('zone_ok')->nullable();
            $table->decimal('transit_days_ok')->nullable();
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
        Schema::dropIfExists('ups_zip_zone_by_wh');
    }
}
