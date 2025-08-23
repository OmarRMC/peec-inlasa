<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Visibility;

class ConfiguracionController extends Controller
{

    public function __construct()
    {
        $this->middleware('canany:' . Permiso::CONFIGURACION . ',' . Permiso::ADMIN)->only(['index']);
    }
    public function index()
    {
        $configuraciones = Configuracion::all()->pluck('valor', 'clave');

        return view('config.index', compact('configuraciones'));
    }

    public function update(Request $request, $seccion)
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN, Permiso::CONFIGURACION])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        switch ($seccion) {
            case 'periodo-inscripcion':
                $request->validate([
                    'fecha_inicio_inscripcion' => 'required|date',
                    'fecha_fin_inscripcion' => 'required|date|after_or_equal:fecha_inicio_inscripcion',
                ], [
                    'fecha_inicio_inscripcion.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_inscripcion.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_inscripcion.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_inscripcion.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_inscripcion.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);


                configuracion(Configuracion::FECHA_INICIO_INSCRIPCION, $request->fecha_inicio_inscripcion);
                configuracion(Configuracion::FECHA_FIN_INSCRIPCION, $request->fecha_fin_inscripcion);
                break;

            case 'periodo-pago':
                $request->validate([
                    'fecha_inicio_pago' => 'required|date',
                    'fecha_fin_pago' => 'required|date|after_or_equal:fecha_inicio_pago',
                ], [
                    'fecha_inicio_pago.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_pago.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_pago.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_pago.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_pago.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);
                configuracion(Configuracion::FECHA_INICIO_PAGO, $request->fecha_inicio_pago);
                configuracion(Configuracion::FECHA_FIN_PAGO, $request->fecha_fin_pago);
                break;

            case 'periodo-vigencia':
                $request->validate([
                    'fecha_inicio_vigencia' => 'required|date',
                    'fecha_fin_vigencia' => 'required|date|after_or_equal:fecha_inicio_vigencia',
                ], [
                    'fecha_inicio_vigencia.required' => 'La fecha de inicio es obligatoria.',
                    'fecha_inicio_vigencia.date' => 'La fecha de inicio debe ser una fecha válida.',
                    'fecha_fin_vigencia.required' => 'La fecha de fin es obligatoria.',
                    'fecha_fin_vigencia.date' => 'La fecha de fin debe ser una fecha válida.',
                    'fecha_fin_vigencia.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
                ]);

                configuracion(Configuracion::FECHA_INICIO_VIGENCIA, $request->fecha_inicio_vigencia);
                configuracion(Configuracion::FECHA_FIN_VIGENCIA, $request->fecha_fin_vigencia);
                break;

            case 'gestion':
                $request->validate([
                    'gestion_actual' => 'required|digits:4',
                ], [
                    'gestion_actual.required' => 'El campo gestión actual es obligatorio.',
                    'gestion_actual.digits' => 'La gestión debe contener exactamente 4 dígitos.',
                ]);

                configuracion(Configuracion::GESTION_ACTUAL, $request->gestion_actual);
                break;

            case 'notificacion':
                $request->validate([
                    'notificacion_key' => 'required|string',
                    'notificacion_titulo' => 'required|string|max:100',
                    'notificacion_descripcion' => 'nullable|string|max:255',
                    'notificacion_mensaje' => 'nullable|string',
                    'notificacion_fecha_inicio' => 'required|date',
                    'notificacion_fecha_fin' => 'required|date|after_or_equal:notificacion_fecha_inicio',
                ], [
                    'notificacion_key.required' => 'El campo "key" es obligatorio.',
                    'notificacion_key.string' => 'El campo "key" debe ser una cadena de texto.',
                    'notificacion_titulo.required' => 'El título es obligatorio.',
                    'notificacion_titulo.string' => 'El título debe ser una cadena de texto.',
                    'notificacion_titulo.max' => 'El título no debe exceder los 100 caracteres.',
                    'notificacion_descripcion.string' => 'La descripción debe ser una cadena de texto.',
                    'notificacion_descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
                    'notificacion_mensaje.string' => 'El mensaje debe ser una cadena de texto.',
                    'notificacion_fecha_fin.required' => 'La fecha de fin es obligatoria.',
                    'notificacion_fecha_fin.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio.',
                ]);


                configuracion(Configuracion::NOTIFICACION_KEY, $request->notificacion_key);
                configuracion(Configuracion::NOTIFICACION_TITULO, $request->notificacion_titulo);
                configuracion(Configuracion::NOTIFICACION_DESCRIPCION, $request->notificacion_descripcion);
                configuracion(Configuracion::NOTIFICACION_MENSAJE, $request->notificacion_mensaje);
                configuracion(Configuracion::NOTIFICACION_FECHA_INICIO, $request->notificacion_fecha_inicio);
                configuracion(Configuracion::NOTIFICACION_FECHA_FIN, $request->notificacion_fecha_fin);
                break;

            case 'email.informacion':
                $request->validate([
                    'email_informacion' => 'required|string',
                ], [
                    'email_informacion.string' => 'El mensaje debe ser una cadena de texto.',
                ]);
                configuracion(Configuracion::EMAIL_INFORMACION, $request->email_informacion);
                break;

            case 'certificados':
                $request->validate([
                    'evaluacion_externa' => 'required|array',
                    'evaluacion_externa.nombre' => 'required|string|max:200',
                    'evaluacion_externa.cargo' => 'required|string|max:200',
                    // 'evaluacion_externa.institucion' => 'required|string|max:200',
                    'evaluacion_externa.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'coordinadora_red' => 'required|array',
                    'coordinadora_red.nombre' => 'required|string|max:200',
                    'coordinadora_red.cargo' => 'required|string|max:200',
                    // 'coordinadora_red.institucion' => 'required|string|max:200',
                    'coordinadora_red.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'directora_general' => 'required|array',
                    'directora_general.nombre' => 'required|string|max:200',
                    'directora_general.cargo' => 'required|string|max:200',
                    // 'directora_general.institucion' => 'required|string|max:200',
                    'directora_general.imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ], [
                    'evaluacion_externa.required' => 'Los datos de Evaluación Externa son obligatorios.',
                    'evaluacion_externa.nombre.required' => 'El nombre de Evaluación Externa es obligatorio.',
                    'evaluacion_externa.cargo.required' => 'El cargo de Evaluación Externa es obligatorio.',
                    // 'evaluacion_externa.institucion.required' => 'La institución de Evaluación Externa es obligatoria.',
                    'evaluacion_externa.imagen.image' => 'La imagen de Evaluación Externa debe ser una imagen válida.',
                    'coordinadora_red.required' => 'Los datos de Coordinadora de Red son obligatorios.',
                    'coordinadora_red.nombre.required' => 'El nombre de Coordinadora de Red es obligatorio.',
                    'coordinadora_red.cargo.required' => 'El cargo de Coordinadora de Red es obligatorio.',
                    // 'coordinadora_red.institucion.required' => 'La institución de Coordinadora de Red es obligatoria.',
                    'coordinadora_red.imagen.image' => 'La imagen de Coordinadora de Red debe ser una imagen válida.',
                    'directora_general.required' => 'Los datos de Directora General son obligatorios.',
                    'directora_general.nombre.required' => 'El nombre de Directora General es obligatorio.',
                    'directora_general.cargo.required' => 'El cargo de Directora General es obligatorio.',
                    // 'directora_general.institucion.required' => 'La institución de Directora General es obligatoria.',
                    'directora_general.imagen.image' => 'La imagen de Directora General debe ser una imagen válida.',
                ]);

                $actuales = [
                    Configuracion::CARGO_EVALUACION_EXTERNA => configuracion(Configuracion::CARGO_EVALUACION_EXTERNA) ?? (object) [],
                    Configuracion::CARGO_COORDINADORA_RED  => configuracion(Configuracion::CARGO_COORDINADORA_RED) ?? (object) [],
                    Configuracion::CARGO_DIRECTORA_GENERAL => configuracion(Configuracion::CARGO_DIRECTORA_GENERAL) ?? (object) [],
                ];

                $nuevos = [
                    Configuracion::CARGO_EVALUACION_EXTERNA => $request->evaluacion_externa,
                    Configuracion::CARGO_COORDINADORA_RED  => $request->coordinadora_red,
                    Configuracion::CARGO_DIRECTORA_GENERAL => $request->directora_general,
                ];

                foreach ($nuevos as $clave => $datosNuevos) {
                    $datosActuales = $actuales[$clave] ?? (object) [];

                    if (isset($datosNuevos['imagen']) && $datosNuevos['imagen'] instanceof \Illuminate\Http\UploadedFile) {
                        $folder = str_replace('_', '-', strtolower($clave));
                        $extension = strtolower($datosNuevos['imagen']->getClientOriginalExtension());
                        $nombreLimpio = preg_replace('/[^A-Za-z0-9_\-]/', '_', $datosNuevos['nombre']);
                        $nombreArchivo = $nombreLimpio . '.' . $extension;

                        $path = Storage::disk('public')->putFileAs(
                            "firmas/{$folder}",
                            $datosNuevos['imagen'],
                            $nombreArchivo,
                            ['visibility' => Visibility::PUBLIC]
                        );

                        $datosNuevos['imagen'] = "/storage/{$path}";
                    } else {
                        $datosNuevos['imagen'] = $datosActuales->imagen ?? null;
                    }
                    $arrActuales = (array) $datosActuales;
                    if ($datosNuevos != $arrActuales) {
                        $datosFinales = array_merge($arrActuales, $datosNuevos);
                        configuracion($clave, $datosFinales);
                    }
                }
                break;
            case 'gestionCertificado':
                $request->validate([
                    'registro_ponderaciones_certificados_gestion' => 'required|digits:4',
                ], [
                    'registro_ponderaciones_certificados_gestion.required' => 'El campo gestión actual es obligatorio.',
                    'registro_ponderaciones_certificados_gestion.digits' => 'La gestión debe contener exactamente 4 dígitos.',
                ]);

                configuracion(Configuracion::REGISTRO_PONDERACIONES_CERTIFICADOS_GESTION, $request->registro_ponderaciones_certificados_gestion);
                break;

            default:
                return redirect()->back()->with('error', 'Error en registrar la Configuración.');
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function certificados()
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        $configuraciones = Configuracion::whereIn('key', [
            Configuracion::CARGO_DIRECTORA_GENERAL,
            Configuracion::CARGO_COORDINADORA_RED,
            Configuracion::CARGO_EVALUACION_EXTERNA
        ])
            ->pluck('valor', 'key')
            ->map(function ($valor, $clave) {
                $obj = json_decode($valor);
                $obj->clave = $clave;
                return $obj;
            })
            ->values();
        return view('certificados.config', compact('configuraciones'));
    }
}
