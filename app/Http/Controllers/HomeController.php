<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return view('home', []);
        $MN_model = new \App\Models\MN;
        //$arMN = $MN_model::all();
        $arMN = $MN_model::whereNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();
        
        return view('home', ["arMN"=>$arMN]);
        
    }
    
    /**
     * Тестовая страница для отработки методов
     */
    public function testpage()
    {
        return view('test');
    }
}
