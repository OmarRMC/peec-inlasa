<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class CommandController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::GAME)->only(['run', 'index']);
    }

    public function index()
    {
        return view('game.index');
    }
    public function run()
    {
        $commands = config('game');

        $key = request('command');

        abort_unless(isset($commands[$key]), 403, 'Comando no permitido');

        $process = new Process(
            $commands[$key]['cmd'],
            base_path(),
            array_merge($_ENV, [
                'APPDATA' => getenv('APPDATA'),
                'COMPOSER_HOME' => getenv('APPDATA') . '\\Composer',
            ])
        );

        $process->setTimeout(3600);
        $process->run();

        return response()->json([
            'command' => $key,
            'type' => $commands[$key]['type'],
            'description' => $commands[$key]['description'],
            'output' => $process->getOutput(),
            'error' => $process->getErrorOutput(),
            'success' => $process->isSuccessful(),
        ]);
    }
}
