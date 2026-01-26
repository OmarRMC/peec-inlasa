<?php

namespace App\Utils;

use Illuminate\Support\Str;

class StorageHelper
{
    /**
     * Convierte una URL tipo "/storage/xxx/yyy.png" a data-uri.
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
        $content = file_get_contents($absolute);
        if ($content === false) return null;

        return "data:{$mime};base64," . base64_encode($content);
    }
}
