<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWelcomeCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('welcome_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->bigInteger('author_id')->unsigned();    //создатель кода
            $table->smallInteger('code');                   //код приглашения
            $table->bigInteger('user_id')->unsigned()->nullable();      //зарегистрированный по коду пользователь
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('welcome_codes');
    }
}
