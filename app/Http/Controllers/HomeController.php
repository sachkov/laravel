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
        return view('home');
    }
    
    /*
     * Список молитвенных нужды, которыми поделились
     * 
     */
    public function prayersList()
    {
        //DB::enableQueryLog();
        $MN_model = new \App\Models\MN;
        $MN = $MN_model
            ->distinct()
            ->leftJoin('mn_user__rs', 'mn.id', '=', 'mn_user__rs.mn_id')
            ->leftJoin('mn_group', 'mn.id', '=', 'mn_group.mn_id')
            //->join('users', 'mn.author_id', '=', 'users.id')
            //->select('mn.*', 'users.name as author_name')
            ->select('mn.*')
            ->where('author_id', '<>', Auth::user()->id)
            ->whereNull('end_date')
            ->where(function ($query) {
                    $groups = [];
                    $gr = DB::table('user_group')
                        ->select("group_id")
                        ->where('user_id', Auth::user()->id)
                        ->get();
                    foreach($gr as $group)
                        $groups[] = $group->group_id;
                    
                    if(count($groups))
                        $query->whereIn('mn_group.group_id', $groups)
                            ->orWhere('mn_user__rs.user_id', '=', Auth::user()->id);
                    else
                        $query->where('mn_user__rs.user_id', '=', Auth::user()->id);
                })
            //->where('mn_user__rs.user_id', '=', Auth::user()->id)
            ->orderBy('mn.updated_at', 'desc')
            ->take(30)
            ->get();
            //dd(DB::getQueryLog());
        return view('prayersList', ["arMN"=>$MN]);
    }
    
}
