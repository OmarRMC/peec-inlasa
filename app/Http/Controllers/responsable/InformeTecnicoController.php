<?php

namespace App\Http\Controllers\responsable;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\Ciclo;
use App\Models\EnsayoAptitud;
use App\Models\InformeTecnico;
use App\Models\Inscripcion;
use App\Models\InscripcionEA;
use App\Models\Permiso;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Normalizer;
use ZipArchive;

class InformeTecnicoController extends Controller
{
    public function informeTecnicoEnsayos()
    {
        $ensayos = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayos = EnsayoAptitud::active()->with('paquete')->get();
        }
        if (Gate::allows(Permiso::RESPONSABLE)) {
            $resposable  = Auth::user()->load(['responsablesEA', 'responsablesEA.paquete']);
            $ensayos = $resposable->responsablesEA;
        }
        if (!$ensayos) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        return view('informe_tecnico.ensayos.index', compact('ensayos'));
    }

    public function informeTecnicoEnsayo(Request $request, $id)
    {
        // if (!Gate::any([Permiso::JEFE_PEEC])) {
        //     return redirect('/')->with('error', 'El periodo para la carga de desempeño no está habilitado actualmente.');
        // }
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($id);
        }
        if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->findOrFail($id);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $paquete = $ensayosAptitud->paquete;
        $idEA = $ensayosAptitud->id;

        $eaDesc = mb_strtolower(
            trim(Normalizer::normalize($ensayosAptitud->descripcion, Normalizer::FORM_C)),
            'UTF-8',
        );
        $paqueteDesc = mb_strtolower(
            trim(Normalizer::normalize($paquete->descripcion, Normalizer::FORM_C)),
            'UTF-8',
        );
        $descripcion = $ensayosAptitud->descripcion;
        if ($eaDesc != $paqueteDesc) {
            $descripcion = "</br>  $paquete->descripcion / $ensayosAptitud->descripcion";
        }
        $gestion = $request->query('gestion', now()->year);
        $selectedCiclo = $request->query('ciclo', null);
        $gestiones = Inscripcion::rangoGestion([
            'status_inscripcion' => [Inscripcion::STATUS_APROBADO, Inscripcion::STATUS_VENCIDO],
        ]);
        $ciclos = $ensayosAptitud->ciclos()->gestionParaInforme($gestion)->activo()->get();
        if ($ciclos->isEmpty()) {
            return redirect()->back()->with('error', 'No existen ciclos activos para el ensayo de aptitud en la gestión seleccionada.');
        }
        $selectedCiclo = $ciclos->where('id', $selectedCiclo)->first()?->id;
        if (!$selectedCiclo) {
            $selectedCiclo = $ciclos->first()?->id;
        }

        return view('informe_tecnico.ensayos.show', compact('ensayosAptitud', 'idEA', 'gestion', 'descripcion', 'gestiones', 'ciclos', 'selectedCiclo'));
    }

    public function cargarInformeTecnico(Request $request)
    {
        $request->validate([
            'archivo' => 'required|mimes:zip|max:102400',
            'id_ciclo' => 'required|integer|exists:ciclos,id',
            'id_ensayo' => 'required|integer|exists:ensayo_aptitud,id',
        ]);

        $zipPath = $request->file('archivo')->getRealPath();
        $idEnsayo = $request->input('id_ensayo');
        $idCiclo = $request->input('id_ciclo');
        $gestion = $request->query('gestion', now()->year);

        $user = Auth::user();
        $ensayo = EnsayoAptitud::find($idEnsayo);
        if (!$ensayo) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }

        $ciclo = $ensayo->ciclos()
            ->activo()
            ->gestionParaInforme($gestion)
            ->where('id', $idCiclo)
            ->first();

        if (!$ciclo) {
            return redirect('/')->with('error', 'El ciclo no pertenece a este ensayo o no es válido para la gestión.');
        }

        $inscripciones = InscripcionEA::with(['inscripcion', 'inscripcion.laboratorio'])
            ->where('id_ea', $idEnsayo)
            ->whereHas('inscripcion', function ($q) use ($gestion) {
                $q->where('gestion', $gestion)->aprobadoOrVencido();
            })
            ->get();
        \Log::info($inscripciones);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            return back()->with('error', 'No se pudo abrir el archivo ZIP.');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $archivo = $zip->statIndex($i);
            $nombreArchivo = $archivo['name'];

            if (!str_ends_with(strtolower($nombreArchivo), '.pdf')) {
                continue;
            }

            $nombre = basename($nombreArchivo);
            $nombreSinExtension = pathinfo($nombre, PATHINFO_FILENAME);
            $codigo = explode(' ', $nombreSinExtension)[0];

            $inscripcionEA = $inscripciones->firstWhere('inscripcion.laboratorio.cod_lab', $codigo);
            if (!$inscripcionEA) {
                \Log::warning("No se encontró inscripción para el código: $codigo");
                continue;
            }
            $pathDestino = "informes_tecnicos/{$gestion}/{$idEnsayo}/{$idCiclo}";
            Storage::disk('public')->makeDirectory($pathDestino);
            $contenido = $zip->getFromIndex($i);
            $informe = InformeTecnico::where('id_ciclo', $idCiclo)
                ->where('id_laboratorio', $inscripcionEA->inscripcion->laboratorio->id)
                ->where('gestion', $gestion)
                ->first();
            $ulid = $informe ? $informe->ulid : (string) Str::ulid();
            $rutaArchivo = "{$pathDestino}/{$ulid}.pdf";
            if (!$informe) {
                $informe = new InformeTecnico();
                $informe->ulid = $ulid;
                $informe->id_ciclo = $idCiclo;
                $informe->id_laboratorio = $inscripcionEA->inscripcion->laboratorio->id;
                $informe->gestion = $gestion;
                $informe->reporte = $rutaArchivo;
                $informe->estado = InformeTecnico::ESTADO_SUBIDO;
                $informe->created_by = $user->id;
                $informe->updated_by = $user->id;
                $informe->save();
            } else {
                $informe->reporte = $rutaArchivo;
                $informe->estado = InformeTecnico::ESTADO_SUBIDO;
                $informe->updated_by = $user->id;
                $informe->updated_at = now();
                $informe->save();
            }
            Storage::disk('public')->put($rutaArchivo, $contenido);
            \Log::info("Archivo guardado y registrado: {$rutaArchivo}");
        }

        $zip->close();

        return back()->with('success', 'Archivos procesados y registrados correctamente.');
    }

    public function listadoInformes(Request $request, $idCiclo)
    {
        $ciclo = Ciclo::find($idCiclo);
        if (!$ciclo) {
            return redirect('/')->with('error', 'Ciclo no encontrado.');
        }
        $idEa = $ciclo->id_ensayo;
        $responsable = Auth::user();
        $ensayosAptitud = null;
        if (Gate::any([Permiso::JEFE_PEEC])) {
            $ensayosAptitud = EnsayoAptitud::find($idEa);
        } else if (Gate::allows(Permiso::RESPONSABLE)) {
            $ensayosAptitud = $responsable->responsablesEA->find($idEa);
        }
        if (!$ensayosAptitud) {
            return redirect('/')->with('error', 'No se tiene el ensayo de aptitud solicitado.');
        }
        $informes = $ciclo->informes()->with('laboratorio')->get();
        return datatables()
            ->of($informes)
            ->addColumn('created_at', fn($informe) => $informe->fecha_registro ?? '-')
            ->addColumn('updated_at', fn($informe) => $informe->fecha_actualizacion ?? '-')
            ->addColumn('nombre_lab', fn($informe) => $informe->laboratorio->nombre_lab)
            ->addColumn('cod_lab', fn($informe) => $informe->laboratorio->cod_lab)
            ->addColumn('informe', fn($informe) => $informe->link_reporte_html)
            ->addColumn('acciones', fn($i) => $i->btn_eliminar_html)
            ->rawColumns(['informe', 'acciones'])
            ->toJson();
    }

    public function eliminarInforme(Request $request, $id)
    {
        $informe = InformeTecnico::with('laboratorio', 'ciclo')->find($id);

        if (!$informe) {
            return response()->json([
                'message' => 'Informe técnico no encontrado.'
            ], 404);
        }

        if (!Gate::any([Permiso::JEFE_PEEC, Permiso::RESPONSABLE])) {
            return response()->json([
                'message' => 'No autorizado.'
            ], 403);
        }

        if ($informe->reporte && Storage::disk('public')->exists($informe->reporte)) {
            Storage::disk('public')->delete($informe->reporte);
        }
        $informe->delete();
        return response()->json([
            'message' => 'Informe técnico eliminado correctamente.'
        ]);
    }
}
