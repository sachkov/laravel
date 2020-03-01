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
        /*
        $MN_model = new \App\Models\MN;
        $prayers = $MN_model::where('author_id', Auth::user()->id)
                ->select(DB::raw('mn.*, count(mn_group.by_admin) as by_admin'));
        $prayers = $prayers->leftJoin('mn_group', 'mn.id', '=', 'mn_group.mn_id');
            //->where('mn_group.by_admin', 1);

        $prayers = $prayers->groupBy('mn.id')
            ->whereNull('no_active')
            ->whereNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->offset(0)
            ->take(10)
            ->get();
        */
        $prayers = intdiv(622, 100);
        return view('home', ["ar"=>$prayers]);
    }
    
    /*
     * Список молитвенных нужды, которыми поделились
     * 
     */
    public function prayersList()
    {
        //DB::enableQueryLog(); //начать запись в лог
        //dd(DB::getQueryLog());  //вывод лога запроса
        //return view('prayersList', ["arMN"=>$MN]);

        return view('prayersList');
    }
    
}
