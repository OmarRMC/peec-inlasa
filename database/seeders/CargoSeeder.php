<?php

namespace Database\Seeders;

use App\Models\Cargo;
use App\Models\Permiso;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cargo')->insert([
            'nombre_cargo' => Permiso::ADMIN_NAME,
            'status' => true
        ]);
        DB::table('cargo')->insert([
            'nombre_cargo' => Cargo::RESPOSABLE_EA,
            'status' => true
        ]);

        DB::table('cargo')->insert([
            'nombre_cargo' => Cargo::SOPORTE,
            'status' => true
        ]);
    }
}
