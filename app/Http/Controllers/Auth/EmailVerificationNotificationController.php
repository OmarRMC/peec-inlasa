<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use App\Notifications\VerificarCorreoLab;
use App\Notifications\VerificarCorreoPersonalizado;
use App\Notifications\VerificarCorreoUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        $user = $request->user();

        if ($user->isLaboratorio()) {
            $user->notify(new VerificarCorreoLab($user, $user->laboratorio));
        } else {
            $user->notify(new VerificarCorreoUser($user));
        }
        // $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }
}
