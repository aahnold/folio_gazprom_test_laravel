<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiTokenMiddleware
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
        if(auth()->user()){
            if(auth()->user()->tokenExpired() && !$request->routeIs('api.login'))
                return \Response::json(['status' => 'error', 'message' => 'Token expired'], 401);
            else
                auth()->user()->touchToken();
        }
        return $next($request);
    }
}
