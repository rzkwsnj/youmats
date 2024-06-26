<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if(Auth::guard('vendor')->check())
            return redirect(RouteServiceProvider::VENDOR_HOME);
        elseif(Auth::guard('web')->check())
            return redirect(RouteServiceProvider::HOME);

        return $next($request);
    }
}
