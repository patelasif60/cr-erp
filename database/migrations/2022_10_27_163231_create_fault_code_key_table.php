<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaultCodeKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fault_code_key', function (Blueprint $table) {
            $table->id();
            $table->string('fault');
            $table->string('code');
            $table->timestamps();
        });

        DB::table('fault_code_key')->insert([
            [
                'fault' => 'e-tailer warehouse',
                'code' => 'XE'
            ],
            [
                'fault' => 'e-tailer AM',
                'code' => 'XA'
            ],
            [
                'fault' => 'Client',
                'code' => 'XY'
            ],
            [
                'fault' => 'Consumer',
                'code' => 'XX'
            ],
            [
                'fault' => 'Carrier',
                'code' => 'XC'
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
        Schema::dropIfExists('fault_code_key');
    }
}
