<?php

namespace Database\Seeders;

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
            'nombre_cargo' => Permiso::RESPONSABLE_NAME,
            'status' => true
        ]);
        DB::table('cargo')->insert([
            'nombre_cargo' => Permiso::LABORATORIO_NAME,
            'status' => true
        ]);
    }
}
