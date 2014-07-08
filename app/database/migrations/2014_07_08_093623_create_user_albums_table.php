<?php

use Illuminate\Database\Migrations\Migration;

class CreateUseralbumsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_albums', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('album_name', 255);
            $table->boolean('public_type');
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
        Schema::drop('user_albums');
    }

}