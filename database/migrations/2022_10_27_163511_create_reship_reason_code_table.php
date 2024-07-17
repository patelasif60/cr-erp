<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReshipReasonCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reship_reason_code', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->string('code');
            $table->timestamps();
        });

        DB::table('reship_reason_code')->insert([
            [
                'reason' => 'Weekend Hold',
                'code' => '0'
            ],
            [
                'reason' => 'Missing Items',
                'code' => '1'
            ],
            [
                'reason' => 'Incorrect Product',
                'code' => '2'
            ],
            [
                'reason' => 'Entire Order Melted',
                'code' => '3'
            ],
            [
                'reason' => 'Partial Order Melted',
                'code' => '4'
            ],
            [
                'reason' => 'Incorrect Shipping Label',
                'code' => '5'
            ],
            [
                'reason' => 'Expired Product',
                'code' => '6'
            ],
            [
                'reason' => 'Damaged Product',
                'code' => '7'
            ],
            [
                'reason' => 'Package Damaged',
                'code' => '8'
            ],
            [
                'reason' => 'Package Lost',
                'code' => '9'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reship_reason_code');
    }
}
