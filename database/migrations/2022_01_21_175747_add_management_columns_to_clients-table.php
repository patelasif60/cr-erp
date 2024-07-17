<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagementColumnsToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('inventory_management_notes')->after('inventory_manager')->nullable();
            $table->string('purchasing_management')->after('product_notes')->nullable();
            $table->string('purchasing_management_notes')->nullable();
            $table->string('order_management_notes')->after('order_management')->nullable();
            $table->string('custom_packaging')->nullable();
            $table->string('custom_packaging_notes')->nullable();
            $table->string('channel_owner_notes')->nullable();
            $table->string('price_management_notes')->after('price_manager')->nullable();
            $table->string('customer_service_notes')->after('customer_service')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('inventory_management_notes');
            $table->dropColumn('purchasing_management');
            $table->dropColumn('purchasing_management_notes');
            $table->dropColumn('order_management_notes');
            $table->dropColumn('custom_packaging');
            $table->dropColumn('custom_packaging_notes');
            $table->dropColumn('channel_owner_notes');
            $table->dropColumn('price_management_notes');
            $table->dropColumn('customer_service_notes');
        });
    }
}
