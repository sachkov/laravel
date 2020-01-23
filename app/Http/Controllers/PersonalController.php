<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\User;
use App\Models\Group;

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
        //$Group = new Group;

        $user = $User::find(Auth::user()->id);
        

        $groups = DB::table('user_group')
            ->join("groups", "user_group.group_id", "=", "groups.id")
            ->where('user_id', Auth::user()->id)
            ->get()->toArray();
            //понять что возвращяет этот запрос
        foreach($groups as $group){
            $count = DB::table('user_group')
                ->where('group_id', $group->id)
                ->count();
            $arGroups[] = [
                "name"=>$group->name, 
                "number"=>$count, 
                "id"=>$group->id,
                "is_author"=>($group->author_id == Auth::user()->id)?"1":"0"
            ];
        }

        return view('personal.personal', ["user"=>$user, "groups"=>$arGroups]);
        
    }
    
    /*
     * Список сгенерированных пользователем кодов и последствия
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
            
        return view('personal.generateCode', ["invites"=>$invites, "valid_code"=>$code_is_valid]);
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

    /*
    *   Создание группы и добавление ИД создателя в author_id
    */
    public function createGroup(Request $request)
    {
        if(Auth::check())
        {
            $Group = new Group;
            $Group->name = $request->input('name');
            $Group->author_id = Auth::user()->id;
            $Group->save();
            $Group->signed_users()->attach(Auth::user()->id);
            
            $out['success'] = $Group->id;;
        } 
        else {
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }

        return response()->json( $out );

    }

    /*
    *   Получение групп на которые не подписан пользователь
    */
    public function getNotMyGroups(Request $request)
    {
        if(Auth::check())
        {
            //$Group = new Group;
            $groups = [];
            $groups = DB::table('user_group')
                ->join("groups", "user_group.group_id", "=", "groups.id")
                ->where('user_id', "<>", Auth::user()->id)
                ->get()->toArray();
            
            $out['success'] = $groups;
        } 
        else {
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }

        return response()->json( $out );

    }
}
