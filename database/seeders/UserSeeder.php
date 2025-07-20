<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $cargoAdmin = DB::table('cargo')->where('nombre_cargo', Permiso::ADMIN_NAME)->first();

        if ($cargoAdmin) {
            $user = User::create([
                'username'      => 'admin',
                'nombre'        => 'Juan',
                'ap_paterno'    => 'Pérez',
                'ap_materno'    => 'Lopez',
                'ci'            => '12345678',
                'telefono'      => '71234567',
                'email'         => 'admin@example.com',
                'password'      => Hash::make('password'),
                'status'        => true,
                'id_cargo'      => $cargoAdmin->id,
                'email_verified_at' => now(),
            ]);

            // Obtener el ID del permiso ADMIN
            $permisoId = Permiso::where('clave', Permiso::ADMIN)->value('id');

            if ($permisoId) {
                $user->permisos()->attach($permisoId);
            }
        }
    }
}
