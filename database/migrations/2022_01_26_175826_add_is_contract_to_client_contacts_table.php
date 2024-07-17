<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsContractToClientContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->tinyInteger('is_contract')->default(0)->comment('1=yes, 0=no')->after('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->dropColumn('is_contract');
        });
    }
}
