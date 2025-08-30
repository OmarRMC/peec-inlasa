<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoLaboratorioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo_laboratorio')->insert([
            ['id' => 1, 'descripcion' => 'Banco Sangre', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'descripcion' => 'Analisis', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'descripcion' => 'S.T.', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'descripcion' => 'CD VIR', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'descripcion' => 'CRVIR', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
