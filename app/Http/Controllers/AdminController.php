<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = $this->getTableNames();
        
        return view('admin', ["tables"=>$tables]);
        
    }
    public function getTable(Request $request)
    {
        //запрос "DESCRIBE `имя таблицы`" выводит описание полей таблицы
        $ar = $this->getTableNames();
        $tables = array_keys($ar);
        $name = $request->input('name');
        if(!$name || !in_array($name, $tables)) return response('bad input name', 400);
        $offset = $request->input('offset')?$request->input('offset'):0;
        $table = DB::table($name)
            ->offset($offset)
            ->take(25)
            ->get();
        $count = DB::table($name)->count();
        return ["table"=>$table, "count"=>$count];
    }
    
    public function deleteRowInTable(Request $request)
    {
        $name = $request->input('name');
        if(!$name || !in_array($name, $tables)) return response('bad input name', 400);
        
        $offset = $request->input('offset')?$request->input('offset'):0;
        $table = DB::table($name)
            ->where("id", intval($request->input('offset')))
            ->delete();
        return true;
    }
    
    private function getTableNames(){
        $value = $_ENV['DB_DATABASE'];
        $ar = DB::select('SHOW TABLES FROM `laravel`', [1]);
        foreach($ar as $table){
            $name = 'Tables_in_'.$value;
            $ar2[$table->$name] = DB::table($table->$name)->count();
        }
        return $ar2;
    }
    /*
    public function prayersList()
    {
        $MN = DB::table("mn")
            ->join('mn_user__rs', 'mn.id', '=', 'mn_user__rs.mn_id')
            ->join('users', 'mn.author_id', '=', 'users.id')
            ->select('mn.*', 'users.name as author_name')
            ->where('mn_user__rs.user_id', Auth::user()->id)
            ->whereNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();
            
        return view('prayersList', ["arMN"=>$MN]);
    }
    public function prayersEnd()
    {
        $MN_model = new \App\Models\MN;
        $arMN = $MN_model::whereNotNull('end_date')
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();
        
        return view('prayersEnd', ["arMN"=>$arMN]);
    }
*/
}
