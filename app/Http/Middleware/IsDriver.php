<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsDriver
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user() &&  Auth::user()->role_id == 2 || Auth::user()->role_id ==1) {
             return $next($request);
        }


        return redirect('/welcome')->with('message','Vous n\'avez pas accès !');
    }
}
