<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
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

                configuracion('fecha_inicio_inscripcion', $request->fecha_inicio_inscripcion);
                configuracion('fecha_fin_inscripcion', $request->fecha_fin_inscripcion);
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

                configuracion('fecha_inicio_pago', $request->fecha_inicio_pago);
                configuracion('fecha_fin_pago', $request->fecha_fin_pago);
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

                configuracion('fecha.inicio.vigencia', $request->fecha_inicio_vigencia);
                configuracion('fecha.fin.vigencia', $request->fecha_fin_vigencia);
                break;

            case 'gestion':
                $request->validate([
                    'gestion_actual' => 'required|digits:4',
                ], [
                    'gestion_actual.required' => 'El campo gestión actual es obligatorio.',
                    'gestion_actual.digits' => 'La gestión debe contener exactamente 4 dígitos.',
                ]);

                configuracion('gestion_actual', $request->gestion_actual);
                break;

            case 'notificacion':
                $request->validate([
                    'notificacion_key' => 'required|string',
                    'notificacion_titulo' => 'required|string|max:100',
                    'notificacion_descripcion' => 'nullable|string|max:255',
                    'notificacion_mensaje' => 'nullable|string',
                ], [
                    'notificacion_key.required' => 'El campo "key" es obligatorio.',
                    'notificacion_key.string' => 'El campo "key" debe ser una cadena de texto.',
                    'notificacion_titulo.required' => 'El título es obligatorio.',
                    'notificacion_titulo.string' => 'El título debe ser una cadena de texto.',
                    'notificacion_titulo.max' => 'El título no debe exceder los 100 caracteres.',
                    'notificacion_descripcion.string' => 'La descripción debe ser una cadena de texto.',
                    'notificacion_descripcion.max' => 'La descripción no debe exceder los 255 caracteres.',
                    'notificacion_mensaje.string' => 'El mensaje debe ser una cadena de texto.',
                ]);

                configuracion('notificacion.key', $request->notificacion_key);
                configuracion('notificacion.titulo', $request->notificacion_titulo);
                configuracion('notificacion.descripcion', $request->notificacion_descripcion);
                configuracion('notificacion.mensaje', $request->notificacion_mensaje);
                break;

            default:
                return redirect()->back()->with('error', 'Error en registrar la Configuración.');
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
