<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Response;
use View;

class DockLogin
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
        header('Access-Control-Allow-Origin: *');
        return $next($request);
    }
}
