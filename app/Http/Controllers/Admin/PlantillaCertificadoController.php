<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlantillaCertificado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PlantillaCertificadoController extends Controller
{
    public function index()
    {
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
        $data['imagen_fondo'] = $this->resolveImagenFondo($request, null);
        $data['firmas'] = $this->resolveFirmas($request);
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
        $data['imagen_fondo'] = $this->resolveImagenFondo($request, $plantilla->imagen_fondo);
        $newFirmas = $this->resolveFirmas($request);
        $this->cleanupRemovedFirmas($plantilla->firmas ?? [], $newFirmas);
        $data['firmas'] = $newFirmas;

        $plantilla->update($data);

        return redirect()
            ->route('plantillas-certificados.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(PlantillaCertificado $plantillas_certificado)
    {
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
        $backgroundDataUri = $this->storageUrlToDataUri($plantilla->imagen_fondo);

        // Firmas con data-uri
        $firmas = collect($plantilla->firmas ?? [])
            ->map(function ($f) {
                $url = $f['firma'] ?? null;
                return [
                    'nombre' => $f['nombre'] ?? '',
                    'cargo'  => $f['cargo'] ?? '',
                    'firma'  => $url,
                    'firma_data_uri' => $this->storageUrlToDataUri($url),
                ];
            })
            ->values()
            ->all();

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
        $paper = $this->paperFromMm(
            (float) $plantilla->ancho_mm,
            (float) $plantilla->alto_mm
        );

        // Datos ejemplo (para ver cómo quedará)
        $sample = [
            'laboratorio_linea_1' => 'LABORATORIO HOSPITAL MILITAR N° 4 "COSSMIL"',
            'laboratorio_linea_2' => 'TRINIDAD CNL. (+) EDWIN CASPARI VARGAS',
            'area' => 'ANÁLISIS CLÍNICOS',
            'items' => [
                'HEMATOLOGÍA: EXCELENTE',
                'QUÍMICA SANGUÍNEA: EXCELENTE',
                'FROTIS BÁSICO: EXCELENTE',
                'GRUPO SANGUÍNEO: EXCELENTE',
                'INMUNOLOGÍA: EXCELENTE',
                'BACTERIOLOGÍA: EXCELENTE',
            ],
            'gestion' => now()->year,
        ];

        $pdf = Pdf::loadView('certificados.plantillas.preview', [
            'plantilla' => $plantilla,
            'background' => $backgroundDataUri,
            'firmas' => $firmas,
            'qr' => $qrDataUri,
            'sample' => $sample,
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

    private function paperFromMm(float $widthMm, float $heightMm): array
    {
        $w = $this->mmToPt($widthMm);
        $h = $this->mmToPt($heightMm);
        return [0, 0, $w, $h];
    }

    private function mmToPt(float $mm): float
    {
        return ($mm * 72) / 25.4;
    }

    /**
     * Convierte una URL tipo "/storage/xxx/yyy.png" a data-uri.
     */
    private function storageUrlToDataUri(?string $url): ?string
    {
        if (!$url) return null;

        if (!Str::startsWith($url, '/storage/')) {
            return null;
        }

        $relative = Str::after($url, '/storage/');
        $absolute = storage_path('app/public/' . $relative);

        if (!is_file($absolute)) return null;

        $mime = mime_content_type($absolute) ?: 'image/png';
        $content = file_get_contents($absolute);
        if ($content === false) return null;

        return "data:{$mime};base64," . base64_encode($content);
    }
}
