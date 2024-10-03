<?php

namespace Zenepay\PasswordExpiry\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class CheckPasswordExpired
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
        $user = $request->user();
        if ($user->isPasswordExpired()) {
            Auth::logout();
            // for Filament
            if (Route::has('filament.admin.auth.password-reset.request')) {
                return redirect()->route('filament.admin.auth.password-reset.request');
            }
            return redirect()->route('password.renew');
        }

        return $next($request);
    }
}
