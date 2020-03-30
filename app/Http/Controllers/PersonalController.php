<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\User;
use App\Models\Group;
use App\Models\Push;

class PersonalController extends Controller
{
    /**
     * Главная страница раздела "Личный кабинет".
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

        return view('personal.personal', [
                "user"=>$user, 
                "groups"=>[]
            ]);
        
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
            $code = rand(1, 32700);
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
            ->whereNull('no_active')
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
            $Group->signed_users()->attach(Auth::user()->id, ['admin' => 1]);
            
            $out['success'] = $Group->id;
        } 
        else {
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );

    }

    /*
    *   Присоединить пользователя к существующей группе
    */
    public function addUser(Request $request)
    {
        if(Auth::check())
        {
            $this->validate($request, [
                'group' => 'required|numeric|max:1000',
              ]);

            $count = DB::table('user_group')
                ->where(
                    [
                        'group_id' => $request->input('group'),
                        'user_id' => Auth::user()->id
                    ])
                ->count();
            //Добавляем если пользователя нет в группе
            $id = 0;
            if(!$count){
                $id = DB::table('user_group')->insertGetId(
                    [   
                        'user_id' => Auth::user()->id, 
                        'group_id' => $request->input('group')
                    ]
                );
            }
            if($id) $out['success'] = $id;
            else $out['error'] = 'ошибка вставки в таблицу';
        } 
        else {
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }
    
    /*
        Выход пользователя из группы
    */
    public function leaveGroup(Request $request)
    {
        if(Auth::check()){ 
            $this->validate($request, [
                'group' => 'required|numeric|max:1000',
                'user_id' => 'numeric|max:100000'
              ]);
            $user_id = $request->input('user_id')
                        ?$request->input('user_id')
                        :Auth::user()->id;
            $count = DB::table('user_group')
                ->where('group_id', $request->input('group'))
                ->count();
            DB::table('user_group')
                ->where(
                [   
                    ['user_id', '=', $user_id], 
                    ['group_id', '=', $request->input('group')]
                ])
                ->delete();
            $out['success'] = 'Пользователь вышел из группы';
            //Удаляем группу если из нее вышел последний чел
            //(т.к. тогда она не ищется через getAllGroups)
            if($count <= 1){
                DB::table('groups')
                    ->where('id', $request->input('group'))
                    ->delete();
                $out['success'] = 'Последний пользователь вышел из группы. 
                    И группа удалена.';
            }
            
        }else{
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }

    /*
        Удаление группы
    */
    /*
    public function delGroup(Request $request)
    {
        if(Auth::check()){ 
            $this->validate($request, [
                'group' => 'required|numeric|max:1000',
              ]);
            $group = DB::table('groups')
              ->where([
                  ['author_id', '=',Auth::user()->id],
                  ['id', '=', $request->input('group')]
              ])
              ->first();
            if($group->id == $request->input('group')){
                DB::table('groups')
                    ->where('id', $request->input('group'))
                    ->delete();
                $out['success'] = 'Удаление завершено';
            }else{
                $out['error'] = 'Удалить можно только собственную группу.';
            }
        }else{
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }*/

    /*
        Изменение наименования группы
    */
    public function changeGroupName(Request $request)
    {
        if(Auth::check()){ 
            $this->validate($request, [
                'id' => 'required|numeric|max:1000',
                'name' => ['required', 'regex:/^[\w-\d\sА-Яа-я"()\. ]{0,100}/u']
              ]);
            $group = DB::table('user_group')
                ->where([
                  ['user_id', '=', Auth::user()->id],
                  ['group_id', '=', $request->input('id')],
                  ["admin", '=', 1]
              ])
              ->first();
            if($group->group_id == $request->input('id')){
               
                $group_model = Group::find($group->group_id);
                $group_model->name = $request->input('name');
                $group_model->save();
                $out['success'] = 'Группа переименована';
            }else{
                $out['error'] = 'Только администраторы могут переименовывать группу.';
            }
        }else{
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }

    /*
        Получение по ajax групп на которые не подписан пользователь
    */
    public function getGroups()
    {
        //Получаем id групп в которых состоит пользователь
        if(Auth::check()){ 
            $out = ["groups"=>$this->getAllGroups()];
        }else{
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }

    /*
    *   Получение групп в которых состоит или не состоит пользователь
    */
    private function getAllGroups()
    {
        $arGroups = [];
        $arGroupid = [];
        //все группы
        $groups = DB::table('user_group')
            ->select(DB::raw("
                count(group_id) as users_count,
                groups.id,
                groups.name, 
                groups.author_id
            "))
            ->join("groups", "user_group.group_id", "=", "groups.id")
            ->groupBy('group_id')->get();
        //id групп куда входит пользователь
        $user_groups = DB::table("user_group")
            ->where("user_id", Auth::user()->id)
            ->get();
        foreach($user_groups as $user)
            $arGroupid[$user->group_id] = $user->admin;
        foreach($groups as $group){
            $arGroups[] = [
                "name"=>$group->name, 
                "number"=>$group->users_count, 
                "id"=>$group->id,
                "is_author"=>($group->author_id == Auth::user()->id)?1:0,
                "is_member"=>intval(in_array(
                    $group->id, 
                    array_keys($arGroupid)
                )),
                "link"=>route('showGroup',["id"=>$group->id]),
                "admin"=>intval($arGroupid[$user->group_id]),
            ];
        }

        return $arGroups;
    }


    /**
     * Страница о сайте
     */
    public function about()
    {
        return view('personal.about');
    }

    /*
    *   Страница группы
    */
    public function showGroup($id)
    {
        $group = Group::findOrFail($id);
        $is_admin = false;

        foreach($group->signed_users as $user){
            if($user->id == Auth::user()->id && $user->pivot->admin){
                $is_admin = true;
                break;
            }
        }
        return view('personal.group', ['group' => $group, "is_admin"=>$is_admin]);
        
    }

    /*
    *   Добавить пользователю статус админа
    */
    public function addAdmin(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required|numeric|max:10000',
            'user_id' => 'required|numeric|max:100000'
        ]);
        DB::table('user_group')
            ->where('group_id', $request->input('group_id'))
            ->where('user_id', $request->input('user_id'))
            ->update(['admin' => 1]);
            
        return "Обновление успешно.";
    }

    /*
    *   Добавить пользователю статус админа
    */
    public function delAdmin(Request $request)
    {
        $this->validate($request, [
            'group_id' => 'required|numeric|max:10000',
            'user_id' => 'required|numeric|max:100000'
        ]);
        DB::table('user_group')
            ->where('group_id', $request->input('group_id'))
            ->where('user_id', $request->input('user_id'))
            ->update(['admin' => null]);
            
        return "Обновление успешно.";
    }

    /**
     * Список всех пользователей на сайте
     */
    public function usersList($count = 100)
    {
        $User = new User;
        $users = User::select('id', 'name', 'created_at')
            ->orderBy('id', 'desc')
            ->take($count)
            ->get();

        $link = null;
        if(count($users) == $count)
            $link = route('usersList',["count"=>$count+100]);
        
        return view("personal.usersList", ["users"=>$users, "nextLink"=>$link]);
    }

    /**
     * Страница настроек
     */
    public function settings()
    {
        return view('personal.settings');
    }

    /**
     * Сохранение токена пользователя
     */
    public function saveToken(Request $request)
    {
        if(Auth::check()){ 
            //!! Найти пользователя и устройство, обновить
            //Если не найдено - создать новую запись !!!
            $Push = new Push;
            $exist = \App\Models\Push::where([
                ["user_id", Auth::user()->id],
                ["site", $request->input('site')]
            ])->first();
            if($exist){
                $exist->token = $request->input('token');
                $exist->site = $request->input('site');
                $exist->save();
                $out = ["status"=>"updated"];
            }else{
                $Push->token = $request->input('token');
                $Push->user_id = Auth::user()->id;
                $Push->site = $request->input('site');
                $Push->save();
                $out = ["status"=>"created"];
            }
            
        }else{
            $out['error'] = 'У вас нет доступа';
        }

        return response()->json( $out );
    }
}
