<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckProfileCompletion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

    if (Auth::check() && Auth::user()->profile_completed == 0) {
        return redirect()->route('profile.edit');
    }

        // プロフィール設定が完了していれば次の処理を実行
        return $next($request);
    }

}