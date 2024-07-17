<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductSelectionFieldInCountryOfOrigin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('country_of_origin', function (Blueprint $table) {
            $table->index('country_of_origin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('country_of_origin', function (Blueprint $table) {
            $table->dropIndex('country_of_origin');
        });
    }
}
