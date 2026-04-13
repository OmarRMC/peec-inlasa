<?php

namespace Database\Seeders;

use App\Models\RecursoLaboratorio;
use Illuminate\Database\Seeder;

class RecursoLaboratorioSeeder extends Seeder
{
    public function run(): void
    {
        $recursos = [
            [
                'titulo'  => 'Convocatoria',
                'url'     => 'https://drive.google.com/file/d/1dXps5OmC_iOXUZAsR1kxAGFyP6vJytUm/view?usp=sharing',
                'archivo' => null,
                'status'  => true,
            ],
            [
                'titulo'  => 'Resolución',
                'url'     => 'https://drive.google.com/file/d/1iY9cjaeMwUp9OBr7gNIRjGQUBSrlKFDp/view?usp=sharing',
                'archivo' => null,
                'status'  => true,
            ],
        ];

        foreach ($recursos as $recurso) {
            RecursoLaboratorio::firstOrCreate(
                ['titulo' => $recurso['titulo']],
                $recurso
            );
        }
    }
}
