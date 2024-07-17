<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsUpdateFeedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_update_feed', function (Blueprint $table) {
            $table->increments('id');
			$table->string('feed_title');
			$table->longText('feed_description');
			$table->string('feed_auth_name');
			$table->integer('feed_auth_id');
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
        Schema::dropIfExists('news_update_feed');
    }
}
