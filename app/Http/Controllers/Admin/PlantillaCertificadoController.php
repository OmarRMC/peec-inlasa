<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificado;
use App\Models\Permiso;
use App\Models\PlantillaCertificado;
use App\Utils\StorageHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlantillaCertificadoController extends Controller
{
    public function index()
    {
        if (!Gate::any([Permiso::GESTION_CERTIFICADOS, Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }
        $plantillas = PlantillaCertificado::query()
            ->orderByDesc('id')
            ->paginate(10);

        return view('certificados.plantillas.index', compact('plantillas'));
    }

    public function create()
    {
        $plantilla = new PlantillaCertificado();
        return view('certificados.plantillas.form', compact('plantilla'));
    }
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['diseno'] = $this->decodeDiseno($data['diseno'] ?? null);
        $data['descripcion'] = $this->decodeDescripcion($request);
        $data['imagen_fondo'] = $this->resolveImagenFondo($request, null);
        $data['firmas'] = $this->resolveFirmas($request);
        if ((int) ($data['activo'] ?? 0) === PlantillaCertificado::SELECCIONADO) {
            PlantillaCertificado::where('activo', PlantillaCertificado::SELECCIONADO)
                ->update(['activo' => PlantillaCertificado::NO_SELECCIONADO]);
        }
        PlantillaCertificado::create($data);
        return redirect()
            ->route('plantillas-certificados.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    public function edit(PlantillaCertificado $plantillas_certificado)
    {
        $plantilla = $plantillas_certificado;
        return view('certificados.plantillas.form', compact('plantilla'));
    }

    public function update(Request $request, PlantillaCertificado $plantillas_certificado)
    {
        $plantilla = $plantillas_certificado;

        $data = $this->validatePayload($request, $plantilla->id);

        $data['diseno'] = $this->decodeDiseno($data['diseno'] ?? null);
        $data['descripcion'] = $this->decodeDescripcion($request);
        $data['imagen_fondo'] = $this->resolveImagenFondo($request, $plantilla->imagen_fondo);
        $newFirmas = $this->resolveFirmas($request);
        $this->cleanupRemovedFirmas($plantilla->firmas ?? [], $newFirmas);
        $data['firmas'] = $newFirmas;

        if ((int) ($data['activo'] ?? 0) === PlantillaCertificado::SELECCIONADO) {
            PlantillaCertificado::where('activo', PlantillaCertificado::SELECCIONADO)
                ->where('id', '!=', $plantilla->id)
                ->update(['activo' => PlantillaCertificado::NO_SELECCIONADO]);
        }

        $plantilla->update($data);

        return redirect()
            ->route('plantillas-certificados.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(PlantillaCertificado $plantillas_certificado)
    {
        if ($plantillas_certificado->certificados()->exists()) {
            return redirect()
                ->route('plantillas-certificados.index')
                ->with('error', 'No se puede eliminar la plantilla porque tiene certificados asociados.');
        }

        $this->deleteStorageUrlIfLocal($plantillas_certificado->imagen_fondo);
        foreach (($plantillas_certificado->firmas ?? []) as $f) {
            $this->deleteStorageUrlIfLocal($f['firma'] ?? null);
        }
        $plantillas_certificado->delete();
        return redirect()
            ->route('plantillas-certificados.index')
            ->with('success', 'Plantilla eliminada correctamente.');
    }

    public function preview(PlantillaCertificado $plantilla)
    {
        // Fondo como data-uri para que DomPDF lo renderice sin isRemoteEnabled
        $backgroundDataUri = StorageHelper::storageUrlToDataUri($plantilla->imagen_fondo);

        // Firmas con data-uri
        $firmas = $plantilla->getFirmas();
        // QR dummy para preview
        $code = (string) $plantilla->id;
        $verifyUrl = route('verificar.certificado', [
            'code' => $code,
            'type' => 'PLANTILLA_PREVIEW',
        ]);

        $qrBase64 = base64_encode(
            QrCode::format('png')->size(320)->margin(1)->generate($verifyUrl)
        );
        $qrDataUri = "data:image/png;base64,{$qrBase64}";

        // Papel basado en mm (custom paper)
        $paper = $plantilla->paperFromMm();

        // Datos ejemplo (para ver cómo quedará)
        $nombreLaboratorio = 'LABORATORIO HOSPITAL MILITAR N° 4 "COSSMIL" TRINIDAD CNL. (+) EDWIN CASPARI VARGAS';
        $ensayosA = '';
        $areas = [
            'ANÁLISIS CLÍNICOS' => [
                "detalles" => [
                    ["ensayo" => "HEMATOLOGÍA", "ponderacion" => "EXCELENTE"],
                    ["ensayo" => "QUÍMICA SANGUÍNEA", "ponderacion" => "SATISFACTORIO"],
                ]
            ]
        ];
        $gestion  =  now()->year;

        // Descripción desde la plantilla (descripcion_desmp)
        $descripcion = $plantilla->descripcion_desmp;
        // Extraer configuración de diseño (QR, nota, elements, etc.)
        $diseno = $plantilla->diseno ?? [];
        $qrConfig = $diseno['qr'] ?? [];
        $notaConfig = $diseno['nota'] ?? [];

        // Procesar elements y convertir imágenes a data-uri (local o externa)
        $elements = $plantilla->getElementos();

        $pdf = Pdf::loadView('certificados.plantillas.preview', [
            'type' => Certificado::TYPE_DESEMP,
            'plantilla' => $plantilla,
            'background' => $backgroundDataUri,
            'firmas' => $firmas,
            'qr' => $qrDataUri,
            'qrConfig' => $qrConfig,
            'notaConfig' => $notaConfig,
            'elements' => $elements,

            'nombreLaboratorio' => $nombreLaboratorio,
            'gestion' => $gestion,
            'areas' => $areas,
            'ensayosA' =>  $ensayosA,

            'descripcion' => $descripcion,
            'diseno' => $diseno,
        ])->setPaper($paper);

        $pdf->getDomPDF()->getOptions()->set('isHtml5ParserEnabled', true);

        return $pdf->stream('plantilla-preview.pdf');
    }

    private function validatePayload(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'activo' => ['required', 'in:0,1'],
            'descripcion' => ['nullable', 'string'],

            'ancho_mm' => ['required', 'numeric', 'min:1'],
            'alto_mm' => ['required', 'numeric', 'min:1'],

            // JSON como texto (textarea)
            'diseno' => ['nullable', 'string'],

            // fondo
            'imagen_fondo_file' => ['nullable', 'image', 'max:5120'],

            // firmas
            'firmas' => ['nullable', 'array'],
            'firmas.*.nombre' => ['required_with:firmas', 'string', 'max:150'],
            'firmas.*.cargo' => ['nullable', 'string', 'max:150'],
            'firmas.*.firma' => ['nullable', 'string', 'max:2048'],
            'firmas.*.firma_file' => ['nullable', 'image', 'max:5120'],
        ]);
    }


    private function decodeDescripcion(Request $request): array
    {
        $rawDesmp = $request->input('descripcion_desmp');
        $rawPart  = $request->input('descripcion_part');
        $decodeObjectJson = function ($raw): array {
            if ($raw === null) return [];
            if (is_array($raw)) return $raw;

            $raw = trim((string) $raw);
            if ($raw === '') return [];
            $decoded = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                return [];
            }
            $isList = array_keys($decoded) === range(0, count($decoded) - 1);
            if ($isList) return [];

            return $decoded;
        };

        $desmp = $decodeObjectJson($rawDesmp);
        $part  = $decodeObjectJson($rawPart);

        return [
            'descripcion_desmp' => $desmp,
            'descripcion_part' => $part,
        ];
    }

    private function decodeDiseno($value): array
    {
        if (is_array($value)) return $value;

        $raw = trim((string) $value);
        if ($raw === '') return [];

        $decoded = json_decode($raw, true);

        // if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
        //     throw ValidationException::withMessages([
        //         'diseno' => 'El campo diseño debe ser un JSON válido.',
        //     ]);
        // }

        return $decoded;
    }

    private function resolveFirmas(Request $request): array
    {
        $firmas = $request->input('firmas', []);
        if (!is_array($firmas)) return [];

        $out = [];

        foreach ($firmas as $i => $firma) {
            $nombre = $firma['nombre'] ?? '';
            $cargo = $firma['cargo'] ?? '';
            $currentUrl = $firma['firma'] ?? null;

            if ($request->hasFile("firmas.$i.firma_file")) {
                $file = $request->file("firmas.$i.firma_file");
                if ($file instanceof UploadedFile && $file->isValid()) {
                    $currentUrl = $this->storeFirmaFile($file, $currentUrl);
                }
            }

            $out[] = [
                'nombre' => $nombre,
                'cargo' => $cargo,
                'firma' => $currentUrl,
            ];
        }

        return $out;
    }

    private function storeFirmaFile(UploadedFile $file, ?string $currentUrl = null): string
    {
        $this->deleteStorageUrlIfLocal($currentUrl);
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $fileName =  Str::ulid() . '.' . $extension;
        $path = $file->storeAs('certificados/plantillas/firmas', $fileName, 'public');

        return "/storage/{$path}";
    }

    private function resolveImagenFondo(Request $request, ?string $currentUrl): ?string
    {
        if (!$request->hasFile('imagen_fondo_file')) {
            return $currentUrl;
        }

        $file = $request->file('imagen_fondo_file');
        if (!($file instanceof UploadedFile) || !$file->isValid()) {
            return $currentUrl;
        }
        $this->deleteStorageUrlIfLocal($currentUrl);

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $fileName = Str::ulid() . '.' . $extension;
        $path = $file->storeAs('certificados/plantillas/fondos', $fileName, 'public');

        return "/storage/{$path}";
    }

    /**
     * Elimina archivos de firmas que estaban antes pero ya no están referenciados en el nuevo array.
     * (Evita basura en storage cuando el usuario elimina firmantes.)
     */
    private function cleanupRemovedFirmas(array $oldFirmas, array $newFirmas): void
    {
        $oldUrls = collect($oldFirmas)->map(fn($f) => $f['firma'] ?? null)->filter()->values();
        $newUrls = collect($newFirmas)->map(fn($f) => $f['firma'] ?? null)->filter()->values();

        $toDelete = $oldUrls->diff($newUrls);

        foreach ($toDelete as $url) {
            $this->deleteStorageUrlIfLocal($url);
        }
    }

    private function deleteStorageUrlIfLocal(?string $url): void
    {
        if (!$url) return;

        if (!Str::startsWith($url, '/storage/')) {
            return;
        }

        $relative = Str::after($url, '/storage/');
        Storage::disk('public')->delete($relative);
    }
}
