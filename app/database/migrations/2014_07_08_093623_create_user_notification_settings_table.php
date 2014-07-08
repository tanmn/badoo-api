<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsernotificationsettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notification_settings', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('new_message_flg');
            $table->boolean('new_visitor_flg');
            $table->boolean('new_liked_flg');
            $table->boolean('new_fav_flg');
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
        Schema::drop('user_notification_settings');
    }

}