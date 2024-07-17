<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToClientManagemetTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_channel_configurations', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after('id')->nullable();
        });
        Schema::table('client_account_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after('id')->nullable();
        });
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after('id')->nullable();
        });
        Schema::table('client_events', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after('id')->nullable();
        });
        Schema::table('client_warehouse_and_fulfillments', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_channel_configurations', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
        Schema::table('client_account_notes', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
        Schema::table('client_contacts', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
        Schema::table('client_events', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
        Schema::table('client_warehouse_and_fulfillments', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
}
