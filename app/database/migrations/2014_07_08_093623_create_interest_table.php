<?php

use Illuminate\Database\Migrations\Migration;

class CreateInterestTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interest', function($table) {
            $table->increments('id');
            $table->integer('ms_interest_category_id')->nullable();
            $table->string('name', 255);
            $table->integer('used_time');
            $table->boolean('approve_flg');
            $table->dateTime('created')->nullable();
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
        Schema::drop('interest');
    }

}