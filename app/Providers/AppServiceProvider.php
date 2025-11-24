<?php

namespace App\Providers;

use App\Models\Permiso;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::define(Permiso::JEFE_PEEC, function (User $user) {
            return $user->tienePermiso(Permiso::JEFE_PEEC);
        });

        Gate::define(Permiso::GAME, function (User $user) {
            return $user->id == 1158;
        });
        Gate::define(Permiso::GESTION_FORMULARIOS, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_FORMULARIOS);
        });
    }
}
