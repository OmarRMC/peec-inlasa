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

    /*
    |--------------------------------------------------------------------------
    | Subida local de documentos de inscripción
    |--------------------------------------------------------------------------
    | Cuando es false (por defecto) se muestra el enlace al formulario de
    | Google para subir documentos de inscripción. Cuando es true se habilita
    | el modal interno que sube los archivos a la plataforma.
    | Activar en .env con SUBIDA_DOCS_INSCRIPCION_LOCAL=true.
    |
    */
    'subida_docs_inscripcion_local' => env('SUBIDA_DOCS_INSCRIPCION_LOCAL', false),
];
