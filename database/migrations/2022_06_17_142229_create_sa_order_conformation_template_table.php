<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaOrderConformationTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sa_order_conformation_template', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100)->nullable();
            $table->string('sku', 100)->nullable();
            $table->string('qtyShipped', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->string('trackingNumber', 100)->nullable();
            $table->string('shipMethod', 100)->nullable();
            $table->string('packageCode', 100)->nullable();
            $table->string('quantityCancelled', 100)->nullable();
            $table->string('cancellationReason', 100)->nullable();
            $table->string('shippedDate', 100)->nullable();
            $table->string('shippedCost', 100)->nullable();
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
        Schema::dropIfExists('sa_order_conformation_template');
    }
}
