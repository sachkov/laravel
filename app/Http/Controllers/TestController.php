<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Загрузка тестовой страницы
     *
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request)
    {
        /**
             * После проверки уже можешь получать любое свойство модели
             * пользователя через фасад Auth, например name - (должен быть подключен
             * посредник $this->middleware('auth'); и use Auth;)
             */
        /*if (Auth::check()) {
         *   $user = Auth::user()->name;
        *}
        */
        /*Второй вариант получения пользователя из запроса (use Illuminate\Http\Request;
        *и public function start(Request $request))
        */
        if ($request->user())
        {
            // $request->user() возвращает объект пользователя...
            $user = $request->user()->name;
        }
        
        
        echo "<pre>";var_dump($user);echo "</pre>";
        //return view('test');
    }
    
}
