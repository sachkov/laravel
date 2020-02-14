<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Auth;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function saveMN(Request $request)
    {
        
        if(Auth::check())
        {
            $MN_model = new \App\Models\MN;
            $MN_model->name = $request->input('name');
            $MN_model->description = $request->input('text');
            $MN_model->author_id = Auth::user()->id;
            $MN_model->save();
            $MN_model->signed_users()->sync(json_decode($request->input('users')));
            $MN_model->signed_groups()->sync(json_decode($request->input('groups')));
            
            $out['success'] = $request->input('name');
        } 
        else {
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }
        //return Response::json($out, 200);
        //dd($MN_model);
        return response()->json( $out );
    }
    
    public function getTable(Request $request)
    {
        if(Auth::check())
        {
            $r = [];
            $MN_model = new \App\Models\MN;
            $offset = 0;
            if($request->input('offset'))
                $offset = $request->input('offset');
            $prayers = $MN_model::where('author_id', Auth::user()->id)
                ->whereNull('no_active')
                ->whereNull('end_date')
                ->orderBy('updated_at', 'desc')
                ->offset($offset)
                ->take(10)
                ->get();
            $table = [];
            foreach($prayers as $k=>$pr){
                $r = [];
                $g = [];
                $table[$k] = [
                    "id"=>$pr->id,
                    "name"=>$pr->name,
                    "created_at"=>$pr->created_at->format('d.m.Y'),
                    "description"=>$pr->description,
                    "answer"=>$pr->answer,
                    "is_thanks"=>$pr->answer_date?1:0,
                ];
                foreach($pr->signed_users as $user){
                    $r[] = ["name"=>$user->name, "id"=>$user->id];
                }
                foreach($pr->signed_groups as $group){
                    $g[] = ["name"=>$group->name, "id"=>$group->id];
                }
                $table[$k]["author"] = ["name"=>$pr->author->name, "email"=>$pr->author->email];
                $table[$k]["users"] = $r;
                $table[$k]["groups"] = $g;
            }
            $count = $MN_model::where('author_id', Auth::user()->id)
                ->whereNull('end_date')
                ->count();
            
            $res = ["table"=>$table, "count"=>$count];
        } 
        else {
            $res['error'] = 'У вас нет доступа';
        }

        return json_encode($res);
    }
    
    public function getUsers(Request $request)
    {
        if(Auth::check())
        {
            $User_model = new \App\User;
            $objUsers = $User_model::all();
            
            foreach($objUsers as $user){
                $res[$user->id] = $user->name;
            }
        } 
        else {
            $res['error'] = 'У вас нет доступа или комментарий пустой';
        }

        return json_encode($res);
    }
    
    public function editMN(Request $request)
    {
        if(Auth::check() && $request->input('id')){
            $MN_model = \App\Models\MN::find($request->input('id'));
            $MN_model->name = $request->input('name');
            $MN_model->description = $request->input('text');
            $MN_model->answer = $request->input('result');
            $MN_model->save();
            $MN_model->signed_users()->sync(json_decode($request->input('users')));
            $MN_model->signed_groups()->sync(json_decode($request->input('groups')));
            
            $out['success'] = $request->input('id');
        }else{
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }
        return response()->json( $out );
    }
    
    /*
     * Завершение МН имеет 2 сценария:
     * 1) Если стоит галочка "опубликовать как благодарность" - то проставляем время ответа и
     * записываем результат (если не пришел результат - возврат ошибки)
     * 2) Если нет галочки опубликовать то проставляем дату завершения и если есть результат то
     * записываем его и проставляем дату ответа.
     */
    public function doneMN(Request $request)
    {
        if(Auth::check()){
            $MN_model = \App\Models\MN::find($request->input('id'));
            if($request->input('re_publish') && $request->input('result')==""){
                $out['error'] = 'Невозможно опубликовать благодарность - не заполнен результат.';
                return response()->json( $out );
            }
            if($request->input('result')!="")
                $MN_model->answer = $request->input('result');
            if($request->input('re_publish')){
                $MN_model->answer_date = date("Y-m-d H:i:s");
            }else{
                $MN_model->end_date = date("Y-m-d H:i:s");
                if($request->input('result') != "")
                    $MN_model->answer_date = date("Y-m-d H:i:s");
            }
            $MN_model->save();
            
            $out['success'] = $request->input('id');
        }else{
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }
        return response()->json( $out );
    }

    /*
    *   "Удаление нужды" - ставим 1 в столбец 'no_active'
    */
    public function deleteMN(Request $request)
    {
        if(Auth::check()){
            if(preg_match('#^\d{1,6}$#', intval($request->input('id')))){
                $MN_model = \App\Models\MN::find($request->input('id'));
                $MN_model->no_active = 1;
                $MN_model->save();
                $out['result'] = $request->input('id');
            }else{
                $out['input error'] = 'Не верный входной параметр id';
            }
        }else{
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }
        return response()->json( $out );
    }
    
    /*
    *   Получение молитвенных нужд для таблицы Общие молитвы
        @param
        - дата последней нужды (для пагинации)
        - id группы или признак вывода личных нужд
    */
    public function getPrayersList(Request $request)
    {
        // 1. Получаем ИД нужд добавленных пользователями
        // 2. Получаем ИД нужд добавленных в группы
        // 3. Удаляем дубли (из пользователей)
        // 4. Получаем МН из массива полученных в п.1 и 2 ИД.
        // П.1 и п.2 получаем с сортировкой по дате обновления, и где 
        // дата обновления > входная дата.
        // Если в обоих массивах по 30 элементов то сравниваем последние даты
        // Финальная дата - наибольшая из 2х последних
        // Если оба массива < 30 то финальная дата - наименьшая из всех
        // Если только 1 массив = 30 элементам то финальная дата - последняя в этом массиве
        // В результирующий массив должны попасть элементы с датами > финальной.

        // Формируем результирующий массив: если в массиве групп есть ИД пользователей,
        // ИД пользователя удаляем;
        // Если в массиве групп есть элементы с одинак ИД то объединяем group_id

        // Сортируем массив по 'mn.updated_at'

        // Получаем данные МН для каждого ИД в результирующем массиве

        // Получаем Имена авторов
        // Получаем Названия групп
        
        $groups = [];
        $num = 30;  //сколько записей получаем за раз

        $gr = DB::table('user_group')
            ->select("group_id")
            ->where('user_id', Auth::user()->id)
            ->get();
        foreach($gr as $group)
            $groups[] = $group->group_id;

// !!!!!!! Добавить where('mn.updated_at', '>', $last_date) !!!!!!!!

        $groups_id = DB::table('mn')
            ->leftJoin('mn_group', 'mn.id', '=', 'mn_group.mn_id')
            //->select('mn.id','mn.updated_at', 'mn_group.group_id')
            ->select('mn.id', 'mn_group.group_id')
            ->whereIn('mn_group.group_id', $groups)
            ->take($num)
            ->orderBy('mn.updated_at', 'desc')
            ->get()->toArray();
        
        $users = DB::table('mn')
            ->leftJoin('mn_user__rs', 'mn.id', '=', 'mn_user__rs.mn_id')
            //->select('mn.id','mn.updated_at')
            ->select('mn.id')
            ->where('mn_user__rs.user_id', Auth::user()->id)
            ->where('mn.author_id', "<>", Auth::user()->id)
            ->take($num)
            ->orderBy('mn.updated_at', 'desc')
            ->get()->toArray();
        /*
        $group_date = $user_date = '';
        $groups_count = count($groups_id);
        $users_count = count($users);

        if($groups_count || $users_count){
            if($groups_count < $num && $users_count < $num){
                if(!$groups_count)
                    $last_date = strtotime($users[$users_count - 1]->updated_at);
                elseif(!$users_count)
                    $last_date = strtotime($groups_id[$groups_count - 1]->updated_at);
                elseif( 
                    strtotime($users[$users_count - 1]->updated_at) > 
                    strtotime($groups_id[$groups_count - 1]->updated_at)
                ) $last_date = strtotime($users[$users_count - 1]->updated_at);
                else $last_date = strtotime($groups_id[$groups_count - 1]->updated_at);

            }elseif($groups_count == $num && $users_count == $num){
                if( 
                    strtotime($users[$users_count - 1]->updated_at) > 
                    strtotime($groups_id[$groups_count - 1]->updated_at)
                ) $last_date = strtotime($users[$users_count - 1]->updated_at);
                else $last_date = strtotime($groups_id[$groups_count - 1]->updated_at);
            }else{
                if($groups_count == $num)
                    $last_date = strtotime($groups_id[$groups_count - 1]->updated_at);
                elseif($users_count == $num)
                $last_date = strtotime($users[$users_count - 1]->updated_at);
            }
        }else{
            $error = "не найдены значения";
        }*/

        foreach($groups_id as $mn){
            //$arG[$mn->id]["date"] = $mn->updated_at;
            $arG[$mn->id][] = $mn->group_id;
        }

        foreach($users as $umn){
            if(!array_key_exists($umn->id, $arG))
                $arG[$umn->id] = [];
                //$arG[$umn->id]["date"] = $umn->updated_at;
        }

        $MN = DB::table('mn')
            ->whereIn('id', array_keys($arG))
            ->orderBy('updated_at', 'desc')
            ->get();
        


            return response()->json( [
                "groups"=>$groups_id, 
                "users"=>$users,
                "last_date"=>$last_date,
                "ar"=>$arG,
                "MN"=>$MN
            ]);
    }

}
