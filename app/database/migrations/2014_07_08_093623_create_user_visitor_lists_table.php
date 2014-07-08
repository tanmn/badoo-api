<?php

use Illuminate\Database\Migrations\Migration;

class CreateUservisitorlistsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_visitor_lists', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('user_visitor_id');
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
        Schema::drop('user_visitor_lists');
    }

}