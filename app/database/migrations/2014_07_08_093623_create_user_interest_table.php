<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserinterestTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_interest', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('interest_id');
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
        Schema::drop('user_interest');
    }

}