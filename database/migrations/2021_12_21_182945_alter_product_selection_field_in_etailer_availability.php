<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductSelectionFieldInEtailerAvailability extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('etailer_availability', function (Blueprint $table) {
            $table->index('etailer_availability');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etailer_availability', function (Blueprint $table) {
            $table->dropIndex('etailer_availability');
        });
    }
}
