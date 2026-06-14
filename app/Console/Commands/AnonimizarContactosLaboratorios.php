<?php

namespace App\Console\Commands;

use App\Models\Laboratorio;
use Exception;
use Illuminate\Console\Command;

class AnonimizarContactosLaboratorios extends Command
{
    /**
     * --dry-run : simula los cambios sin guardarlos.
     * --force   : omite el guard de producción y la confirmación interactiva.
     */
    protected $signature = 'laboratorios:anonimizar-contactos
                            {--dry-run : Simula los cambios sin guardarlos}
                            {--force : Omite el guard de producción y la confirmación interactiva}';

    protected $description = 'Reemplaza correos, teléfonos y nombres de laboratorios y sus usuarios por valores falsos deterministas (para entornos de prueba con copia de producción)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        // 1) Guard de entorno: evita anonimizar la base real por accidente.
        if (app()->environment('production') && !$force) {
            $this->error('Estás en entorno de PRODUCCIÓN. Este comando reemplaza datos de contacto reales.');
            $this->line('Si realmente quieres ejecutarlo aquí, vuelve a correrlo con --force.');
            return self::FAILURE;
        }

        $total = Laboratorio::count();
        $this->info('Anonimización de contactos de laboratorios' . ($dryRun ? ' (modo simulación)' : '') . '...');
        $this->info("Laboratorios encontrados: {$total}");

        // 2) Confirmación interactiva (salvo dry-run o --force).
        if (!$dryRun && !$force && !$this->confirm("Se reemplazarán correos, teléfonos y nombres de {$total} laboratorios y sus usuarios. ¿Continuar?")) {
            $this->warn('Operación cancelada.');
            return self::SUCCESS;
        }

        $procesados = 0;
        $labsActualizados = 0;
        $usuariosActualizados = 0;
        $sinUsuario = 0;
        $errores = 0;

        // 3) Desactivar auditoría globalmente durante la corrida para que la tabla
        //    `audits` no almacene los correos/teléfonos reales en old_values.
        Laboratorio::withoutAuditing(function () use (
            $dryRun,
            &$procesados,
            &$labsActualizados,
            &$usuariosActualizados,
            &$sinUsuario,
            &$errores
        ) {
            Laboratorio::with('usuario')->chunk(200, function ($labs) use (
                $dryRun,
                &$procesados,
                &$labsActualizados,
                &$usuariosActualizados,
                &$sinUsuario,
                &$errores
            ) {
                foreach ($labs as $lab) {
                    $procesados++;

                    // Valores falsos deterministas por id de laboratorio.
                    $sufijo = str_pad((string) $lab->id, 7, '0', STR_PAD_LEFT);
                    $mail = "lab{$lab->id}@example.test";
                    $mail2 = "lab{$lab->id}.2@example.test";
                    $wapp = "7{$sufijo}";
                    $wapp2 = "6{$sufijo}";
                    $nombre = "Laboratorio de prueba {$lab->id}";
                    $sigla = "LAB{$lab->id}";

                    try {
                        // Nombre e identificación del laboratorio: siempre.
                        $lab->nombre_lab = $nombre;
                        $lab->sigla_lab = $sigla;

                        // Laboratorio: principal siempre; secundarios solo si tenían valor.
                        $lab->mail_lab = $mail;
                        $lab->wapp_lab = $wapp;
                        if (!empty($lab->mail2_lab)) {
                            $lab->mail2_lab = $mail2;
                        }
                        if (!empty($lab->wapp2_lab)) {
                            $lab->wapp2_lab = $wapp2;
                        }

                        // Usuario asociado: email/telefono solo si tenían valor.
                        $usuario = $lab->usuario;
                        $tocaUsuario = false;
                        if ($usuario) {
                            if (!empty($usuario->email)) {
                                $usuario->email = $mail;
                                $tocaUsuario = true;
                            }
                            if (!empty($usuario->telefono)) {
                                $usuario->telefono = $wapp;
                                $tocaUsuario = true;
                            }
                        } else {
                            $sinUsuario++;
                        }

                        if ($dryRun) {
                            $this->line("DRY RUN - Lab #{$lab->id} ({$lab->cod_lab}): nombre_lab -> {$nombre}, sigla_lab -> {$sigla}, mail_lab -> {$mail}, wapp_lab -> {$wapp}"
                                . ($usuario ? " | usuario #{$usuario->id}: email -> {$usuario->email}, telefono -> {$usuario->telefono}" : ' | sin usuario'));
                            $labsActualizados++;
                            if ($tocaUsuario) {
                                $usuariosActualizados++;
                            }
                            continue;
                        }

                        $lab->saveQuietly();
                        $labsActualizados++;

                        if ($tocaUsuario) {
                            $usuario->saveQuietly();
                            $usuariosActualizados++;
                        }
                    } catch (Exception $e) {
                        $this->error("Error en lab #{$lab->id}: " . $e->getMessage());
                        $errores++;
                    }
                }
            });
        }, true);

        $this->line('--------------------------------');
        $this->info("Procesados: {$procesados}");
        $this->info("Laboratorios actualizados: {$labsActualizados}");
        $this->info("Usuarios actualizados: {$usuariosActualizados}");
        $this->info("Laboratorios sin usuario: {$sinUsuario}");
        $this->info("Errores: {$errores}");
        $this->info('Proceso de anonimización completado' . ($dryRun ? ' (sin cambios guardados).' : '.'));

        return self::SUCCESS;
    }
}
