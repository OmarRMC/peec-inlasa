<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GaleriaController extends Controller
{
    private const FOLDER   = 'galeria';
    private const MAX_KB   = 51200; // 50 MB

    public function index()
    {
        if (!Gate::any([Permiso::ADMIN])) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        $archivos = $this->listarArchivos();

        return view('galeria.index', compact('archivos'));
    }

    public function upload(Request $request)
    {
        if (!Gate::any([Permiso::ADMIN])) {
            abort(403);
        }

        $request->validate([
            'archivo' => ['required', 'file', 'max:' . self::MAX_KB],
        ], [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.file'     => 'El archivo no es válido.',
            'archivo.max'      => 'El archivo no debe superar los 50 MB.',
        ]);

        $file      = $request->file('archivo');
        $extension = strtolower($file->getClientOriginalExtension());
        $nombre    = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $nombre    = $nombre ?: 'archivo';

        $nombreFinal = $nombre . '_' . strtolower((string) Str::ulid()) . '.' . $extension;

        Storage::disk('public')->putFileAs(self::FOLDER, $file, $nombreFinal);

        return back()->with('success', 'Archivo "' . $nombreFinal . '" subido correctamente.');
    }

    public function destroy(string $filename)
    {
        if (!Gate::any([Permiso::ADMIN])) {
            abort(403);
        }

        // Evitar path traversal
        $filename = basename($filename);
        $path     = self::FOLDER . '/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'El archivo no existe.');
        }

        Storage::disk('public')->delete($path);

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    // ---------------------------------------------------------------

    private function listarArchivos(): array
    {
        if (!Storage::disk('public')->exists(self::FOLDER)) {
            Storage::disk('public')->makeDirectory(self::FOLDER);
        }

        $files  = Storage::disk('public')->files(self::FOLDER);
        $result = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $mime     = Storage::disk('public')->mimeType($file) ?: 'application/octet-stream';
            $size     = Storage::disk('public')->size($file);
            $modified = Storage::disk('public')->lastModified($file);
            $url      = asset('storage/' . $file);

            $result[] = [
                'filename' => $filename,
                'url'      => $url,
                'mime'     => $mime,
                'size'     => $size,
                'modified' => $modified,
                'type'     => $this->clasificarTipo($mime),
            ];
        }

        usort($result, fn($a, $b) => $b['modified'] - $a['modified']);

        return $result;
    }

    private function clasificarTipo(string $mime): string
    {
        if (str_starts_with($mime, 'image/'))    return 'image';
        if ($mime === 'application/pdf')          return 'pdf';
        if (str_contains($mime, 'word'))          return 'word';
        if (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) return 'excel';
        if (str_contains($mime, 'zip') || str_contains($mime, 'rar') || str_contains($mime, 'compressed')) return 'zip';
        return 'other';
    }
}
