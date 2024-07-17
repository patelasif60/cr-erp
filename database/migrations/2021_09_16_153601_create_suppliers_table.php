<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('e_trailer_account_number')->nullable();
            $table->string('order_schedule')->nullable();
            $table->string('order_deadlines')->nullable();
            $table->string('minimums')->nullable();
            $table->text('order_restriction_details')->nullable();
            $table->string('delivery_schedule')->nullable();
            $table->text('lead_time_overview_notes')->nullable();
            $table->string('e_team_purchase_manager')->nullable();
            $table->string('order_method')->nullable();
            $table->string('order_portal_url')->nullable();
            $table->string('order_portal_username')->nullable();
            $table->string('order_portal_password')->nullable();
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
        Schema::dropIfExists('suppliers');
    }
}
