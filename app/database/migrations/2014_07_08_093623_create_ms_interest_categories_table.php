<?php

use Illuminate\Database\Migrations\Migration;

class CreateMsinterestcategoriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_interest_categories', function($table) {
            $table->increments('id');
            $table->string('category_name', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ms_interest_categories');
    }

}