<?php

namespace App\Models;

use App\Utils\StorageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PlantillaCertificado extends Model
{
    use HasFactory;

    const SELECCIONADO = 1;
    const NO_SELECCIONADO = 0;

    const ESTADOS = [
        self::SELECCIONADO => 'Seleccionado',
        self::NO_SELECCIONADO => 'No Seleccionado',
    ];

    protected $table = 'plantillas_certificados';

    protected $fillable = [
        'nombre',
        'descripcion',
        'imagen_fondo',
        'ancho_mm',
        'alto_mm',
        'diseno',
        'firmas',
        'activo',
    ];

    protected $casts = [
        'diseno' => 'array',
        'firmas' => 'array',
        'descripcion' => 'array',
        'activo' => 'boolean',
        'ancho_mm' => 'decimal:2',
        'alto_mm' => 'decimal:2',
    ];

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'plantilla_certificado_id');
    }

    protected function descripcionDesmp(): Attribute
    {
        return Attribute::get(fn() => data_get($this->descripcion, 'descripcion_desmp', ''));
    }

    protected function descripcionDesmpText(): Attribute
    {
        return Attribute::get(fn() => (string) data_get($this->descripcion, 'descripcion_desmp.text', ''));
    }

    protected function descripcionDesmpStyle(): Attribute
    {
        return Attribute::get(fn() => (array) data_get($this->descripcion, 'descripcion_desmp.style', []));
    }

    /**
     * Convierte un array/objeto de estilos a CSS inline string
     *
     * @param array|object|null $styles Array u objeto con propiedades CSS
     * @return string CSS inline compatible (ej: "font-size: 15pt; color: #000;")
     *
     * Ejemplo:
     *   PlantillaCertificado::toInlineCss(['font-size' => '15pt', 'color' => '#000'])
     *   // Retorna: "font-size: 15pt; color: #000;"
     */
    public static function toInlineCss(array|object|null $styles): string
    {
        if (empty($styles)) {
            return '';
        }

        $stylesArray = is_object($styles) ? (array) $styles : $styles;

        return collect($stylesArray)
            ->filter(fn($value) => $value !== null && $value !== '')
            ->map(fn($value, $key) => "{$key}: {$value}")
            ->implode('; ') . ';';
    }

    /**
     * Convierte el array de estilos a CSS inline string
     * Ejemplo: ['font-size' => '15pt', 'color' => '#000'] => "font-size: 15pt; color: #000;"
     */
    protected function descripcionDesmpCss(): Attribute
    {
        return Attribute::get(fn() => self::toInlineCss($this->descripcion_desmp_style));
    }

    protected function descripcionPart(): Attribute
    {
        return Attribute::get(fn() => data_get($this->descripcion, 'descripcion_part', ''));
    }

    protected function descripcionPartText(): Attribute
    {
        return Attribute::get(fn() => (string) data_get($this->descripcion, 'descripcion_part.text', ''));
    }

    protected function descripcionPartStyle(): Attribute
    {
        return Attribute::get(fn() => (array) data_get($this->descripcion, 'descripcion_part.style', []));
    }

    /**
     * Convierte el array de estilos de descripcion_part a CSS inline string
     */
    protected function descripcionPartCss(): Attribute
    {
        return Attribute::get(fn() => self::toInlineCss($this->descripcion_part_style));
    }

    public function getFirmas()
    {
        $firmas = collect($this->firmas ?? [])
            ->map(function ($f) {
                $url = $f['firma'] ?? null;
                return [
                    'nombre' => $f['nombre'] ?? '',
                    'cargo'  => $f['cargo'] ?? '',
                    'firma'  => $url,
                    'firma_data_uri' => StorageHelper::storageUrlToDataUri($url),
                ];
            })
            ->values()
            ->all();
        return $firmas;
    }

    public function paperFromMm(): array
    {
        $w = $this->mmToPt((float) $this->ancho_mm);
        $h = $this->mmToPt((float) $this->alto_mm);
        return [0, 0, $w, $h];
    }

    private function mmToPt(float $mm): float
    {
        return ($mm * 72) / 25.4;
    }

    public function getElementos()
    {
        $diseno = $this->diseno ?? [];
        return  collect($diseno['elements'] ?? [])->map(function ($element) {
            if (($element['type'] ?? '') === 'image' && !empty($element['src'])) {
                $element['src_data_uri'] = $this->urlToDataUri($element['src']);
            }
            return $element;
        })->all();
    }
    /**
     * Convierte cualquier URL (local o externa) a data-uri para PDF.
     * Soporta: /storage/..., http://..., https://...
     */
    private function urlToDataUri(?string $url): ?string
    {
        if (!$url) return null;

        // URL local de storage
        if (Str::startsWith($url, '/storage/')) {
            return StorageHelper::storageUrlToDataUri($url);
        }

        // URL externa (http/https)
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $this->externalUrlToDataUri($url);
        }

        return null;
    }
    /**
     * Descarga una imagen desde URL externa y la convierte a data-uri.
     */
    private function externalUrlToDataUri(string $url): ?string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $content = @file_get_contents($url, false, $context);
            if ($content === false) {
                Log::warning("No se pudo descargar imagen externa: {$url}");
                return null;
            }

            // Detectar tipo MIME desde headers o contenido
            $mime = 'image/png'; // default
            if (isset($http_response_header)) {
                foreach ($http_response_header as $header) {
                    if (stripos($header, 'Content-Type:') === 0) {
                        $mime = trim(str_ireplace('Content-Type:', '', $header));
                        break;
                    }
                }
            }

            // Si no se detectó, intentar por extensión
            if ($mime === 'image/png') {
                $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                $mimeMap = [
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'webp' => 'image/webp',
                    'svg' => 'image/svg+xml',
                ];
                $mime = $mimeMap[$ext] ?? 'image/png';
            }

            return "data:{$mime};base64," . base64_encode($content);
        } catch (\Exception $e) {
            Log::error("Error descargando imagen externa: {$url} - " . $e->getMessage());
            return null;
        }
    }
}
