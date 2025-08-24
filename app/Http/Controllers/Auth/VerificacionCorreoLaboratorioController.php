<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VerificacionCorreoLaboratorioController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        Log::info('Esto aqui');
        if (! hash_equals((string) $hash, sha1($user->email))) {
            return redirect('/')->with('error', 'El enlace de verificación no es válido.');
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('info', 'Correo ya verificado.');
        }

        $user->markEmailAsVerified();
        $user->status = true;
        $user->save();
        Log::info('Esto aqui1');
        if($user->laboratorio){
            $lab= $user->laboratorio; 
            $lab->status = true; 
            $lab->save();
        }

        
        event(new Verified($user));
        Auth::login($user);
        return redirect()->route('dashboard')->with('success', 'Correo verificado correctamente. Bienvenido al sistema.');
    }
}
