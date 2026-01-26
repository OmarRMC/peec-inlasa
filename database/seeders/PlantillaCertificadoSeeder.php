<?php

namespace Database\Seeders;

use App\Models\PlantillaCertificado;
use Illuminate\Database\Seeder;

class PlantillaCertificadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlantillaCertificado::create([
            'nombre' => 'Bicentenario(Default)',
            'descripcion' => [
                'descripcion_desmp' => [
                    'text' => 'Por su desempeño en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de Laboratorios de Salud - PEEC INLASA en el área de:',
                    'style' => [
                        'width' => '60%',
                        'margin' => 'auto',
                        'text-align' => 'center',
                    ],
                ],
                'descripcion_part' => [
                    'text' => 'Por su participación en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de Laboratorios de Salud - PEEC INLASA, en',
                    'style' => [
                        'width' => '60%',
                        'margin' => 'auto',
                        'text-align' => 'center',
                    ],
                ],
            ],
            'imagen_fondo' => '/storage/certificados/plantillas/fondos/01KFNESJ44WTNCC5C6JE35ETXE.png',
            'ancho_mm' => 210.00,
            'alto_mm' => 297.00,
            'diseno' => [
                'nota' => [
                    'text' => 'El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron con el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados por los Informes de Evaluación emitidos durante la gestión '
                ],
            ],
            'firmas' => [
                [
                    'nombre' => 'Lic. Jhaneline Lizeth Mamani Poma',
                    'cargo' => 'JEFE PROGRAMA DE EVALUACIÓN EXTERNA DE LA CALIDAD',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFX8W8TM3X9BH4QYFSNQ5XHQ.png',
                ],
                [
                    'nombre' => 'Dra. Shirley Aramayo Wayar',
                    'cargo' => 'COORDINADORA DIVISIÓN RED DE LABORATORIOS DE SALUD PÚBLICA',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFX8W996TN76AAFWD76CMDWH.png',
                ],
                [
                    'nombre' => 'Dra. Evelin Esther Fortún Fernández',
                    'cargo' => 'DIRECTORA GENERAL EJECUTIVA',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFX8W99840QDVRNTH6HM5D7E.png',
                ],
            ],
            'activo' => false,
        ]);

        PlantillaCertificado::create([
            'nombre' => 'Plantilla Nueva',
            'descripcion' => [
                'descripcion_desmp' => [
                    'text' => 'Por su desempeño en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de Laboratorios de Salud - PEEC INLASA en el área de:',
                    'style' => [
                        'width' => '60%',
                        'margin' => 'auto',
                        'text-align' => 'center',
                    ],
                ],
                'descripcion_part' => [
                    'text' => 'Por su participación en el Programa de Evaluación Externa de la Calidad del Instituto Nacional de Laboratorios de Salud - PEEC INLASA, en',
                    'style' => [
                        'width' => '60%',
                        'margin' => 'auto',
                        'text-align' => 'center',
                    ],
                ],
            ],
            'imagen_fondo' => '/storage/certificados/plantillas/fondos/01KFNETJBRAX21XFB47HZXHEF2.png',
            'ancho_mm' => 297.00,
            'alto_mm' => 210.00,
            'diseno' => [
                'schemaVersion' => 1,
                'unit' => 'mm',
                'qr' => [
                    'position' => [
                        'right' => 6,
                        'bottom' => 6,
                    ],
                    'size' => [
                        'width' => 20,
                        'height' => 20,
                    ],
                ],
                'nota' => [
                    'text' => 'El PEEC INLASA, extiende el presente Certificado únicamente a los Laboratorios Clínicos que cumplieron con el porcentaje de participación y/o desempeño exigido, según procedimientos internos; respaldados por los Informes de Evaluación emitidos durante la gestión',
                    'position' => [
                        'left' => 48,
                        'bottom' => 12,
                        'width' => 70,
                    ],
                    'style' => [
                        'font-size' => '5pt',
                        'color' => '#111',
                    ],
                ],
                'elements' => [
                    [
                        'type' => 'text',
                        'text' => 'Gestión {{ gestion }}',
                        'position' => [
                            'left' => 0,
                            'bottom' => 12,
                        ],
                        'style' => [
                            'font-size' => '9pt',
                            'width' => '100%',
                            'text-align' => 'center',
                            'font-weight' => 'bold',
                            'font-family' => "'Annapurna SIL', 'Noto Serif', 'DejaVu Serif', 'Times New Roman', serif",
                        ],
                    ],
                ],
            ],
            'firmas' => [
                [
                    'nombre' => 'Dra. Jhayline Lizeth Mamani Poma',
                    'cargo' => 'JEFE PROGRAMA DE EVALUACIÓN EXTERNA DE LA CALIDAD',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFVTNT79YMSGNWWQE84FRXQY.png',
                ],
                [
                    'nombre' => 'Dra. Shirley Aramayo Wayar',
                    'cargo' => 'COORDINADORA DIVISIÓN RED DE LABORATORIOS DE SALUD PÚBLICA',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFVTNTMK0RW1S2DB5YAZF44G.png',
                ],
                [
                    'nombre' => 'Dra. Patricia Carla Vacaflor Torrico',
                    'cargo' => 'DIRECTORA GENERAL EJECUTIVA',
                    'firma' => '/storage/certificados/plantillas/firmas/01KFVTNTMMFSV0SAMRJYE2HR9N.png',
                ],
            ],
            'activo' => true,
        ]);
    }
}
