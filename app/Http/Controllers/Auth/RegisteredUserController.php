<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $cargos = [];
        return view('auth.register', compact('cargos'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username'     => ['required', 'string', 'max:50', 'unique:users,username'],
            'nombre'       => ['required', 'string', 'max:50'],
            'ap_paterno'   => ['required', 'string', 'max:50'],
            'ap_materno'   => ['nullable', 'string', 'max:50'],
            'ci'           => ['required', 'string', 'max:15', 'unique:users,ci'],
            'telefono'     => ['nullable', 'string', 'max:20'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', 'confirmed', Rules\Password::defaults()],
            'id_cargo'     => ['nullable', 'exists:cargo,id'],
        ]);

        $user = User::create([
            'username'    => $request->username,
            'nombre'      => $request->nombre,
            'ap_paterno'  => $request->ap_paterno,
            'ap_materno'  => $request->ap_materno,
            'ci'          => $request->ci,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'id_cargo'    => $request->id_cargo,
            'status'      => true, // o puedes inicializar con false si requiere activación
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard'));
    }
}
