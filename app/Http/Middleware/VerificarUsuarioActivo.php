<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificarUsuarioActivo
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            if (!$usuario->status) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'error' => 'Por momento tu cuenta está inactiva.',
                ]);
            }
        }
        return $next($request);
    }
}
