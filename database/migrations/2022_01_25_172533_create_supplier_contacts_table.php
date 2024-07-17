<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('cell_phone')->nullable();
            $table->string('contact_notes')->nullable();
            $table->tinyInteger('is_primary')->comment('1=yes, 0=no')->default(0);
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
        Schema::dropIfExists('supplier_contacts');
    }
}
