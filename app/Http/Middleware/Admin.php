<?php

namespace App\Http\Middleware;

use Closure;

class Admin
{
    /**
     * Обработка входящего запроса
     * 
     * Если id пользователя не 1 то редирект на home
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user()->id != 1) {
            return redirect('/');
        }
        return $next($request);
    }
}
