<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersearchsettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_search_settings', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('search_purpose');
            $table->boolean('search_gender')->nullable();
            $table->integer('search_age_from')->nullable();
            $table->integer('search_age_to')->nullable();
            $table->dateTime('created');
            $table->dateTime('modified')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_search_settings');
    }

}