<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarrierAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrier_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->integer('carrier_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('api_key')->nullable();
            $table->string('account_rules')->nullable();
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
        Schema::dropIfExists('carrier_accounts');
    }
}
