<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\DetallePagoDocumento;
use App\Models\DocumentoInscripcion;
use App\Models\Inscripcion;
use App\Models\Pago;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Flysystem\Visibility;

class DocumentosController extends Controller
{
    public function subirDocumentosInscripcion(Request $request, $id)
    {
        $inscripcion = Inscripcion::findOrFail($id);
        $lab = $inscripcion->laboratorio;

        $validator = Validator::make($request->all(), [
            'titulos' => 'required|array',
            'documentos' => 'required|array',
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'titulos.required' => 'Debe seleccionar al menos un título.',
            'documentos.required' => 'Debe subir al menos un documento.',
            'documentos.*.mimes' => 'Solo se permiten archivos PDF, JPG, JPEG o PNG.',
            'documentos.*.max' => 'Cada archivo no debe superar los 5 MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('notice', [
                'title' => 'Errores de validación',
                'message' => implode('<br>', $validator->errors()->all()),
                'type' => 'error',
            ]);
        }
        $titulos = $request->input('titulos', []);
        $archivos = $request->file('documentos', []);

        foreach ($titulos as $index => $titulo) {
            if (isset($archivos[$index])) {
                $docExistente = $inscripcion->documentosInscripcion()
                    ->where('nombre_doc', $titulo)
                    ->first();
                if ($docExistente->ruta_doc && Storage::disk('public')->exists(str_replace('/storage/', '', $docExistente->ruta_doc))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $docExistente->ruta_doc));
                }
                $archivo = $archivos[$index];
                $extension = $archivo->getClientOriginalExtension();
                $nombreFormateado = Str::slug(pathinfo($titulo, PATHINFO_FILENAME)) . '.' . $extension;
                $folder = "{$lab->cod_lab}/{$inscripcion->gestion}/{$inscripcion->id}";
                $path = Storage::disk('public')->putFileAs(
                    $folder,
                    $archivo,
                    $nombreFormateado,
                    ['visibility' => Visibility::PUBLIC]
                );

                if ($docExistente) {
                    $docExistente->update([
                        'ruta_doc' => "/storage/{$path}",
                    ]);
                } else {
                    $documento = new DocumentoInscripcion();
                    $documento->nombre_doc = $titulo;
                    $documento->ruta_doc = "/storage/{$path}";
                    $documento->status = 1;
                    $documento->tipo = DocumentoInscripcion::TYPE_DOCUMENTO_INSCRIPCION;
                    $documento->id_inscripcion = $inscripcion->id;
                    $documento->save();
                }
            }
        }

        return redirect()->back()->with('success', 'Documentos subidos correctamente.');
    }

    public function subirComprobante(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comprobante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'nit' => 'required|numeric',
            'razon_social' => 'required|string|max:150',
        ], [
            'comprobante.required' => 'Debe subir al menos un documento.',
            'comprobante.mimes' => 'Solo se permiten archivos PDF, JPG, JPEG o PNG.',
            'comprobante.max' => 'El archivo no debe superar los 5 MB.',
            'nit.required' => 'El NIT es obligatorio.',
            'nit.numeric' => 'El NIT debe ser un número válido.',
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social no debe superar los 150 caracteres.',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('notice', [
                'title' => 'Errores de validación',
                'message' => implode('<br>', $validator->errors()->all()),
                'type' => 'error',
            ]);
        }
        $inscripcion = Inscripcion::findOrFail($id);
        $lab = $inscripcion->laboratorio;
        $archivo = $request->comprobante;
        $extension = $archivo->getClientOriginalExtension();
        $nombre = str::uuid() . '.' . $extension;
        $folder = "{$lab->cod_lab}/{$inscripcion->gestion}/{$inscripcion->id}/p";
        $path = Storage::disk('public')->putFileAs(
            $folder,
            $archivo,
            $nombre,
            ['visibility' => Visibility::PUBLIC]
        );
        $documento = new DocumentoInscripcion();
        $documento->ruta_doc = "/storage/{$path}";
        $documento->nombre_doc = DocumentoInscripcion::TIPOS[DocumentoInscripcion::TYPE_DOCUMENTO_PAGO];
        $documento->status = 1;
        $documento->id_inscripcion = $inscripcion->id;
        $documento->tipo = DocumentoInscripcion::TYPE_DOCUMENTO_PAGO;
        $documento->save();

        $detalle = new DetallePagoDocumento();
        $detalle->documento_inscripcion_id = $documento->id;
        $detalle->nit = $request->input('nit');
        $detalle->razon_social = $request->input('razon_social');
        $detalle->save();
        return back()->with('success', 'Comprobante subido correctamente.');
    }

    public function indexDocPagos($id)
    {
        if (
            !Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN, Permiso::LABORATORIO]) ||
            !configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS)
        ) {
            return redirect('/')->with('error', 'No tiene acceso a la gestión de pagos.');
        }

        if (Gate::allows(Permiso::LABORATORIO)) {
            $lab = Auth::user()->laboratorio;
            $inscripcion = $lab->inscripciones()
                ->with('documentosPago.detallePago')
                ->find($id);
        } else {
            $inscripcion = Inscripcion::with(['documentosPago', 'laboratorio'])->find($id);
        }
        if (!$inscripcion) {
            return redirect('/')->with('error', 'No se encontró el registro solicitado.');
        }
        $codigo = $inscripcion->laboratorio->cod_lab;
        $gestion = $inscripcion->gestion;
        $documentos = $inscripcion->documentosPago;
        return view('inscripcion_paquete.pagos.index', compact('documentos', 'gestion', 'codigo'));
    }
}
