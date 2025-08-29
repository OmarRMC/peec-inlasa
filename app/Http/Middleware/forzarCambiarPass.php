<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpFoundation\Response;

class forzarCambiarPass
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = $request->username;
        $user = User::where('username', $username)->first();
        if ($user && !$user->password) {
            $token = Password::createToken($user);
            return redirect()->route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]);
        }
        return $next($request);
    }
}
