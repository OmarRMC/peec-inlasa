<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelLaboratorioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nivel_laboratorio')->insert([
            ['id' => 1, 'descripcion_nivel' => '1er Nivel', 'status_nivel' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'descripcion_nivel' => '2do Nivel', 'status_nivel' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'descripcion_nivel' => '3er Nivel', 'status_nivel' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
