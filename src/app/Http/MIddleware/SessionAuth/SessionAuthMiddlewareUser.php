<?php

namespace App\Http\Controllers\SessionAuth;

use Closure;
use Illuminate\Http\Request;

class SessionAuthMiddlewareUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!SessionAuthHas()){
            return redirect()->to(SessionAuthRouteLogin());
        }

        return $next($request);

    }
}
