<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->tinyInteger('is_phone_etailer')->default(0);
            $table->string('phone_etailer_notes')->nullable();
            $table->tinyInteger('is_email_etailer')->default(0);
            $table->string('email_etailer_notes')->nullable();
            $table->tinyInteger('is_live_chat_etailer')->default(0);
            $table->string('live_chat_etailer_notes')->nullable();
            $table->tinyInteger('is_miscellaneous_etailer')->default(0);
            $table->string('miscellaneous_etailer_notes')->nullable();
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
        Schema::dropIfExists('customer_services');
    }
}
