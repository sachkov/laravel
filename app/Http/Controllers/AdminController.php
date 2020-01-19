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
        /*$code = 1;
        $WelcomeCodes = new \App\Models\WelcomeCodes;
        $code = $WelcomeCodes::select('id')
                ->where([
                    ["code", "=", '222'], 
                    ["created_at", ">", date("Y-m-d", (time() - 5*24*60*60))]
                ])
                ->whereNull('user_id')
                ->count();

        /*$WelcomeCodes->code = 222;
        $WelcomeCodes->author_id = Auth::user()->id;
        $WelcomeCodes->save();*/
        $BadEmailsReg = new \App\Models\BadEmailsReg;
        $code = $bad_email = $BadEmailsReg::where("email", 'aa@aa.ru')->get();
        
        return view('admin', ["tables"=>$tables, "code"=>$code]);
        //return view('admin', ["tables"=>$tables]);
        
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
        $ar = $this->getTableNames();
        $tables = array_keys($ar);
        if(!$name || !in_array($name, $tables)) return response('bad input name', 400);
        
        $offset = $request->input('offset')?$request->input('offset'):0;
        $table = DB::table($name)
            ->where("id", intval($request->input('id')))
            ->delete();
        return 'delete is success';
    }
    
    private function getTableNames(){
        $value = $_ENV['DB_DATABASE'];
        $ar = DB::select('SHOW TABLES FROM `'.$value.'`', [1]);
        foreach($ar as $table){
            $name = 'Tables_in_'.$value;
            $ar2[$table->$name] = DB::table($table->$name)->count();
        }
        return $ar2;
    }

}