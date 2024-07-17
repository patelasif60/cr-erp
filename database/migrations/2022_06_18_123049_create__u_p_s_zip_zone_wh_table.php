<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUPSZipZoneWhTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ups_zip_zone_wh', function (Blueprint $table) {
            $table->id();
            $table->string('state', 100)->nullable();
            $table->string('zip_3', 100)->nullable();
            $table->string('zone_WI', 100)->nullable();
            $table->string('transit_days_WI', 100)->nullable();
            $table->string('zone_PA', 100)->nullable();
            $table->string('transit_days_PA', 100)->nullable();
            $table->string('zone_NV', 100)->nullable();
            $table->string('transit_days_NV', 100)->nullable();
            $table->string('zone_OKC', 100)->nullable();
            $table->string('transit_days_OKC', 100)->nullable();
            $table->string('wh_assigned', 100)->nullable();
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
        Schema::dropIfExists('_u_p_s_zip_zone_wh');
    }
}
