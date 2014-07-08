<?php

use Illuminate\Database\Migrations\Migration;

class CreateAlbumphotosTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_photos', function($table) {
            $table->increments('id');
            $table->integer('album_id')->nullable();
            $table->string('photo', 255);
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
        Schema::drop('album_photos');
    }

}