<?php

namespace Database\Seeders;

use App\Models\Permiso;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisoSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Permiso::PERMISOS_HABILITADOS as $clave => $nombre) {
            Permiso::firstOrCreate(
                ['clave' => $clave],
                [
                    'nombre_permiso' => $nombre,
                    'descripcion' => $nombre,
                    'status' => true,
                ]
            );
        }
    }
}
