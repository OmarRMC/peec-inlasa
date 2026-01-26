<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificarController extends Controller
{
    public function verificarCertificado(Request $request)
    {
        $code = $request->code;
        $type = $request->type;
        $ins = Inscripcion::with('laboratorio')
            ->where('ulid', $code)
            ->first();
        if (!$ins) {
            return redirect()
                ->route('login')
                ->with('error', 'El código ingresado no corresponde a ningún certificado.');
        }
        $gestion = $ins->gestion;
        $laboratorio = $ins->laboratorio;
        $data = [];
        if ($type == Certificado::TYPE_DESEMP) {
            $data = $laboratorio->getDataCertificadoDesemp($gestion);
        }
        if ($type == Certificado::TYPE_PARTICIPACION) {
            $data = $laboratorio->getDataCertificadoParticipacion($gestion);
        }
        if (empty($data)) {
            return redirect()->route('login');
        }
        return view('verificacion.certificado.lab', compact('data', 'laboratorio', 'gestion', 'type'));
    }

    public function validarAntiguo(Request $request)
    {
        return redirect()->route('login')->with('error', 'El código no corresponde a este certificado.');
    }
}
