<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{


    public function cargoWrite(User $user): bool
    {
        return true;
    }
    /**
     * Ver un usuario específico.
     */
    public function view(User $user, User $model): bool
    {
        return $user->tienePermiso('Gestión de Usuarios');
    }


    public function index(User $user, User $model): bool
    {
        return false;
    }


    /**
     * Crear un nuevo usuario.
     */
    public function create(User $user): bool
    {
        return $user->tienePermiso('Gestión de Usuarios');
    }

    /**
     * Actualizar datos del usuario.
     */
    public function update(User $user, User $model): bool
    {
        return $user->tienePermiso('Administrar datos');
    }

    /**
     * Eliminar un usuario.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->tienePermiso('Administrar datos');
    }

    /**
     * Restaurar un usuario (si usas soft deletes).
     */
    public function restore(User $user, User $model): bool
    {
        return $user->tienePermiso('Administrar datos');
    }

    /**
     * Forzar eliminación definitiva.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->tienePermiso('Administrar datos');
    }
}
