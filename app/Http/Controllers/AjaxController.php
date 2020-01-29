<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
    
}
