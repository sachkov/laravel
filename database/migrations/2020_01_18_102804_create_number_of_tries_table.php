<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNumberOfTriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('number_of_tries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('email')->unique();      //электронный адрес введенный с не верным кодом
            $table->smallInteger('tries');          //кол-во попыток ввести код
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('number_of_tries');
    }
}
