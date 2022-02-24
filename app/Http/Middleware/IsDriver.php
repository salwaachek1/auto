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
        if (auth()->user()->role_id== 2) {
             return $next($request);
        }

        return redirect('/aaa')->with('error','You have not driver access');
    }
}
