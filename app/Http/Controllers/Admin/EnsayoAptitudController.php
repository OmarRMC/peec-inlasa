<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\EnsayoAptitud;
use App\Models\Paquete;
use App\Models\Permiso;
use App\Models\Programa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnsayoAptitudController extends Controller
{
    public function __construct()
    {
        $this->middleware('canany:' . Permiso::ADMIN . ',' . Permiso::GESTION_PROGRAMAS_AREAS_PAQUETES_EA)->only(['index', 'create', 'update', 'destroy', 'show', 'edit', 'porPaquete']);
    }
    private function messages()
    {
        return [
            'id_paquete.required' => 'El paquete es obligatorio.',
            'id_paquete.exists' => 'El paquete seleccionado no existe.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no debe exceder 100 caracteres.',
            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }

    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $ensayos = EnsayoAptitud::with(['paquete', 'paquete.area'])
            ->when($search, fn($q) => $q->where('descripcion', 'like', "%{$search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('ensayo_aptitud.index', compact('ensayos', 'search'));
    }

    public function create()
    {
        $backUrl = request('back_url', '');
        $defaultIdPaquete = (int) request('id_paquete', 0);

        $paquetes = Paquete::active()->get();

        // Si venimos de jerarquía, asegurar que el paquete padre esté en la lista
        if ($defaultIdPaquete && $paquetes->doesntContain('id', $defaultIdPaquete)) {
            $paquetePadre = Paquete::find($defaultIdPaquete);
            if ($paquetePadre) {
                $paquetes->prepend($paquetePadre);
            }
        }

        return view('ensayo_aptitud.create', compact('paquetes', 'backUrl', 'defaultIdPaquete'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_paquete' => 'required|exists:paquete,id',
            'descripcion' => 'required|string|max:100',
            'status' => 'required|boolean',
        ], $this->messages());

        EnsayoAptitud::create($request->all());

        $backUrl = $request->_back_url;
        return redirect($backUrl ?: route('ensayo_aptitud.index'))->with('success', 'Ensayo de Aptitud creado correctamente.');
    }

    public function edit(EnsayoAptitud $ensayoAptitud)
    {
        $paquetes = Paquete::active()->get();
        $ensayo_aptitud = $ensayoAptitud;
        $backUrl = request('back_url', '');
        return view('ensayo_aptitud.edit', compact('ensayo_aptitud', 'paquetes', 'backUrl'));
    }

    public function update(Request $request, EnsayoAptitud $ensayoAptitud)
    {
        $request->validate([
            'id_paquete' => 'required|exists:paquete,id',
            'descripcion' => 'required|string|max:100',
            'status' => 'required|boolean',
        ], $this->messages());

        $ensayoAptitud->update($request->all());

        $backUrl = $request->_back_url;
        return redirect($backUrl ?: route('ensayo_aptitud.index'))->with('success', 'Ensayo de Aptitud actualizado correctamente.');
    }

    public function destroy(Request $request, EnsayoAptitud $ensayoAptitud)
    {
        $ensayoAptitud->delete();
        $backUrl = $request->_back_url;
        return redirect($backUrl ?: route('ensayo_aptitud.index'))->with('success', 'Ensayo de Aptitud eliminado correctamente.');
    }

    public function porPaquete(Paquete $paquete)
    {
        $paquete->load('area.programa');
        $ensayos = $paquete->ensayosAptitud()->orderBy('descripcion')->paginate(20)->withQueryString();
        return view('ensayo_aptitud.por_paquete', compact('paquete', 'ensayos'));
    }

    public function getEnsayoPorAreaAjax(Request $request, $id)
    {
        $area = Area::active()
            ->with([
                'paquetes' => function ($query) {
                    $query->active();
                },
                'paquetes.ensayosAptitud' => function ($query) {
                    $query->active();
                },
            ])
            ->findOrFail($id);
        Log::info('$area');
        Log::info($area);
        $ensayos = $area->paquetes->flatMap(function ($paquete) {
            return $paquete->ensayosAptitud->map(function ($ensayo) use ($paquete) {
                $descPaquete = mb_strtolower(trim($paquete->descripcion), 'UTF-8');
                $descEnsayo = mb_strtolower(trim($ensayo->descripcion), 'UTF-8');

                return [
                    'id' => $ensayo->id,
                    'descripcion' => $descPaquete === $descEnsayo
                        ? $ensayo->descripcion
                        : "$paquete->descripcion - $ensayo->descripcion"
                ];
            });
        });
        return response()->json($ensayos);
    }
}
