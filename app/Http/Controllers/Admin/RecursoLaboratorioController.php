<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\RecursoLaboratorio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecursoLaboratorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN);
    }

    public function index()
    {
        $recursos = RecursoLaboratorio::latest('id')->paginate(20);
        return view('recurso_laboratorio.index', compact('recursos'));
    }

    public function create()
    {
        return view('recurso_laboratorio.create');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $data['archivo'] = null;

        if ($request->hasFile('archivo')) {
            $data['archivo'] = $this->subirArchivo($request->file('archivo'), $request->titulo);
            $data['url']     = null;
        }

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        RecursoLaboratorio::create($data);

        return redirect()->route('recursos_lab.index')
            ->with('success', 'Recurso creado correctamente.');
    }

    public function edit(RecursoLaboratorio $recursos_lab)
    {
        return view('recurso_laboratorio.edit', ['recurso' => $recursos_lab]);
    }

    public function update(Request $request, RecursoLaboratorio $recursos_lab)
    {
        $data = $this->validar($request, $recursos_lab->id);

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior si existe
            if ($recursos_lab->archivo) {
                Storage::disk('public')->delete($recursos_lab->archivo);
            }
            $data['archivo'] = $this->subirArchivo($request->file('archivo'), $request->titulo);
            $data['url']     = null;
        } else {
            // Si se llenó URL, limpiar archivo previo
            if (!empty($data['url']) && $recursos_lab->archivo) {
                Storage::disk('public')->delete($recursos_lab->archivo);
                $data['archivo'] = null;
            } else {
                $data['archivo'] = $recursos_lab->archivo;
            }
        }

        $data['updated_by'] = Auth::id();

        $recursos_lab->update($data);

        return redirect()->route('recursos_lab.index')
            ->with('success', 'Recurso actualizado correctamente.');
    }

    public function destroy(RecursoLaboratorio $recursos_lab)
    {
        if ($recursos_lab->archivo) {
            Storage::disk('public')->delete($recursos_lab->archivo);
        }

        $recursos_lab->delete();

        return redirect()->route('recursos_lab.index')
            ->with('success', 'Recurso eliminado correctamente.');
    }

    // ---------------------------------------------------------------

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'titulo'  => 'required|string|max:150',
            'url'     => 'nullable|url|max:500',
            'archivo' => 'nullable|file|max:20480',
            'status'  => 'required|boolean',
        ], [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max'      => 'El título no debe exceder 150 caracteres.',
            'url.url'         => 'La URL no tiene un formato válido.',
            'url.max'         => 'La URL no debe exceder 500 caracteres.',
            'archivo.file'    => 'El archivo no es válido.',
            'archivo.max'     => 'El archivo no debe superar los 20 MB.',
            'status.required' => 'El estado es obligatorio.',
        ]);
    }

    private function subirArchivo($file, string $titulo): string
    {
        $extension   = strtolower($file->getClientOriginalExtension());
        $nombre      = Str::slug($titulo) ?: 'recurso';
        $nombreFinal = $nombre . '_' . strtolower((string) Str::ulid()) . '.' . $extension;

        Storage::disk('public')->putFileAs('galeria', $file, $nombreFinal);

        return 'galeria/' . $nombreFinal;
    }
}
