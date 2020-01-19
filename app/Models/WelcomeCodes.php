<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
$table->bigInteger('author_id')->unsigned();    //создатель кода
$table->smallInteger('code');                   //код приглашения
$table->bigInteger('user_id')->unsigned()->nullable();      //зарегистрированный по коду пользователь
 */

class WelcomeCodes extends Model
{
    protected $table = 'welcome_codes';
    //
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
