<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programa')->insertOrIgnore([
            ['id' => 1, 'descripcion' => 'ANÁLISIS CLÍNICOS', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'descripcion' => 'INMUNOLOGÍA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'descripcion' => 'PARASITOLOGÍA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'descripcion' => 'BACTERIOLOGÍA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'descripcion' => 'BANCOS DE SANGRE', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'descripcion' => 'PROGRAMA ITS/VIH-SIDA/HV', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'descripcion' => 'VIROLOGÍA Y GENÉTICA MOLECULAR', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'descripcion' => 'ENDOCRINOLOGÍA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'descripcion' => 'MARCADORES BIOLÓGICOS', 'status' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'descripcion' => 'CITOPATOLOGÍA', 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('area')->insertOrIgnore([
            ['id' => 1, 'id_programa' => 1, 'descripcion' => 'HEMATOLOGÍA Y QUÍMICA SANGUÍNEA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'id_programa' => 1, 'descripcion' => 'HEMOSTASIA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'id_programa' => 1, 'descripcion' => 'UROANALISIS', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'id_programa' => 2, 'descripcion' => 'INMUNOLOGÍA BÁSICA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'id_programa' => 2, 'descripcion' => 'SEROLOGÍA INFECCIOSA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'id_programa' => 3, 'descripcion' => 'SEROLOGÍA PARASITARIA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'id_programa' => 3, 'descripcion' => 'COPROANÁLISIS', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'id_programa' => 3, 'descripcion' => 'HEMOPARASITOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'id_programa' => 4, 'descripcion' => 'BACTERIOLOGÍA CLÍNICA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'id_programa' => 4, 'descripcion' => 'TUBERCULOSIS', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'id_programa' => 4, 'descripcion' => 'MICROBIOLOGIA DE ALIMENTOS', 'status' => false, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'id_programa' => 5, 'descripcion' => 'HEMATOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'id_programa' => 5, 'descripcion' => 'SEROLOGÍA INFECCIOSA Y PARASITOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'id_programa' => 6, 'descripcion' => 'SEROLOGÍA INFECCIOSA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'id_programa' => 7, 'descripcion' => 'SARS-COV-2', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'id_programa' => 7, 'descripcion' => 'PANEL VIRAL RESPIRATORIO', 'status' => false, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'id_programa' => 7, 'descripcion' => 'VIRUS DEL PAPILOMA HUMANO (HPV)', 'status' => false, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'id_programa' => 8, 'descripcion' => 'HORMONAS', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'id_programa' => 9, 'descripcion' => 'MARCADORES TUMORALES', 'status' => false, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'id_programa' => 9, 'descripcion' => 'MARCADORES CARDIACOS', 'status' => false, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'id_programa' => 10, 'descripcion' => 'CITOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'id_programa' => 1, 'descripcion' => 'HEMATOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'id_programa' => 1, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'id_programa' => 1, 'descripcion' => 'INMUNOHEMATOLOGÍA', 'status' => true, 'max_paquetes_inscribir' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('paquete')->insertOrIgnore([
            ['id' => 1, 'max_participantes' => 5, 'id_area' => 1, 'descripcion' => 'Primer Paquete H/QS', 'costo_paquete' => 1900, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'max_participantes' => 5, 'id_area' => 1, 'descripcion' => 'Segundo Paquete H/QS', 'costo_paquete' => 2750, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'max_participantes' => 5, 'id_area' => 1, 'descripcion' => 'Tercer Paquete H/QS', 'costo_paquete' => 3300, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'max_participantes' => 5, 'id_area' => 2, 'descripcion' => 'TP, APTT, INR, Fibrinógeno por coagulometria.', 'costo_paquete' => 700, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'max_participantes' => 5, 'id_area' => 4, 'descripcion' => 'Pruebas Básicas: FR, ASTO y PCR', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'max_participantes' => 5, 'id_area' => 4, 'descripcion' => 'Serología Anti CCP - ELISA', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'max_participantes' => 5, 'id_area' => 5, 'descripcion' => 'Serología Infecciosa VIH (prueba rápida y ELISA)', 'costo_paquete' => 330, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'max_participantes' => 5, 'id_area' => 5, 'descripcion' => 'Serología Infecciosa Sífilis', 'costo_paquete' => 300, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'max_participantes' => 5, 'id_area' => 5, 'descripcion' => 'Serología para HBV: HBsAg y HBcAc (ELISA)', 'costo_paquete' => 500, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'max_participantes' => 5, 'id_area' => 5, 'descripcion' => 'Serología para HVC (ELISA)', 'costo_paquete' => 440, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'max_participantes' => 5, 'id_area' => 6, 'descripcion' => 'Serología para Chagas', 'costo_paquete' => 660, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'max_participantes' => 5, 'id_area' => 6, 'descripcion' => 'Serología para Toxoplasmosis', 'costo_paquete' => 440, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'max_participantes' => 5, 'id_area' => 7, 'descripcion' => 'Enteroparásitos', 'costo_paquete' => 450, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 14, 'max_participantes' => 5, 'id_area' => 8, 'descripcion' => 'Malaria', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'max_participantes' => 5, 'id_area' => 8, 'descripcion' => 'Leishmania', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 16, 'max_participantes' => 5, 'id_area' => 9, 'descripcion' => 'Bacteriología', 'costo_paquete' => 2000, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 17, 'max_participantes' => 5, 'id_area' => 10, 'descripcion' => 'Baciloscopía', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 18, 'max_participantes' => 5, 'id_area' => 12, 'descripcion' => 'Hemoglobina y Hematocrito', 'costo_paquete' => 550, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 19, 'max_participantes' => 5, 'id_area' => 13, 'descripcion' => 'Serología', 'costo_paquete' => 1100, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 20, 'max_participantes' => 5, 'id_area' => 14, 'descripcion' => 'Serología infecciosa', 'costo_paquete' => 550, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 21, 'max_participantes' => 5, 'id_area' => 15, 'descripcion' => 'SARS-CoV-2 por RT-PCR', 'costo_paquete' => 1950, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 22, 'max_participantes' => 5, 'id_area' => 15, 'descripcion' => 'Anticuerpos Anti SARS-CoV-2', 'costo_paquete' => 2000, 'status' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 23, 'max_participantes' => 5, 'id_area' => 18, 'descripcion' => 'TSH, T3 total, T4 total, T4 libre, Estradiol, Progesterona, LH, FSH, Testosterona Total y Prolactina', 'costo_paquete' => 600, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 24, 'max_participantes' => 5, 'id_area' => 21, 'descripcion' => 'Papanicolaou - Citología De Cuello Uterino', 'costo_paquete' => 350, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 25, 'max_participantes' => 5, 'id_area' => 17, 'descripcion' => 'TSH, T3 total, T4 total, T4 libre, Estradiol, Progesterona, LH, FSH, Testosterona Total y Prolactina', 'costo_paquete' => 550, 'status' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 26, 'max_participantes' => 5, 'id_area' => 22, 'descripcion' => 'Frotis sanguíneo básico', 'costo_paquete' => 300, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 27, 'max_participantes' => 5, 'id_area' => 22, 'descripcion' => 'Frotis sanguíneo avanzado', 'costo_paquete' => 350, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 28, 'max_participantes' => 5, 'id_area' => 3, 'descripcion' => 'Examen químico y sedimento urinario - Paquete 1', 'costo_paquete' => 330, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 29, 'max_participantes' => 5, 'id_area' => 3, 'descripcion' => 'Examen químico y sedimento urinario - Paquete 2', 'costo_paquete' => 380, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 30, 'max_participantes' => 5, 'id_area' => 23, 'descripcion' => 'Química Sanguínea: Paquete 1', 'costo_paquete' => 1500, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 31, 'max_participantes' => 5, 'id_area' => 23, 'descripcion' => 'Química Sanguínea: Paquete 2', 'costo_paquete' => 2300, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 32, 'max_participantes' => 5, 'id_area' => 23, 'descripcion' => 'Química Sanguínea: Paquete 3', 'costo_paquete' => 2800, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 33, 'max_participantes' => 5, 'id_area' => 24, 'descripcion' => 'Grupo sanguíneo', 'costo_paquete' => 450, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 34, 'max_participantes' => 5, 'id_area' => 24, 'descripcion' => 'Coombs Directo e Indirecto', 'costo_paquete' => 450, 'status' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 35, 'max_participantes' => 5, 'id_area' => 12, 'descripcion' => 'Grupo Sanguíneo', 'costo_paquete' => 450, 'status' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('ensayo_aptitud')->insertOrIgnore([
            ['id' => 1, 'id_paquete' => 1, 'descripcion' => 'HEMATOLOGÍA', 'status' => 1],
            ['id' => 2, 'id_paquete' => 1, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 3, 'id_paquete' => 2, 'descripcion' => 'HEMATOLOGÍA', 'status' => 1],
            ['id' => 4, 'id_paquete' => 2, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 5, 'id_paquete' => 3, 'descripcion' => 'HEMATOLOGÍA', 'status' => 1],
            ['id' => 6, 'id_paquete' => 3, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 7, 'id_paquete' => 4, 'descripcion' => 'HEMOSTASIA ', 'status' => 1],
            ['id' => 8, 'id_paquete' => 5, 'descripcion' => 'PROTEINA C REACTIVA', 'status' => 1],
            ['id' => 9, 'id_paquete' => 5, 'descripcion' => 'FACTOR REUMATOIDEO', 'status' => 1],
            ['id' => 10, 'id_paquete' => 5, 'descripcion' => 'ANTIESTREPTOLISINA O', 'status' => 1],
            ['id' => 11, 'id_paquete' => 6, 'descripcion' => 'ANTI CCP', 'status' => 1],
            ['id' => 12, 'id_paquete' => 7, 'descripcion' => 'VIH', 'status' => 1],
            ['id' => 13, 'id_paquete' => 8, 'descripcion' => 'SIFILIS', 'status' => 1],
            ['id' => 14, 'id_paquete' => 9, 'descripcion' => 'HEPATITIS B', 'status' => 1],
            ['id' => 15, 'id_paquete' => 9, 'descripcion' => 'HEPATITIS B Ac ANTI CORE', 'status' => 1],
            ['id' => 16, 'id_paquete' => 10, 'descripcion' => 'HEPATITIS C', 'status' => 1],
            ['id' => 17, 'id_paquete' => 11, 'descripcion' => 'CHAGAS', 'status' => 1],
            ['id' => 18, 'id_paquete' => 12, 'descripcion' => 'TOXOPLASMOSIS', 'status' => 1],
            ['id' => 19, 'id_paquete' => 13, 'descripcion' => 'ENTEROPARASITOS', 'status' => 1],
            ['id' => 20, 'id_paquete' => 14, 'descripcion' => 'MALARIA', 'status' => 1],
            ['id' => 21, 'id_paquete' => 15, 'descripcion' => 'LEISHMANIASIS', 'status' => 1],
            ['id' => 22, 'id_paquete' => 16, 'descripcion' => 'BACTERIOLOGÍA', 'status' => 1],
            ['id' => 23, 'id_paquete' => 17, 'descripcion' => ' BACILOSCOPÍA', 'status' => 1],
            ['id' => 24, 'id_paquete' => 18, 'descripcion' => 'HEMATOLOGÍA', 'status' => 1],
            ['id' => 25, 'id_paquete' => 19, 'descripcion' => 'SEROLOGÍA', 'status' => 1],
            ['id' => 26, 'id_paquete' => 20, 'descripcion' => 'SEROLOGÍA INFECCIOSA', 'status' => 1],
            ['id' => 27, 'id_paquete' => 21, 'descripcion' => 'RT-PCR PARA SARS-CoV-2', 'status' => 1],
            ['id' => 28, 'id_paquete' => 22, 'descripcion' => 'Anticuerpos Anti SARS-CoV-2', 'status' => 0],
            ['id' => 29, 'id_paquete' => 23, 'descripcion' => 'HORMONAS', 'status' => 1],
            ['id' => 30, 'id_paquete' => 24, 'descripcion' => 'CITOPATOLOGÍA PAP', 'status' => 1],
            ['id' => 31, 'id_paquete' => 23, 'descripcion' => 'HORMONAS', 'status' => 1],
            ['id' => 32, 'id_paquete' => 26, 'descripcion' => 'FROTIS SANGUÍNEO', 'status' => 1],
            ['id' => 33, 'id_paquete' => 27, 'descripcion' => 'FROTIS SANGUÍNEO', 'status' => 1],
            ['id' => 34, 'id_paquete' => 28, 'descripcion' => 'UROANÁLISIS ', 'status' => 1],
            ['id' => 35, 'id_paquete' => 29, 'descripcion' => 'UROANÁLISIS ', 'status' => 1],
            ['id' => 36, 'id_paquete' => 30, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 37, 'id_paquete' => 31, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 38, 'id_paquete' => 32, 'descripcion' => 'QUÍMICA SANGUÍNEA', 'status' => 1],
            ['id' => 39, 'id_paquete' => 33, 'descripcion' => 'GRUPO SANGUÍNEO', 'status' => 1],
            ['id' => 40, 'id_paquete' => 34, 'descripcion' => 'GRUPO SANGUÍNEO', 'status' => 1],
        ]);
    }
}
