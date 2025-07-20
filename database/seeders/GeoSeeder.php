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
            ['id' => 4, 'id_prov' => 2, 'nombre_municipio' => 'VILLA AZURDUY', 'cod_municipio' => 10201, 'status_municipio' => true],
            ['id' => 5, 'id_prov' => 2, 'nombre_municipio' => 'TARVITA', 'cod_municipio' => 10202, 'status_municipio' => true],
        ]);
    }
}
