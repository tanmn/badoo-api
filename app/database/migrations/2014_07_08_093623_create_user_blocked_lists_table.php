<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserblockedlistsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_blocked_lists', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_blocked_id');
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
        Schema::drop('user_blocked_lists');
    }

}