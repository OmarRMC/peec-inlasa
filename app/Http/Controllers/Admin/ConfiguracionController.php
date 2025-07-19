<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

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
                ]);
                configuracion('fecha_inicio_inscripcion', $request->fecha_inicio_inscripcion);
                configuracion('fecha_fin_inscripcion', $request->fecha_fin_inscripcion);
                break;

            case 'periodo-pago':
                $request->validate([
                    'fecha_inicio_pago' => 'required|date',
                    'fecha_fin_pago' => 'required|date|after_or_equal:fecha_inicio_pago',
                ]);
                configuracion('fecha_inicio_pago', $request->fecha_inicio_pago);
                configuracion('fecha_fin_pago', $request->fecha_fin_pago);
                break;

            case 'periodo-vigencia':
                $request->validate([
                    'fecha_inicio_vigencia' => 'required|date',
                    'fecha_fin_vigencia' => 'required|date|after_or_equal:fecha_inicio_vigencia',
                ]);
                configuracion('fecha_inicio_vigencia', $request->fecha_inicio_vigencia);
                configuracion('fecha_fin_vigencia', $request->fecha_fin_vigencia);
                break;

            case 'gestion':
                $request->validate([
                    'gestion_actual' => 'required|digits:4',
                ]);
                configuracion('gestion_actual', $request->gestion_actual);
                break;

            case 'notificacion':
                $request->validate([
                    'notificacion.key' => 'required|string',
                    'notificacion.titulo' => 'required|string|max:100',
                    'notificacion.descripcion' => 'nullable|string|max:255',
                    'notificacion.mensaje' => 'nullable|string',
                ]);
                configuracion('notificacion.key', $request->notificacion['key']);
                configuracion('notificacion.titulo', $request->notificacion['titulo']);
                configuracion('notificacion.descripcion', $request->notificacion['descripcion']);
                configuracion('notificacion.mensaje', $request->notificacion['mensaje']);
                break;

            default:
                return redirect()->back()->with('error', 'Error en registrar la Configuración.');
        }

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
