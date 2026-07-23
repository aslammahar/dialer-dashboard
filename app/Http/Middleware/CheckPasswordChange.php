<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChange
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
        if (Auth::check() && Auth::user()->password_changed_at === null && !$request->session()->has('force_password_change')) {
            $request->session()->put('force_password_change', true);
            return redirect()->route('password.change.notification');
        }

        return $next($request);
    }
} 