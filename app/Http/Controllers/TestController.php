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
        /*if ($request->user())
        {
            // $request->user() возвращает объект пользователя...
            $user = $request->user()->name;
        }
        echo "<pre>";var_dump($request->user());echo "</pre>";
        */
        $table = new \App\Models\mn_user_R;
        $t = $table::all()->toArray();
        
        $User_m = new \App\User;
        //$arMN = $MN_model::all();
        $Users = $User_m::all()->toArray();
        //$ar=[1,2,3];
              
        $MN_model = new \App\Models\MN;
        
        //$mn1 = $MN_model::find(1);

        //$mn1->signed_users()->attach(2);
        $a = $MN_model::find(1)->signed_users->toArray();
        //$c = get_class($a);
        
        $MN = $MN_model::all()->toArray();
        
        $ar["USERS"] = $Users;
        $ar["MN"] = $MN;
        $ar["test"] = $a;
        $ar["pivot_table"] = $t;
        
        
        return view('test', ['ar'=>$ar]);
    }
    
}
