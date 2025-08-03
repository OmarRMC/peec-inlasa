<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pais')->insert([
            ['id' => 1, 'nombre_pais' => 'BOLIVIA',    'sigla_pais' => 'BOL', 'cod_pais' => 591, 'status_pais' => true],
            ['id' => 2, 'nombre_pais' => 'ARGENTINA', 'sigla_pais' => 'ARG', 'cod_pais' => 54,  'status_pais' => false],
            ['id' => 3, 'nombre_pais' => 'BRASIL',     'sigla_pais' => 'BRA', 'cod_pais' => 55,  'status_pais' => false],
            ['id' => 4, 'nombre_pais' => 'CHILE',      'sigla_pais' => 'CHI', 'cod_pais' => 56,  'status_pais' => false],
            ['id' => 5, 'nombre_pais' => 'PERÚ',       'sigla_pais' => 'PER', 'cod_pais' => 51,  'status_pais' => false],
        ]);

        DB::table('departamento')->insert([
            ['id' => 1,  'id_pais' => 1, 'nombre_dep' => 'CHUQUISACA',  'sigla_dep' => 'CHQ', 'status_dep' => true],
            ['id' => 2,  'id_pais' => 1, 'nombre_dep' => 'LA PAZ',      'sigla_dep' => 'LPZ', 'status_dep' => true],
            ['id' => 3,  'id_pais' => 1, 'nombre_dep' => 'COCHABAMBA',  'sigla_dep' => 'CBB', 'status_dep' => true],
            ['id' => 4,  'id_pais' => 1, 'nombre_dep' => 'ORURO',       'sigla_dep' => 'ORU', 'status_dep' => true],
            ['id' => 5,  'id_pais' => 1, 'nombre_dep' => 'POTOSI',      'sigla_dep' => 'PTS', 'status_dep' => true],
            ['id' => 6,  'id_pais' => 1, 'nombre_dep' => 'TARIJA',      'sigla_dep' => 'TRJ', 'status_dep' => true],
            ['id' => 7,  'id_pais' => 1, 'nombre_dep' => 'SANTA CRUZ',  'sigla_dep' => 'SCZ', 'status_dep' => true],
            ['id' => 8,  'id_pais' => 1, 'nombre_dep' => 'BENI',        'sigla_dep' => 'BEN', 'status_dep' => true],
            ['id' => 9,  'id_pais' => 1, 'nombre_dep' => 'PANDO',       'sigla_dep' => 'PND', 'status_dep' => true],
            ['id' => 10, 'id_pais' => 2, 'nombre_dep' => 'BUENOS AIRES', 'sigla_dep' => 'BAR', 'status_dep' => false],
            ['id' => 11, 'id_pais' => 3, 'nombre_dep' => 'RÍO DE JANEIRO', 'sigla_dep' => 'RJA', 'status_dep' => false],
            ['id' => 12, 'id_pais' => 3, 'nombre_dep' => 'SAO PAULO',   'sigla_dep' => 'SPA', 'status_dep' => false],
            ['id' => 13, 'id_pais' => 3, 'nombre_dep' => 'BRASILIA',    'sigla_dep' => 'BRS', 'status_dep' => false],
            ['id' => 14, 'id_pais' => 4, 'nombre_dep' => 'SANTIAGO DE CHILE', 'sigla_dep' => 'SAC', 'status_dep' => false],
            ['id' => 15, 'id_pais' => 5, 'nombre_dep' => 'LIMA',         'sigla_dep' => 'LIM', 'status_dep' => false],
        ]);

        DB::table('provincia')->insert([
            ['id' => 1,  'id_dep' => 1, 'nombre_prov' => 'OROPEZA',                        'cod_prov' => 101, 'status_prov' => true],
            ['id' => 2,  'id_dep' => 1, 'nombre_prov' => 'AZURDUY',                        'cod_prov' => 102, 'status_prov' => true],
            ['id' => 3,  'id_dep' => 1, 'nombre_prov' => 'ZUDAÑEZ',                        'cod_prov' => 103, 'status_prov' => true],
            ['id' => 4,  'id_dep' => 1, 'nombre_prov' => 'TOMINA',                         'cod_prov' => 104, 'status_prov' => true],
            ['id' => 5,  'id_dep' => 1, 'nombre_prov' => 'HERNANDO SILES',                'cod_prov' => 105, 'status_prov' => true],
            ['id' => 6,  'id_dep' => 1, 'nombre_prov' => 'YAMPARAEZ',                     'cod_prov' => 106, 'status_prov' => true],
            ['id' => 7,  'id_dep' => 1, 'nombre_prov' => 'NOR CINTI',                      'cod_prov' => 107, 'status_prov' => true],
            ['id' => 8,  'id_dep' => 1, 'nombre_prov' => 'BELISARIO BOETO',               'cod_prov' => 108, 'status_prov' => true],
            ['id' => 9,  'id_dep' => 1, 'nombre_prov' => 'SUD CINTI',                      'cod_prov' => 109, 'status_prov' => true],
            ['id' => 10, 'id_dep' => 1, 'nombre_prov' => 'LUIS CALVO',                    'cod_prov' => 110, 'status_prov' => true],

            ['id' => 11, 'id_dep' => 2, 'nombre_prov' => 'MURILLO',                       'cod_prov' => 201, 'status_prov' => true],
            ['id' => 12, 'id_dep' => 2, 'nombre_prov' => 'OMASUYOS',                      'cod_prov' => 202, 'status_prov' => true],
            ['id' => 13, 'id_dep' => 2, 'nombre_prov' => 'PACAJES',                       'cod_prov' => 203, 'status_prov' => true],
            ['id' => 14, 'id_dep' => 2, 'nombre_prov' => 'CAMACHO',                       'cod_prov' => 204, 'status_prov' => true],
            ['id' => 15, 'id_dep' => 2, 'nombre_prov' => 'MUÑECAS',                       'cod_prov' => 205, 'status_prov' => true],
            ['id' => 16, 'id_dep' => 2, 'nombre_prov' => 'LARECAJA',                      'cod_prov' => 206, 'status_prov' => true],
            ['id' => 17, 'id_dep' => 2, 'nombre_prov' => 'FRANZ TAMAYO',                  'cod_prov' => 207, 'status_prov' => true],
            ['id' => 18, 'id_dep' => 2, 'nombre_prov' => 'INGAVI',                        'cod_prov' => 208, 'status_prov' => true],
            ['id' => 19, 'id_dep' => 2, 'nombre_prov' => 'LOAYZA',                        'cod_prov' => 209, 'status_prov' => true],
            ['id' => 20, 'id_dep' => 2, 'nombre_prov' => 'INQUISIVI',                     'cod_prov' => 210, 'status_prov' => true],
            ['id' => 21, 'id_dep' => 2, 'nombre_prov' => 'SUR YUNGAS',                    'cod_prov' => 211, 'status_prov' => true],
            ['id' => 22, 'id_dep' => 2, 'nombre_prov' => 'LOS ANDES',                     'cod_prov' => 212, 'status_prov' => true],
            ['id' => 23, 'id_dep' => 2, 'nombre_prov' => 'AROMA',                         'cod_prov' => 213, 'status_prov' => true],
            ['id' => 24, 'id_dep' => 2, 'nombre_prov' => 'NOR YUNGAS',                    'cod_prov' => 214, 'status_prov' => true],
            ['id' => 25, 'id_dep' => 2, 'nombre_prov' => 'ABEL ITURRALDE',               'cod_prov' => 215, 'status_prov' => true],
            ['id' => 26, 'id_dep' => 2, 'nombre_prov' => 'BAUTISTA SAAVEDRA',            'cod_prov' => 216, 'status_prov' => true],
            ['id' => 27, 'id_dep' => 2, 'nombre_prov' => 'MANCO KAPAC',                   'cod_prov' => 217, 'status_prov' => true],
            ['id' => 28, 'id_dep' => 2, 'nombre_prov' => 'GUALBERTO VILLARROEL',         'cod_prov' => 218, 'status_prov' => true],
            ['id' => 29, 'id_dep' => 2, 'nombre_prov' => 'GRAL. J. MANUEL PANDO',        'cod_prov' => 219, 'status_prov' => true],
            ['id' => 30, 'id_dep' => 2, 'nombre_prov' => 'CARANAVI',                      'cod_prov' => 220, 'status_prov' => true],

            ['id' => 31, 'id_dep' => 3, 'nombre_prov' => 'CERCADO',                      'cod_prov' => 301, 'status_prov' => true],
            ['id' => 32, 'id_dep' => 3, 'nombre_prov' => 'CAMPERO',                      'cod_prov' => 302, 'status_prov' => true],
            ['id' => 33, 'id_dep' => 3, 'nombre_prov' => 'AYOPAYA',                      'cod_prov' => 303, 'status_prov' => true],
            ['id' => 34, 'id_dep' => 3, 'nombre_prov' => 'ESTEBAN ARCE',                 'cod_prov' => 304, 'status_prov' => true],
            ['id' => 35, 'id_dep' => 3, 'nombre_prov' => 'ARANI',                        'cod_prov' => 305, 'status_prov' => true],
            ['id' => 36, 'id_dep' => 3, 'nombre_prov' => 'ARQUE',                        'cod_prov' => 306, 'status_prov' => true],
            ['id' => 37, 'id_dep' => 3, 'nombre_prov' => 'CAPINOTA',                     'cod_prov' => 307, 'status_prov' => true],
            ['id' => 38, 'id_dep' => 3, 'nombre_prov' => 'GERMAN JORDAN',                'cod_prov' => 308, 'status_prov' => true],
            ['id' => 39, 'id_dep' => 3, 'nombre_prov' => 'QUILLACOLLO',                  'cod_prov' => 309, 'status_prov' => true],
            ['id' => 40, 'id_dep' => 3, 'nombre_prov' => 'CHAPARE',                      'cod_prov' => 310, 'status_prov' => true],
            ['id' => 41, 'id_dep' => 3, 'nombre_prov' => 'TAPACARI',                     'cod_prov' => 311, 'status_prov' => true],
            ['id' => 42, 'id_dep' => 3, 'nombre_prov' => 'CARRASCO',                     'cod_prov' => 312, 'status_prov' => true],
            ['id' => 43, 'id_dep' => 3, 'nombre_prov' => 'MIZQUE',                       'cod_prov' => 313, 'status_prov' => true],
            ['id' => 44, 'id_dep' => 3, 'nombre_prov' => 'PUNATA',                       'cod_prov' => 314, 'status_prov' => true],
            ['id' => 45, 'id_dep' => 3, 'nombre_prov' => 'BOLIVAR',                      'cod_prov' => 315, 'status_prov' => true],
            ['id' => 46, 'id_dep' => 3, 'nombre_prov' => 'TIRAQUE',                      'cod_prov' => 316, 'status_prov' => true],

            ['id' => 47, 'id_dep' => 4, 'nombre_prov' => 'CERCADO',                      'cod_prov' => 401, 'status_prov' => true],
            ['id' => 48, 'id_dep' => 4, 'nombre_prov' => 'ABAROA',                       'cod_prov' => 402, 'status_prov' => true],
            ['id' => 49, 'id_dep' => 4, 'nombre_prov' => 'CARANGAS',                     'cod_prov' => 403, 'status_prov' => true],
            ['id' => 50, 'id_dep' => 4, 'nombre_prov' => 'SAJAMA',                       'cod_prov' => 404, 'status_prov' => true],
            ['id' => 51, 'id_dep' => 4, 'nombre_prov' => 'LITORAL',                      'cod_prov' => 405, 'status_prov' => true],
            ['id' => 52, 'id_dep' => 4, 'nombre_prov' => 'POOPO',                        'cod_prov' => 406, 'status_prov' => true],
            ['id' => 53, 'id_dep' => 4, 'nombre_prov' => 'PANTALEON DALENCE',            'cod_prov' => 407, 'status_prov' => true],
            ['id' => 54, 'id_dep' => 4, 'nombre_prov' => 'LADISLAO CABRERA',            'cod_prov' => 408, 'status_prov' => true],
            ['id' => 55, 'id_dep' => 4, 'nombre_prov' => 'SABAYA',                       'cod_prov' => 409, 'status_prov' => true],
            ['id' => 56, 'id_dep' => 4, 'nombre_prov' => 'SAUCARI',                      'cod_prov' => 410, 'status_prov' => true],
            ['id' => 57, 'id_dep' => 4, 'nombre_prov' => 'TOMAS BARRON',                 'cod_prov' => 411, 'status_prov' => true],
            ['id' => 58, 'id_dep' => 4, 'nombre_prov' => 'SUR CARANGAS',                 'cod_prov' => 412, 'status_prov' => true],
            ['id' => 59, 'id_dep' => 4, 'nombre_prov' => 'S. PEDRO DE TOTORA',          'cod_prov' => 413, 'status_prov' => true],
            ['id' => 60, 'id_dep' => 4, 'nombre_prov' => 'SEBASTIAN PAGADOR',           'cod_prov' => 414, 'status_prov' => true],
            ['id' => 61, 'id_dep' => 4, 'nombre_prov' => 'MEJILLONES',                   'cod_prov' => 415, 'status_prov' => true],
            ['id' => 62, 'id_dep' => 4, 'nombre_prov' => 'NOR CARANGAS',                'cod_prov' => 416, 'status_prov' => true],

        ]);

        DB::table('municipio')->insert([
            ['id' => 1, 'id_prov' => 1, 'nombre_municipio' => 'SUCRE', 'cod_municipio' => 10101, 'status_municipio' => true],
            ['id' => 2, 'id_prov' => 1, 'nombre_municipio' => 'YOTALA', 'cod_municipio' => 10102, 'status_municipio' => true],
            ['id' => 3, 'id_prov' => 1, 'nombre_municipio' => 'POROMA', 'cod_municipio' => 10103, 'status_municipio' => true],
            // ['id' => 4, 'id_prov' => 2, 'nombre_municipio' => 'VILLA AZURDUY', 'cod_municipio' => 10201, 'status_municipio' => true],
            // ['id' => 5, 'id_prov' => 2, 'nombre_municipio' => 'TARVITA', 'cod_municipio' => 10202, 'status_municipio' => true],
            // ['id' => 1, 'id_prov' => 1, 'nombre_municipio' => 'SUCRE',    'cod_municipio' => 10101, 'status_municipio' => true],
            // ['id' => 2, 'id_prov' => 1, 'nombre_municipio' => 'YOTALA',   'cod_municipio' => 10102, 'status_municipio' => true],
            // ['id' => 3, 'id_prov' => 1, 'nombre_municipio' => 'POROMA',   'cod_municipio' => 10103, 'status_municipio' => true],

            // Azurduy (id_prov = 2)
            ['id' => 4, 'id_prov' => 2, 'nombre_municipio' => 'AZURDUY',  'cod_municipio' => 10201, 'status_municipio' => true],
            ['id' => 5, 'id_prov' => 2, 'nombre_municipio' => 'TARVITA',   'cod_municipio' => 10202, 'status_municipio' => true],
            ['id' => 6, 'id_prov' => 2, 'nombre_municipio' => 'SAN PEDRO', 'cod_municipio' => 10203, 'status_municipio' => true], // cantón adicional :contentReference[oaicite:1]{index=1}

            // Zudañez (id_prov = 3)
            ['id' => 7, 'id_prov' => 3, 'nombre_municipio' => 'VILLA ZUDAÑEZ', 'cod_municipio' => 10301, 'status_municipio' => true],
            ['id' => 8, 'id_prov' => 3, 'nombre_municipio' => 'PRESTO',         'cod_municipio' => 10302, 'status_municipio' => true],
            ['id' => 9, 'id_prov' => 3, 'nombre_municipio' => 'MOJONGOYA',      'cod_municipio' => 10303, 'status_municipio' => true], // corregido a Villa Mojocoya
            ['id' => 10, 'id_prov' => 3, 'nombre_municipio' => 'ICLA',          'cod_municipio' => 10304, 'status_municipio' => true],

            // Tomina (id_prov = 4)
            ['id' => 11, 'id_prov' => 4, 'nombre_municipio' => 'PADILLA',    'cod_municipio' => 10401, 'status_municipio' => true],
            ['id' => 12, 'id_prov' => 4, 'nombre_municipio' => 'TOMINA',     'cod_municipio' => 10402, 'status_municipio' => true],
            ['id' => 13, 'id_prov' => 4, 'nombre_municipio' => 'SOPACHUY',   'cod_municipio' => 10403, 'status_municipio' => true],
            ['id' => 14, 'id_prov' => 4, 'nombre_municipio' => 'VILLA ALCALÁ', 'cod_municipio' => 10404, 'status_municipio' => true],
            ['id' => 15, 'id_prov' => 4, 'nombre_municipio' => 'EL VILLAR',  'cod_municipio' => 10405, 'status_municipio' => true],

            // Hernando Siles (id_prov = 5)
            ['id' => 16, 'id_prov' => 5, 'nombre_municipio' => 'MONTEAGUDO', 'cod_municipio' => 10501, 'status_municipio' => true],
            ['id' => 17, 'id_prov' => 5, 'nombre_municipio' => 'HUACARETA',  'cod_municipio' => 10502, 'status_municipio' => true],
            ['id' => 18, 'id_prov' => 5, 'nombre_municipio' => 'SAN PABLO',  'cod_municipio' => 10503, 'status_municipio' => true], // sección adicional :contentReference[oaicite:2]{index=2}

            // Yamparáez (id_prov = 6)
            ['id' => 19, 'id_prov' => 6, 'nombre_municipio' => 'TARABUCO',  'cod_municipio' => 10601, 'status_municipio' => true],
            ['id' => 20, 'id_prov' => 6, 'nombre_municipio' => 'YAMPARAEZ', 'cod_municipio' => 10602, 'status_municipio' => true],
            ['id' => 21, 'id_prov' => 6, 'nombre_municipio' => 'VILLA CHARCAS', 'cod_municipio' => 10603, 'status_municipio' => true], // integrar Nor Cinti sección incluida en Yamparáez contexto :contentReference[oaicite:3]{index=3}

            // Nor Cinti (id_prov = 7)
            ['id' => 22, 'id_prov' => 7, 'nombre_municipio' => 'CAMARGO',      'cod_municipio' => 10701, 'status_municipio' => true],
            ['id' => 23, 'id_prov' => 7, 'nombre_municipio' => 'SAN LUCAS',    'cod_municipio' => 10702, 'status_municipio' => true],
            ['id' => 24, 'id_prov' => 7, 'nombre_municipio' => 'INCAHUASI',    'cod_municipio' => 10703, 'status_municipio' => true],
            ['id' => 25, 'id_prov' => 7, 'nombre_municipio' => 'VILLA CHARCAS', 'cod_municipio' => 10704, 'status_municipio' => true],

            // Belisario Boeto (id_prov = 8)
            ['id' => 26, 'id_prov' => 8, 'nombre_municipio' => 'VILLA SERRANO', 'cod_municipio' => 10801, 'status_municipio' => true],

            // Sud Cinti (id_prov = 9)
            ['id' => 27, 'id_prov' => 9, 'nombre_municipio' => 'VILLA ABECIA', 'cod_municipio' => 10901, 'status_municipio' => true],
            ['id' => 28, 'id_prov' => 9, 'nombre_municipio' => 'CULPINA',        'cod_municipio' => 10902, 'status_municipio' => true],
            ['id' => 29, 'id_prov' => 9, 'nombre_municipio' => 'LAS CARRERAS',  'cod_municipio' => 10903, 'status_municipio' => true],

            // Luis Calvo (id_prov = 10)
            ['id' => 30, 'id_prov' => 10, 'nombre_municipio' => 'MUYUPAMPA',    'cod_municipio' => 11001, 'status_municipio' => true],
            ['id' => 31, 'id_prov' => 10, 'nombre_municipio' => 'HUACAYA',       'cod_municipio' => 11002, 'status_municipio' => true],
            ['id' => 32, 'id_prov' => 10, 'nombre_municipio' => 'MACHARETÍ',     'cod_municipio' => 11003, 'status_municipio' => true],

            // Provincia Murillo (id_dep = 2, id_prov suponemos siguiente consecutivo)
            ['id' => 33, 'id_prov' => 11, 'nombre_municipio' => 'NUESTRA SEÑORA DE LA PAZ', 'cod_municipio' => 20101, 'status_municipio' => true],
            ['id' => 34, 'id_prov' => 11, 'nombre_municipio' => 'EL ALTO', 'cod_municipio' => 20102, 'status_municipio' => true],
            ['id' => 35, 'id_prov' => 11, 'nombre_municipio' => 'PALCA', 'cod_municipio' => 20103, 'status_municipio' => true],
            ['id' => 36, 'id_prov' => 11, 'nombre_municipio' => 'MECAPACA', 'cod_municipio' => 20104, 'status_municipio' => true],
            ['id' => 37, 'id_prov' => 11, 'nombre_municipio' => 'ACHOCALLA', 'cod_municipio' => 20105, 'status_municipio' => true],

            // Provincia Omasuyos (id_prov = 12)
            ['id' => 38, 'id_prov' => 12, 'nombre_municipio' => 'ACHACACHI', 'cod_municipio' => 20201, 'status_municipio' => true],
            ['id' => 39, 'id_prov' => 12, 'nombre_municipio' => 'ANCORAIMES', 'cod_municipio' => 20202, 'status_municipio' => true],
            ['id' => 40, 'id_prov' => 12, 'nombre_municipio' => 'HUARINA', 'cod_municipio' => 20203, 'status_municipio' => true],
            ['id' => 41, 'id_prov' => 12, 'nombre_municipio' => 'CHUA COCANI', 'cod_municipio' => 20204, 'status_municipio' => true],
            ['id' => 42, 'id_prov' => 12, 'nombre_municipio' => 'HUATAJATA', 'cod_municipio' => 20205, 'status_municipio' => true],

            // Provincia Pacajes (id_prov = 15)
            ['id' => 43, 'id_prov' => 15, 'nombre_municipio' => 'CORO CORO', 'cod_municipio' => 21501, 'status_municipio' => true],
            ['id' => 44, 'id_prov' => 15, 'nombre_municipio' => 'CAQUIAVIRI', 'cod_municipio' => 21502, 'status_municipio' => true],
            ['id' => 45, 'id_prov' => 15, 'nombre_municipio' => 'CALACOTO', 'cod_municipio' => 21503, 'status_municipio' => true],
            ['id' => 46, 'id_prov' => 15, 'nombre_municipio' => 'COMANCHE', 'cod_municipio' => 21504, 'status_municipio' => true],
            ['id' => 47, 'id_prov' => 15, 'nombre_municipio' => 'CHARAÑA', 'cod_municipio' => 21505, 'status_municipio' => true],

            // Provincia Camacho (id_prov = 5)
            ['id' => 48, 'id_prov' => 5, 'nombre_municipio' => 'PUERTO ACOSTA', 'cod_municipio' => 20501, 'status_municipio' => true],
            ['id' => 49, 'id_prov' => 5, 'nombre_municipio' => 'MOCOMOCO', 'cod_municipio' => 20502, 'status_municipio' => true],
            ['id' => 50, 'id_prov' => 5, 'nombre_municipio' => 'ESCOMA', 'cod_municipio' => 20503, 'status_municipio' => true],

            // Provincia Muñecas (id_prov = 7)
            ['id' => 51, 'id_prov' => 7, 'nombre_municipio' => 'CHUMA', 'cod_municipio' => 20701, 'status_municipio' => true],
            ['id' => 52, 'id_prov' => 7, 'nombre_municipio' => 'AYATA', 'cod_municipio' => 20702, 'status_municipio' => true],
            ['id' => 53, 'id_prov' => 7, 'nombre_municipio' => 'AUCAPATA', 'cod_municipio' => 20703, 'status_municipio' => true],

            // Provincia Larecaja (id_prov = 6)
            ['id' => 54, 'id_prov' => 6, 'nombre_municipio' => 'SORATA', 'cod_municipio' => 20601, 'status_municipio' => true],
            ['id' => 55, 'id_prov' => 6, 'nombre_municipio' => 'GUANAY', 'cod_municipio' => 20602, 'status_municipio' => true],
            ['id' => 56, 'id_prov' => 6, 'nombre_municipio' => 'TACACOMA', 'cod_municipio' => 20603, 'status_municipio' => true],

            // Provincia Franz Tamayo (id_prov = 17)
            ['id' => 57, 'id_prov' => 17, 'nombre_municipio' => 'APOLO', 'cod_municipio' => 21701, 'status_municipio' => true],
            ['id' => 58, 'id_prov' => 17, 'nombre_municipio' => 'PELECHUCO', 'cod_municipio' => 21702, 'status_municipio' => true],


            ['id' => 59, 'id_prov' => 47, 'nombre_mun' => 'ORURO',             'cod_municipio' => 40001, 'status_mun' => true],
            ['id' => 60, 'id_prov' => 48, 'nombre_mun' => 'SALINAS DE GARCÍA MENDOZA', 'cod_municipio' => 40002, 'status_mun' => true],
            ['id' => 61, 'id_prov' => 49, 'nombre_mun' => 'CORQUE',            'cod_municipio' => 40003, 'status_mun' => true],
            ['id' => 62, 'id_prov' => 50, 'nombre_mun' => 'TURCO',             'cod_municipio' => 40004, 'status_mun' => true],
            ['id' => 63, 'id_prov' => 51, 'nombre_mun' => 'HUACHACALLA',       'cod_municipio' => 40005, 'status_mun' => true],
            ['id' => 64, 'id_prov' => 52, 'nombre_mun' => 'POOPO',             'cod_municipio' => 40006, 'status_mun' => true],
            ['id' => 65, 'id_prov' => 53, 'nombre_mun' => 'HUAJLLAMARKA',      'cod_municipio' => 40007, 'status_mun' => true],
            ['id' => 66, 'id_prov' => 54, 'nombre_mun' => 'SALINAS',           'cod_municipio' => 40008, 'status_mun' => true],
            ['id' => 67, 'id_prov' => 55, 'nombre_mun' => 'SABAYA',            'cod_municipio' => 40009, 'status_mun' => true],
            ['id' => 68, 'id_prov' => 56, 'nombre_mun' => 'TOTORA',            'cod_municipio' => 40010, 'status_mun' => true],
            ['id' => 69, 'id_prov' => 57, 'nombre_mun' => 'TOMAS BARRÓN',      'cod_municipio' => 40011, 'status_mun' => true],
            ['id' => 70, 'id_prov' => 58, 'nombre_mun' => 'SANTIAGO DE HUAYLLAMARCA', 'cod_municipio' => 40012, 'status_mun' => true],
            ['id' => 71, 'id_prov' => 59, 'nombre_mun' => 'SAN PEDRO DE TOTORA', 'cod_municipio' => 40013, 'status_mun' => true],
            ['id' => 72, 'id_prov' => 60, 'nombre_mun' => 'SANTIAGO DE HUARI', 'cod_municipio' => 40014, 'status_mun' => true],
            ['id' => 73, 'id_prov' => 61, 'nombre_mun' => 'LA RIVERA',         'cod_municipio' => 40015, 'status_mun' => true],
            ['id' => 74, 'id_prov' => 62, 'nombre_mun' => 'BELEN DE ANDAMARCA', 'cod_municipio' => 40016, 'status_mun' => true],


        ]);
    }
}
