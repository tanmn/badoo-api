<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserprofilesettingsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile_settings', function($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->boolean('online_status_flg');
            $table->boolean('location_flg');
            $table->boolean('real_name_flg');
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
        Schema::drop('user_profile_settings');
    }

}