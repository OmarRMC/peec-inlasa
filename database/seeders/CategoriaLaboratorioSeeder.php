<?php

namespace Database\Seeders;

use App\Models\CategoriaLaboratorio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaLaboratorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoriaLaboratorio::create([
            "descripcion" => "Privado"
        ]);

        CategoriaLaboratorio::create([
            "descripcion" => "Publico"
        ]);

        CategoriaLaboratorio::create([
            "descripcion" => "Convenio"
        ]);

        CategoriaLaboratorio::create([
            "descripcion" => "Seguro Social"
        ]);
    }
}
