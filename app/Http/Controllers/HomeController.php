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
        //Получаем ИД МН в которых пользователь - админ
        /*
        $UG = DB::table("user_group")
            ->select("group_id")
            ->where("user_id", Auth::user()->id)
            ->where("admin", 1)
            ->get();
        $arGroupsId = [];
        foreach($UG as $user) $arGroupsId[] = $user->group_id;

        $r = [];
        $num = 10;          //Кол-во записей в выборке
        $offset = 0;
        $MN_model = new \App\Models\MN;

        $sortBy = "personal";
        
        //DB::enableQueryLog(); //начать запись в лог
        $prayers = $MN_model::selectRaw('mn.*, count(mn_group.by_admin) as by_admin')
            ->leftJoin('mn_group', function($join){
                $join->on('mn.id', '=', 'mn_group.mn_id')
                    ->where('mn_group.by_admin', 1);
            })
            ->when(($sortBy=="all"), function ($query) use ($arGroupsId){
                return $query->where(function($q) use ($arGroupsId){
                    $q->whereIn('mn_group.group_id', $arGroupsId)
                        ->orWhere('mn.author_id', Auth::user()->id);
                });
            }, function($query) use ($arGroupsId, $sortBy){
                if($sortBy == "personal")
                    return $query->where('mn.author_id', Auth::user()->id)
                                ->whereNull('mn_group.by_admin');
                else
                    return $query->whereIn('mn_group.group_id', $arGroupsId);
            })
            ->whereNull('mn.no_active')
            ->whereNull('mn.end_date')
            ->groupBy('mn.id')
            ->orderBy('mn.updated_at', 'desc')
            ->offset($offset)
            ->take(30)
            ->get();
            */
        //dd(DB::getQueryLog());  //вывод лога запроса
        return view('home');
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
