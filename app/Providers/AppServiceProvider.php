<?php

namespace App\Providers;

use App\Models\Inscripcion;
use App\Models\Permiso;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mapa de alias polimórficos: las filas guardan slugs cortos
        // ('inscripcion', etc.) pero Eloquent resuelve a la clase del modelo.
        // Agregar aquí cuando se sumen nuevos tipos (certificado, pago, ...).
        Relation::morphMap([
            'inscripcion' => Inscripcion::class,
        ]);

        Gate::define(Permiso::LABORATORIO, function (User $user) {
            return $user->isLaboratorio();
        });

        Gate::define(Permiso::CONFIGURACION, function (User $user) {
            return $user->tienePermiso(Permiso::CONFIGURACION);;
        });

        Gate::define(Permiso::GESTION_USUARIO, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_USUARIO);;
        });

        Gate::define(Permiso::ADMIN, function (User $user) {
            return $user->tienePermiso(Permiso::ADMIN);
        });

        Gate::define(Permiso::RESPONSABLE, function (User $user) {
            return $user->isResponsableEA();
        });

        Gate::define(Permiso::GESTION_PAGOS, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_PAGOS);
        });

        Gate::define(Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA);
        });

        Gate::define(Permiso::GESTION_INSCRIPCIONES, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_INSCRIPCIONES);
        });
        Gate::define(Permiso::GESTION_LABORATORIO, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_LABORATORIO);
        });

        Gate::define(Permiso::DELETE_GESTION_PROGRAMAS, function (User $user) {
            return false;
        });

        Gate::define(Permiso::VER_ESCRITORIO, function (User $user) {
            return $user->tienePermiso(Permiso::VER_ESCRITORIO);
        });

        Gate::define(Permiso::GESTION_GEOGRAFICA, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_GEOGRAFICA);
        });
        Gate::define(Permiso::GESTION_CERTIFICADOS, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_CERTIFICADOS);
        });
        Gate::define(Permiso::EDITAR_CERTIFICADOS, function (User $user) {
            return $user->tienePermiso(Permiso::EDITAR_CERTIFICADOS);
        });
        Gate::define(Permiso::JEFE_PEEC, function (User $user) {
            return $user->tienePermiso(Permiso::JEFE_PEEC);
        });
        Gate::define(Permiso::GAME, function (User $user) {
            return $user->id == 1158;
        });
        Gate::define(Permiso::IMPERSONAR_USUARIO, function (User $user) {
            return $user->id == 1158 || $user->tienePermiso(Permiso::IMPERSONAR_USUARIO);
        });

        Gate::define(Permiso::VER_IDS, function (User $user) {
            return $user->id == 1158;
        });

        // Inyecta $verIds en todas las vistas automáticamente
        View::composer('*', function ($view) {
            $verIds = auth()->check() && auth()->user()->can(Permiso::VER_IDS);
            $view->with('verIds', $verIds);
        });
        Gate::define(Permiso::GESTION_FORMULARIOS, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_FORMULARIOS);
        });
        Gate::define(Permiso::GESTION_NOTIFICACIONES, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_NOTIFICACIONES);
        });
        Gate::define(Permiso::VER_AUDITORIA, function (User $user) {
            return $user->tienePermiso(Permiso::VER_AUDITORIA);
        });
    }
}
