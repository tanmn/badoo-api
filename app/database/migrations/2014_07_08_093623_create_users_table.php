<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function($table) {
            $table->increments('id');
            $table->string('user_api_cd', 200)->nullable();
            $table->string('phone', 128);
            $table->string('password', 128)->nullable();
            $table->dateTime('last_active');
            $table->string('password_old', 200)->nullable();
            $table->integer('type_login');
            $table->string('fb_id', 100)->nullable();
            $table->string('tw_id', 100)->nullable();
            $table->string('gg_id', 100)->nullable();
            $table->text('access_token')->nullable();
            $table->dateTime('last_login');
            $table->boolean('deleted_flg');
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
        Schema::drop('users');
    }

}