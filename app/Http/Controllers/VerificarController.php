<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Inscripcion;
use Illuminate\Http\Request;

class VerificarController extends Controller
{
    public function verificarCertificado(Request $request)
    {
        $code = $request->code;
        $type = $request->type;
        $ins = Inscripcion::with('laboratorio')->findOrFail($code);
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
}
