<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DetalleCertificado;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DetalleCertificadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!Gate::any([Permiso::ADMIN, Permiso::GESTION_CERTIFICADOS, Permiso::RESPONSABLE])) {
            return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
        }
        $request->validate([
            'calificacion_certificado' => 'nullable|string|max:100',
        ]);
        $DetalleCertificado = DetalleCertificado::findOrFail($id);
        $DetalleCertificado->calificacion_certificado = $request->calificacion_certificado;
        $DetalleCertificado->updated_by = Auth::user()->id;
        $DetalleCertificado->save();
        return response()->json(['message' => 'Detalle certificado actualizado correctamente.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
