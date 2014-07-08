<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserfavlistsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_fav_lists', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('fav_user_id');
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
        Schema::drop('user_fav_lists');
    }

}