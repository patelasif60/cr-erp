<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('notification_type')->default('in_app');
            $table->string('product_management')->nullable();
            $table->string('order_by_client')->nullable();
            $table->string('order_by_order_type')->nullable();
            $table->string('order_by_shipping_speed')->nullable();
            $table->string('inventory_low_stock')->nullable();
            $table->string('inventory_high_stock')->nullable();
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
        Schema::dropIfExists('user_notification_settings');
    }
}
