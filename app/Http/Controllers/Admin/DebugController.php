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
        $this->middleware('canany:' . Permiso::GAME)->only(['descargar']);
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
