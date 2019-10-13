<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function saveMN(Request $request)
    {
        /*
        if(true)
        {
            $MN_model = new \App\Models\MN();
            $MN_model->comment = $request->input('comment');
            $MN_model->item_id = $id;
            $MN_model->user_id = Auth::user()->id;
            $MN_model->save();
            $out['success'] = $request->all();
        } 
        else {
            $out['error'] = 'У вас нет доступа или комментарий пустой';
        }
        return Response::json($out, 200);*/
        //dd($request->all());
        //return Response::json(["f"=>'У вас нет доступа или комментарий пустой'], 200);
        return response()->json( $request->all() );
    }
}
