<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserlikedlistTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_liked_list', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_like_id');
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
        Schema::drop('user_liked_list');
    }

}