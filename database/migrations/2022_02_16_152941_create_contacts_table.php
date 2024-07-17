<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('name')->nllable();
            $table->string('title')->nllable();
            $table->string('email')->nllable();
            $table->string('office_phone')->nllable();
            $table->string('cell_phone')->nllable();
            $table->string('contact_note')->nllable();
            $table->tinyInteger('is_primary')->nllable();
            $table->tinyInteger('is_contract')->nllable();
            $table->timestamps();
        });

        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('supplier_contacts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
