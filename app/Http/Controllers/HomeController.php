<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

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
        /*$MN_model = new \App\Models\MN;

        $arMN = $MN_model::whereNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();*/
        
        return view('home');
        
    }
    
    /*
     * Список молитвенных нужды, которыми поделились
     * 
     */
    public function prayersList()
    {
        $MN = DB::table("mn")
            ->join('mn_user__rs', 'mn.id', '=', 'mn_user__rs.mn_id')
            ->join('users', 'mn.author_id', '=', 'users.id')
            ->select('mn.*', 'users.name as author_name')
            ->where('mn_user__rs.user_id', Auth::user()->id)
            ->whereNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();
            
        return view('prayersList', ["arMN"=>$MN]);
    }
    
    /*
     * Список моих завершенных молитв
     */
    public function prayersEnd()
    {
        $MN_model = new \App\Models\MN;
        $arMN = $MN_model::whereNotNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();
        
        return view('prayersEnd', ["arMN"=>$arMN]);
    }
    /**
     * Тестовая страница для отработки методов
     */
    public function testpage()
    {
        return view('test');
    }
}
