<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueInContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('title')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('office_phone')->nullable()->change();
            $table->string('cell_phone')->nullable()->change();
            $table->string('contact_note')->nullable()->change();
            $table->integer('is_primary')->nullable()->change();
            $table->integer('is_contract')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
