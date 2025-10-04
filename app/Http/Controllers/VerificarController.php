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

    public function validarAntiguo(Request $request)
    {
        $codigo = $request->query('code');
        $certificado = Certificado::where('codigo_certificado', 'LIKE', "%$codigo%")->first();

        if (!$certificado) {
            return redirect()
                ->route('login')
                ->with('error', 'El código ingresado no corresponde a ningún certificado.');
        }
        $codigos = explode(',', $certificado->codigo_certificado);
        $participacion = $codigos[0] ?? null;
        $desempeño = $codigos[1] ?? null;
        $type = $participacion == $codigo ? Certificado::TYPE_PARTICIPACION : Certificado::TYPE_DESEMP;
        $gestion = $certificado->gestion_certificado;
        $laboratorio = $certificado->inscripcion->laboratorio;
        if ($type == Certificado::TYPE_DESEMP) {
            $data = $laboratorio->getDataCertificadoDesemp($gestion);
        }
        if ($type == Certificado::TYPE_PARTICIPACION) {
            $data = $laboratorio->getDataCertificadoParticipacion($gestion);
        }
        if (empty($data)) {
            return redirect()->route('login')->with('error', 'El código no corresponde a este certificado.');
        }
        return view('verificacion.certificado.lab', compact('data', 'laboratorio', 'gestion', 'type'));
    }
}
