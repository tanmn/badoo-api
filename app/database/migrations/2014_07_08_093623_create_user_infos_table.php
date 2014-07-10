<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserinfosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('name', 100)->nullable();
            $table->double('lat')->nullable();
            $table->string('country', 200)->nullable();
            $table->double('lng')->nullable();
            $table->string('location', 255)->nullable();
            $table->boolean('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->text('thinking')->nullable();
            $table->text('school')->nullable();
            $table->text('company')->nullable();
            $table->integer('status_sex')->nullable();
            $table->text('address')->nullable();
            $table->string('avatar', 200)->nullable();
            $table->string('nameSocial_gg', 200)->nullable();
            $table->string('nameSocial_fb', 200)->nullable();
            $table->string('nameSocial_tw', 200)->nullable();
            $table->string('data_search', 255)->nullable();
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
        Schema::drop('user_infos');
    }

}