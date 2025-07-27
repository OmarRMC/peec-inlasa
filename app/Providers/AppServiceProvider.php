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

        Gate::define(Permiso::ADMIN, function (User $user) {
            return $user->tienePermiso(Permiso::ADMIN);
        });

        Gate::define(Permiso::RESPONSABLE, function (User $user) {
            return $user->isResponsableEA();
        });

        Gate::define(Permiso::GESTION_PAGOS, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_PAGOS);;
        });

        Gate::define(Permiso::GESTION_INSCRIPCIONES, function (User $user) {
            return $user->tienePermiso(Permiso::GESTION_INSCRIPCIONES);
        });


        Gate::define(Permiso::DELETE_GESTION_PROGRAMAS, function (User $user) {
            return false;
        });
    }
}
