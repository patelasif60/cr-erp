<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderSummary2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_summary', function (Blueprint $table) {
            $table->id();
            $table->string('etailer_order_number', 100)->nullable();
            $table->string('channel_order_number', 100)->nullable();
            $table->string('order_source', 100)->nullable();
            $table->string('channel_type', 100)->nullable();
            $table->string('purchase_date', 100)->nullable();
            $table->string('customer_number', 100)->nullable();
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_email', 100)->nullable();
            $table->string('customer_phone', 100)->nullable();
            $table->string('ship_to_name', 100)->nullable();
            $table->string('ship_to_address_type', 100)->nullable();
            $table->string('ship_to_address1', 100)->nullable();
            $table->string('ship_to_address2', 100)->nullable();
            $table->string('ship_to_address3', 100)->nullable();
            $table->string('ship_to_city', 100)->nullable();
            $table->string('ship_to_state', 100)->nullable();
            $table->string('ship_to_zip', 100)->nullable();
            $table->string('ship_to_country', 100)->nullable();
            $table->string('ship_to_phone', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->string('delivery_notes', 100)->nullable();
            $table->string('customer_shipping_price', 100)->nullable();
            $table->string('gift_message', 100)->nullable();
            $table->string('sales_tax', 100)->nullable();
            $table->string('shipping_tax', 100)->nullable();
            $table->string('shipping_discount_type', 100)->nullable();
            $table->string('shipping_discount_amount', 100)->nullable();
            $table->string('channel_estimated_ship_date', 100)->nullable();
            $table->string('channel_estimated_delivery_date', 100)->nullable();
            $table->string('is_amazon_prime', 100)->nullable();
            $table->string('paypal_transaction_ids', 100)->nullable();
            $table->string('customer_vat', 100)->nullable();
            $table->string('currency', 100)->nullable();
            $table->string('ship_by_date', 100)->nullable();
            $table->string('order_total_price', 100)->nullable();
            $table->string('order_status', 100)->nullable();
            $table->string('complete_date', 100)->nullable();
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
        Schema::dropIfExists('order_summary2');
    }
}
