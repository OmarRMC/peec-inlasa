<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formulario;

class FormularioSeeder extends Seeder
{
    public function run(): void
    {
        Formulario::updateOrCreate(
            ['codigo' => 'PEEC-FOR-03.01'],
            [
                'titulo' => 'Programa de Evaluación Externa de la Calidad',
                'proceso' => 'inscripcion',
                'version' => '2',
                'descripcion' => 'Formulario oficial para la inscripción al PEEC',
                'status' => true,
                'fec_formulario' => now(),
            ]
        );
    }
}
