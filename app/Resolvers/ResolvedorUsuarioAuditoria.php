<?php

namespace App\Resolvers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lab404\Impersonate\Services\ImpersonateManager;
use OwenIt\Auditing\Contracts\UserResolver;

/**
 * Resolvedor de usuario para la auditoría.
 *
 * Cuando un administrador está suplantando (impersonando) a otro usuario,
 * registra en la auditoría al ADMINISTRADOR REAL y no al usuario suplantado.
 * Esto refuerza el principio de No Repudio (ISO/IEC 25010).
 */
class ResolvedorUsuarioAuditoria implements UserResolver
{
    /**
     * Devuelve el usuario que debe quedar registrado en la auditoría.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve()
    {
        $gestorSuplantacion = app(ImpersonateManager::class);

        // Si hay una suplantación activa, se registra al usuario real (el que suplanta).
        if ($gestorSuplantacion->isImpersonating()) {
            $impersonador = $gestorSuplantacion->getImpersonator();

            if ($impersonador instanceof User) {
                return $impersonador;
            }
        }

        // Caso normal: el usuario autenticado en los guards configurados.
        foreach (config('audit.user.guards', [config('auth.defaults.guard')]) as $guard) {
            try {
                if (Auth::guard($guard)->check()) {
                    return Auth::guard($guard)->user();
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return null;
    }
}
