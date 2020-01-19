<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\User;

class PersonalController extends Controller
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
        $User = new User;

        $user = $User::find(Auth::user()->id);
        
        return view('personal', ["user"=>$user]);
        
    }
    
    /*
     * Список молитвенных нужды, которыми поделились
     * 
     */
    public function generateCode()
    {
        $WelcomeCodes = new \App\Models\WelcomeCodes;
        $code_is_valid = false; //Нет сгенерированного кода
        $invites = $WelcomeCodes::where("author_id", Auth::user()->id)
            ->orderBy("id", "desc")
            ->get();
        foreach($invites as $i){
            if($i->user_id){
                $i->status = "Зарегистрирован ";
                if($i->user) $i->status .= $i->user->name;
                else $i->status .= "пользователь.";
            }elseif($i->created_at < date("Y-m-d", (time() - 5*24*60*60))){
                $i->status = "Никто не воспользовался приглашением";
            }else{
                $i->status = "Код ".$i->code.". Ожидаем регистрации!";
                $code_is_valid = true;
            }
        }
            
        return view('generateCode', ["invites"=>$invites, "valid_code"=>$code_is_valid]);
    }

    /*
    *   Генерация случайного кода
    */
    public function generate()
    {
        $WelcomeCodes = new \App\Models\WelcomeCodes;
        do{
            $code = rand(0, 99999);
            $codes = $WelcomeCodes::where([
                ["code", $code],
                ["created_at", "<", date("Y-m-d", (time() - 5*24*60*60))]
            ])
                ->whereNull('user_id')
                ->count();
        }while($codes > 0);
        $WelcomeCodes->code = $code;
        $WelcomeCodes->author_id = Auth::user()->id;
        $WelcomeCodes->save();
        
        return $code;
    }
    
    /*
     * Список моих завершенных молитв
     */
    public function prayersEnd()
    {
        $MN_model = new \App\Models\MN;
        $arMN = $MN_model
            ->whereNotNull('end_date')
            ->where('author_id', Auth::user()->id)
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();
        
        return view('prayersEnd', ["arMN"=>$arMN]);
    }
}