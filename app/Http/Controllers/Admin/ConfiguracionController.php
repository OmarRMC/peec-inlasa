<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{

    public function __construct()
    {
        $this->middleware('canany:' . Permiso::CONFIGURACION . ',' . Permiso::ADMIN);
    }
    public function index()
    {
        $configuraciones = Configuracion::all()->pluck('valor', 'clave');

        return view('config.index', compact('configuraciones'));
    }

    public function update(Request $request, $seccion)
    {
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
            default:
                return redirect()->back()->with('error', 'Error en registrar la Configuración.');
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
