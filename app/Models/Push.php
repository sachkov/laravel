<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
    $table->increments('id');
    $table->timestamps();
    $table->string('token', 100)->nullable();
    $table->integer('user_id')->unsigned();
    $table->char('site', 10)->nullable();
*/
class Push extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
