<?php

return [

    'git_checkout_main' => [
        'cmd' => ['git', 'checkout', 'main'],
        'type' => 'git',
        'description' => 'Cambia a la rama main',
    ],

    'git_pull' => [
        'cmd' => ['git', 'pull', 'origin', 'main'],
        'type' => 'git',
        'description' => 'Actualiza el repositorio',
    ],

    'git_add_all' => [
        'cmd' => ['git', 'add', '.'],
        'type' => 'git',
        'description' => 'Agrega cambios al stage',
    ],

    'git_stash' => [
        'cmd' => ['git', 'stash'],
        'type' => 'git',
        'description' => 'Guarda cambios locales',
    ],
    'git_stash_apply_0' => [
        'cmd' => ['git', 'stash', 'apply', '0'],
        'type' => 'git',
        'description' => 'Aplica el stash en la posición 0',
    ],
    'git_status' => [
        'cmd' => ['git', 'status'],
        'type' => 'git',
        'description' => 'Muestra el estado del repositorio',
    ],

    'git_reset_hard_origin_main' => [
        'cmd' => ['git', 'reset', '--hard', 'origin/main'],
        'type' => 'git',
        'description' => 'Descarta todos los cambios locales y sincroniza con origin/main',
    ],

    'composer_install' => [
        'cmd' => ['composer', 'install'],
        'type' => 'composer',
        'description' => 'Instala dependencias PHP',
    ],

    'composer_dump_autoload' => [
        'cmd' => ['composer', 'dump-autoload'],
        'type' => 'composer',
        'description' => 'Regenera autoload',
    ],


    'npm_install' => [
        'cmd' => ['npm', 'install'],
        'type' => 'npm',
        'description' => 'Instala dependencias JS',
    ],

    'npm_build' => [
        'cmd' => ['npm', 'run', 'build'],
        'type' => 'npm',
        'description' => 'Compila assets',
    ],

    'cache_clear' => [
        'cmd' => ['php', 'artisan', 'cache:clear'],
        'type' => 'artisan',
        'description' => 'Limpia cache',
    ],

    'config_clear' => [
        'cmd' => ['php', 'artisan', 'config:clear'],
        'type' => 'artisan',
        'description' => 'Limpia config cache',
    ],

    'view_clear' => [
        'cmd' => ['php', 'artisan', 'view:clear'],
        'type' => 'artisan',
        'description' => 'Limpia vistas',
    ],

    'optimize_clear' => [
        'cmd' => ['php', 'artisan', 'optimize:clear'],
        'type' => 'artisan',
        'description' => 'Limpia optimizaciones',
    ],
    'migrate' => [
        'cmd' => ['php', 'artisan', 'migrate'],
        'type' => 'artisan',
        'description' => 'Ejecuta migraciones',
    ],

    'migrate_status' => [
        'cmd' => ['php', 'artisan', 'migrate:status'],
        'type' => 'artisan',
        'description' => 'Estados de las migraciones',
    ],

    'certificado_fix_nombre_laboratorio' => [
        'cmd' => ['php', 'artisan', 'certificado:fix-nombre-laboratorio'],
        'type' => 'artisan',
        'description' => 'Corrige caracteres especiales del nombre del laboratorio en certificados',
    ],
    'seeder_plantilla' => [
        'cmd' => ['php', 'artisan', 'db:seed', '--class=PlantillaCertificadoSeeder'],
        'type' => 'artisan',
        'description' => 'Ejecuta el seeder PlantillaCertificadoSeeder para poblar/actualizar las plantillas de certificados en la base de datos.',
    ]
];
