<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_or_channel')->nullable();
            $table->string('company_name')->nullable();
            $table->string('main_point_of_contact')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->nullable();
            $table->string('business_relationship')->nullable();
            $table->string('account_manager')->nullable();
            $table->string('sales_manager')->nullable();
            $table->string('product_locations')->nullable();
            $table->string('invenory_owner')->nullable();
            $table->string('inventory_manager')->nullable();
            $table->string('reorder_details')->nullable();
            $table->string('packaging_specifis')->nullable();
            $table->string('technical_connections')->nullable();
            $table->string('client_ipc_account')->nullable();
            $table->string('order_channels')->nullable();
            $table->string('stores_sold_in')->nullable();
            $table->string('store_owner')->nullable();
            $table->string('price_manager')->nullable();
            $table->string('customer_service')->nullable();
            $table->string('order_management')->nullable();
            $table->string('account_notes')->nullable();
            $table->string('lob')->nullable();
            $table->string('consignments')->nullable();
            $table->text('product_notes')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
