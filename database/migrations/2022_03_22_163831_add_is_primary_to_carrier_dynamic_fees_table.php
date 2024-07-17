<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPrimaryToCarrierDynamicFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carrier_dynamic_fees', function (Blueprint $table) {
            $table->tinyInteger('is_primary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carrier_dynamic_fees', function (Blueprint $table) {
            $table->dropColumn('is_primary');
        });
    }
}
