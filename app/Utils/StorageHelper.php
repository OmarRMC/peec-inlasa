<?php

namespace App\Utils;

use Illuminate\Support\Str;

class StorageHelper
{
    /**
     * Convierte una URL tipo "/storage/xxx/yyy.png" a data-uri.
     * Si el archivo es SVG, lo convierte a PNG con Imagick para que
     * DomPDF pueda renderizarlo como background-image CSS.
     */
    public static function storageUrlToDataUri(?string $url): ?string
    {
        if (!$url) return null;

        if (!Str::startsWith($url, '/storage/')) {
            return null;
        }

        $relative = Str::after($url, '/storage/');
        $absolute = storage_path('app/public/' . $relative);

        if (!is_file($absolute)) return null;

        $mime = mime_content_type($absolute) ?: 'image/png';

        // SVG no es soportado como background-image CSS en DomPDF.
        // Lo convertimos a PNG con Imagick para garantizar el renderizado.
        if (stripos($mime, 'svg') !== false && extension_loaded('imagick')) {
            try {
                $imagick = new \Imagick();
                $imagick->setBackgroundColor(new \ImagickPixel('white'));
                $imagick->readImage($absolute);
                $imagick->setImageFormat('png');

                // Limitar dimensiones para que GD (usado por DomPDF) no agote la memoria.
                // GD necesita width × height × 4 bytes; con 1800px cabe en ~13 MB.
                $w = $imagick->getImageWidth();
                $h = $imagick->getImageHeight();
                if ($w > 1800 || $h > 1800) {
                    $imagick->resizeImage(1800, 1800, \Imagick::FILTER_LANCZOS, 1, true);
                }

                $pngContent = $imagick->getImageBlob();
                $imagick->destroy();

                return 'data:image/png;base64,' . base64_encode($pngContent);
            } catch (\Exception $e) {
                // Si Imagick falla, continúa con el base64 normal del SVG
            }
        }

        $content = file_get_contents($absolute);
        if ($content === false) return null;

        return "data:{$mime};base64," . base64_encode($content);
    }
}
