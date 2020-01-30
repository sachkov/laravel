<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/personal/about';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $validator =  Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        //Добавляем доп проверку кода и присутствует ли емеил в черном списке
        $validator->sometimes("code", "confirmed", function($data){
            if(!$data->code) return true;
            $WelcomeCodes = new \App\Models\WelcomeCodes;
            $BadEmailsReg = new \App\Models\BadEmailsReg;
            $code_is_fine = $WelcomeCodes::select('id')
                    ->where([
                        ["code", "=", $data->code], 
                        ["created_at", ">", date("Y-m-d", (time() - 5*24*60*60))]
                    ])
                    ->whereNull('user_id')
                    ->count();
            $bad_email = $BadEmailsReg::where("email", $data->email)->first();
            if(!$code_is_fine){
                if(!$bad_email){
                    //сохраняем email
                    $BadEmailsReg->email = $data->email;
                    $BadEmailsReg->tries = 1;
                    $BadEmailsReg->save();
                }else{
                    $bad_email->tries++;
                    $bad_email->save();
                }
            }else{
                if($bad_email && $bad_email->tries > 3)
                    $code_is_fine = false;
            }

            return $code_is_fine?false:true;
        });
        
        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * The user has been registered.
     * Выдернул данный метод из namespace Illuminate\Foundation\Auth\RegistersUsers;
     * надеюсь что он переоприделится)
     * Добавляем запись в список приглашений
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if($user->id){
            $WelcomeCodes = new \App\Models\WelcomeCodes;
            $WelcomeCodes::where([
                    ["code", "=", $request->input("code")], 
                    ["created_at", ">", date("Y-m-d", (time() - 5*24*60*60))]
                ])
                ->whereNull('user_id')
                ->update(["user_id"=> $user->id]);
        }
    }
}
