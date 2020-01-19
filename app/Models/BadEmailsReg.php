<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/*
Модель для таблицы отслеживания неудачных регистраций
$table->string('email')->unique();      //электронный адрес введенный с не верным кодом
$table->smallInteger('tries');          //кол-во попыток ввести код
 */

class BadEmailsReg extends Model
{
    protected $table = 'number_of_tries';
    //
}
