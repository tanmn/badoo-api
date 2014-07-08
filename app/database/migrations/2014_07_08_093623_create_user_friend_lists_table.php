<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserfriendlistsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friend_lists', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_friend_id');
            $table->integer('accepted_flg');
            $table->boolean('deleted_flg');
            $table->integer('read_flg')->nullable();
            $table->dateTime('created');
            $table->dateTime('modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_friend_lists');
    }

}