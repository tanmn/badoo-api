<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsernotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_notifications', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_notification_id');
            $table->integer('type_notification');
            $table->boolean('read_flg');
            $table->boolean('delete_flg');
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
        Schema::drop('user_notifications');
    }

}