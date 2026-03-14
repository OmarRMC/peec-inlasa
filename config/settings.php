<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Allow Delete
    |--------------------------------------------------------------------------
    | Controla si se permite eliminar registros en la gestión de usuarios
    | y permisos. Cambiar a true en .env con ALLOW_DELETE=true para habilitar.
    |
    */
    'allow_delete_permiso' => env('ALLOW_DELETE_PERMISO', false),
];
