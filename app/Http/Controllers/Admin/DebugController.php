<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class DebugController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::GAME)->only(['descargar', 'descargarRuta']);
    }

    public function descargar()
    {
        $zipFileName = 'debug_' . now()->format('Ymd_His') . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            $logPath = storage_path('logs');
            $this->addFolderToZip($zip, $logPath, 'logs');

            $migrationsPath = database_path('migrations');
            $this->addFolderToZip($zip, $migrationsPath, 'migrations');

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function descargarRuta(Request $request)
    {
        $rutaRelativa = $request->input('path', '');

        // Normalizar separadores
        $rutaRelativa = str_replace('\\', '/', trim($rutaRelativa, '/\\'));

        $base = realpath(base_path());
        $rutaAbsoluta = realpath($base . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rutaRelativa));

        // Seguridad: la ruta debe existir y estar dentro del proyecto
        abort_if(!$rutaAbsoluta, 404, 'Ruta no encontrada');
        abort_if(strpos($rutaAbsoluta, $base) !== 0, 403, 'Acceso denegado');

        // Si es un archivo, descargarlo directamente
        if (is_file($rutaAbsoluta)) {
            return $this->enviarDescarga($rutaAbsoluta, basename($rutaAbsoluta), false);
        }

        // Si es una carpeta, comprimir y descargar
        if (is_dir($rutaAbsoluta)) {
            $nombreCarpeta = basename($rutaAbsoluta);
            $zipFileName = $nombreCarpeta . '_' . now()->format('Ymd_His') . '.zip';
            $zipPath = storage_path('app/' . $zipFileName);

            $zip = new ZipArchive;
            abort_if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true, 500, 'No se pudo crear el ZIP');

            $this->addFolderToZip($zip, $rutaAbsoluta, $nombreCarpeta);
            $zip->close();

            return $this->enviarDescarga($zipPath, $zipFileName, true);
        }

        abort(404, 'La ruta no es un archivo ni carpeta válida');
    }

    private function enviarDescarga($filePath, $fileName, $eliminarDespues)
    {
        // Cerrar sesión antes de enviar para que Laravel no agregue output después
        session()->save();

        // Desactivar compresión y buffering de PHP/Apache
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('implicit_flush', true);

        // Limpiar todos los buffers activos
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $fileSize = filesize($filePath);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mimeType = $ext === 'zip' ? 'application/zip' : 'application/octet-stream';

        header_remove();
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . $fileSize);
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Enviar en chunks para evitar problemas de memoria con archivos grandes
        $handle = fopen($filePath, 'rb');
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);

        if ($eliminarDespues) {
            @unlink($filePath);
        }

        exit;
    }

    private function addFolderToZip($zip, $folderPath, $zipFolder)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipFolder . '/' . substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
