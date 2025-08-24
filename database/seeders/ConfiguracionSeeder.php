<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuracion;
use Illuminate\Support\Carbon;

class ConfiguracionSeeder extends Seeder
{
  public function run(): void
  {
    $now = now();
    $datos = [
      Configuracion::FECHA_INICIO_INSCRIPCION => $now->copy()->format('Y-m-d'),
      Configuracion::FECHA_FIN_INSCRIPCION    => $now->copy()->addDays(15)->format('Y-m-d'),

      Configuracion::FECHA_INICIO_PAGO        => $now->copy()->addDays(1)->format('Y-m-d'),
      Configuracion::FECHA_FIN_PAGO           => $now->copy()->addDays(30)->format('Y-m-d'),

      Configuracion::FECHA_INICIO_VIGENCIA    => $now->copy()->addMonth()->format('Y-m-d'),
      Configuracion::FECHA_FIN_VIGENCIA       => $now->copy()->addMonths(12)->format('Y-m-d'),

      Configuracion::GESTION_ACTUAL           => $now->year,
      Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION => $now->year,

      Configuracion::NOTIFICACION_KEY         => 'info-general',
      Configuracion::NOTIFICACION_TITULO      => 'Bienvenido al sistema SIGPEEC',
      Configuracion::NOTIFICACION_DESCRIPCION => 'Notificación visible al ingresar al sistema',
      Configuracion::NOTIFICACION_MENSAJE     => '
      <div style="display:flex; align-items:center; gap:15px; font-family: Arial, sans-serif;">
        <img src="https://picsum.photos/800/600" alt="Notificación" style="width:80px; height:80px; border-radius:8px; object-fit:cover;">
          <div>
            <h3 style="margin:0 0 8px; font-size:1.2rem; color:#333;">
              <i class="fas fa-check-circle" style="color: #4caf50; margin-right:8px;"></i>
              ¡Actualización Disponible!
            </h3>
            <p style="margin:0; color:#555; font-size:0.95rem;">
              Tu sistema ha sido actualizado exitosamente. Ahora puedes disfrutar de nuevas funciones y mejoras de rendimiento.
            </p>
          </div>
      </div>',
      Configuracion::NOTIFICACION_FECHA_INICIO => $now->copy()->format('Y-m-d'),
      Configuracion::NOTIFICACION_FECHA_FIN    => $now->copy()->addMonths(6)->format('Y-m-d'),
      Configuracion::EMAIL_INFORMACION => "<p style='background-color: yellow; color: black; padding: 10px; border-radius: 5px;'>Modificable desde la configuración</p>",
      Configuracion::FECHA_INICIO_REGISTRO_CERTIFICADOS => $now->copy()->format('Y-m-d'),
      Configuracion::FECHA_FIN_REGISTRO_CERTIFICADOS =>  $now->copy()->addMonths(4)->format('Y-m-d'),
      Configuracion::CARGO_EVALUACION_EXTERNA => [
        'nombre' => 'Lic. Jhaneline Lizeth Mamani Poma',
        'cargo' => 'JEFE PROGRAMA DE EVALUACIÓN EXTERNA DE LA CALIDAD',
        'institucion' => 'INLASA',
        'imagen' => '/storage/firmas/firma1.png',
        'label' => 'Jefe PEEC',
      ],
      Configuracion::CARGO_COORDINADORA_RED  => [
        'nombre'      => 'Dra. Shirley Aramayo Wayar',
        'cargo'       => 'COORDINADORA DIVISIÓN RED DE LABORATORIOS DE SALUD PÚBLICA',
        'institucion' => 'INLASA',
        'imagen'      => '/storage/firmas/firma2.png',
        'label' => 'Coord. Red Lab. Salud Pública',
      ],
      Configuracion::CARGO_DIRECTORA_GENERAL => [
        'nombre'      => 'Dra. Evelin Esther Fortún Fernández',
        'cargo'       => 'DIRECTORA GENERAL EJECUTIVA',
        'institucion' => 'INLASA',
        'imagen'      => '/storage/firmas/firma3.png',
        'label' => 'Dir. General',
      ]
    ];

    foreach ($datos as $key => $valor) {
      Configuracion::updateOrCreate(
        ['key' => $key],
        ['valor' => is_array($valor) ? json_encode($valor, JSON_UNESCAPED_UNICODE) : (string) $valor]
      );
    }
  }
}
