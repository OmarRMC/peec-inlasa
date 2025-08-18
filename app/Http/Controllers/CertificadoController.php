<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificadoController extends Controller
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function certificadoDesempenoParticionListLabs()
    {
        $gestiones = ['2025', '2024', '2023'];
        return view('certificados.admin.des_participacion', compact('gestiones'));
    }
    public function publicarCertificado(Request $request, $gestion)
    {
        $total = Certificado::where('gestion_certificado', $gestion)
            ->noPublicado()
            ->whereHas('inscripcion', fn($q) => $q->Aprobado())
            ->whereHas('detalles', fn($q) => $q->where('temporal', false))
            ->update(['publicado' => 1]);

        return back()->with('success', "Se publicaron {$total} certificados de la gestión {$gestion}.");
    }
}
