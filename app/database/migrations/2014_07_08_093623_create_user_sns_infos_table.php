<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersnsinfosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_sns_infos', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('sns_type', 255);
            $table->string('sns_id', 255);
            $table->text('auth_token');
            $table->text('auth_token_secret');
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
        Schema::drop('user_sns_infos');
    }

}