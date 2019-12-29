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
            $prayers = $MN_model::where('author_id', Auth::user()->id)
               ->orderBy('name', 'desc')
               ->take(20)
               ->get();
            foreach($prayers as $k=>$pr){
                $r = [];
                $res[$k] = [
                    "id"=>$pr->id,
                    "name"=>$pr->name,
                    "created_at"=>$pr->created_at->format('d.m.Y'),
                    "description"=>$pr->description,
                    "answer"=>$pr->answer,
                    "is_thanks"=>$pr->answer_date?1:0,
                    //"IDs"=>$pr->signed_user_IDs
                ];
                foreach($pr->signed_users as $user){
                    $r[] = ["name"=>$user->name, "id"=>$user->id];
                }
                $res[$k]["author"] = ["name"=>$pr->author->name, "email"=>$pr->author->email];
                $res[$k]["users"] = $r;
            }
        } 
        else {
            $res['error'] = 'У вас нет доступа или комментарий пустой';
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
        if(Auth::check()){
            $MN_model = \App\Models\MN::find($request->input('id'));
            $MN_model->name = $request->input('name');
            $MN_model->description = $request->input('text');
            $MN_model->save();
            $MN_model->signed_users()->sync(json_decode($request->input('users')));
            
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
}
