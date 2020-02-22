<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        $groups = [];
        $authors = [];
        $arG = [];
        $num = 30;  //сколько записей получаем за раз

        // Получаем группы в которых состоит пользователь
        $gr = DB::table('user_group')
            ->leftJoin("groups", "user_group.group_id", "=", "groups.id")
            ->select("user_group.group_id", "groups.name")
            ->where('user_id', Auth::user()->id)
            ->get();
        foreach($gr as $group){
            if(strlen($group->name) > 15)
                $str = mb_substr($group->name, 0, 15)."..";
            else
                $str = $group->name;
            $groups[$group->group_id] = $str;
        }

// !!!!!!! Добавить where('mn.updated_at', '>', $last_date) !!!!!!!!
// !!!!!!! ->where("updated_at", ">", "2020-02-01 00:00:00") !!!!!!!
        
        $updated_at = "2040-01-01 00:00:00";    //1577822400
        if( $request->input('last_date')
            &&
            strtotime($request->input('last_date')) < 2222222222
        ){
            $updated_at = date("Y-m-d H:i:s",strtotime($request->input('last_date')));
        }

        $groups_id = DB::table('mn')
            ->leftJoin('mn_group', 'mn.id', '=', 'mn_group.mn_id')
            ->select('mn.id', 'mn_group.group_id', 'mn.author_id')
            ->whereIn('mn_group.group_id', array_keys($groups))
            ->whereNull('no_active')
            ->whereNull('end_date')
            ->where("mn.updated_at", "<", $updated_at)
            ->take($num)
            ->orderBy('mn.updated_at', 'desc')
            ->get()->toArray();
        
        $users = DB::table('mn')
            ->leftJoin('mn_user__rs', 'mn.id', '=', 'mn_user__rs.mn_id')
            ->select('mn.id', 'mn.author_id')
            ->where('mn_user__rs.user_id', Auth::user()->id)
            ->where('mn.author_id', "<>", Auth::user()->id)
            ->whereNull('no_active')
            ->whereNull('end_date')
            ->where("mn.updated_at", "<", $updated_at)
            ->take($num)
            ->orderBy('mn.updated_at', 'desc')
            ->get()->toArray();

        foreach($groups_id as $mn){
            $arG[$mn->id][] = $mn->group_id;
            $authors[] = $mn->author_id;
        }

        foreach($users as $umn){
            if(!array_key_exists($umn->id, $arG)){
                $arG[$umn->id] = [];
                $authors[] = $umn->author_id;
            }
        }
        $arAuthors = DB::table("users")
            ->select("id", "name")
            ->whereIn("id", $authors)
            ->get()->keyBy("id");


        $objMN = DB::table('mn')
            ->select("id", "name", "description", "author_id", "answer", 
                    "answer_date", "updated_at")
            ->whereIn('id', array_keys($arG))
            ->take($num)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        $objMN2 = $objMN->transform(function ($item, $key) {
            $item->diff = $this->humanDate($item->updated_at);
            return $item;
        });

        return json_encode( [
            "groups"=>$groups, 
            "authors"=>$arAuthors,
            "mn_groups"=>$arG,
            "MN"=>$objMN2,
        ] );
    }

    private function humanDate($uDate){
        $months = [
            1=>"января",
            2=>"февраля",
            3=>"марта",
            4=>"апреля",
            5=>"мая",
            6=>"июня",
            7=>"июля",
            8=>"августа",
            9=>"сентября",
            10=>"октября",
            11=>"ноября",
            12=>"декабря",
        ];
        $text = "";
        $now = Carbon::now();
        $diff = $now->diffInDays($uDate);
        $D = Carbon::parse($uDate);
        if($diff < 1){
            $text .= "сегодня";
        }elseif($diff < 2){
            $text .= "вчера";
        }elseif($diff < 5){
            $text .= $diff." дня назад";
        }elseif($diff < 10){
            $text .= $diff." дней назад";
        }elseif($D->year != $now->year){
            $text .= $D->day." ".$months[$D->month]." ".$D->year ;
        }else{
            $text .= $D->day." ".$months[$D->month];
        }
        
        return $text;
    }
}
