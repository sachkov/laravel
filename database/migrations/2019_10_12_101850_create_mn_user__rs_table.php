<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMnUserRsTable extends Migration
{
    /**
     * Таблица связей молитвенных нужд и пользователей, которым открыта данная нужда
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mn_user__rs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('mn_id');
            $table->bigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mn_user__rs');
    }
}
