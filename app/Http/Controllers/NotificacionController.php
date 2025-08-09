<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function getNotificacion()
    {
        $titulo = Configuracion::where('key', Configuracion::NOTIFICACION_TITULO)->value('valor');
        $descripcion = Configuracion::where('key', Configuracion::NOTIFICACION_DESCRIPCION)->value('valor');
        $mensaje = Configuracion::where('key', Configuracion::NOTIFICACION_MENSAJE)->value('valor');
        $clave = Configuracion::where('key', Configuracion::NOTIFICACION_KEY)->value('valor');
        $user = Auth::user();
        $lab = $user->laboratorio;
        if ($lab && $lab->tieneNotificacionPendiente()) {
            return response()->json([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'mensaje' => $mensaje,
                'clave' => $clave
            ]);
        }

        return response()->json(null, 204);
    }

    public function marcarLeido(Request $request)
    {
        $user = Auth::user();
        $clave = $request->clave;
        $lab = $user->laboratorio;
        if ($lab) {
            $lab->notificacion_read_at = now();
            $lab->notificacion_key = $clave;
            $lab->save();
        }
        return response()->json(['ok' => true]);
    }
}
