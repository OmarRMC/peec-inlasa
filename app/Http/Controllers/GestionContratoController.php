<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\ContratoDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GestionContratoController extends Controller
{
    public function index()
    {
        $contratos = Contrato::with('detalles')->orderBy('id', 'desc')->get();
        return view('gestion_contratos.index', compact('contratos'));
    }

    public function saveAll(Request $request)
    {
        $idContrato = $request->id_contrato;

        if ($request->has('secciones')) {

            foreach ($request->secciones as $id => $data) {

                $detalle = ContratoDetalle::find($id);
                if (!$detalle) continue;

                $detalle->titulo      = $data['titulo'] ?? $detalle->titulo;
                $detalle->descripcion = $data['descripcion'] ?? null;
                $detalle->posicion    = $data['posicion'] ?? 1;
                $detalle->estado      = isset($data['estado']) ? 1 : 0;

                $detalle->save();
            }
        }
        if ($request->filled('secciones_eliminar')) {
            $ids = explode(',', $request->secciones_eliminar);
            ContratoDetalle::whereIn('id', $ids)->delete();
        }
        if ($request->has('nuevas')) {

            foreach ($request->nuevas as $data) {
                if (!isset($data['titulo']) || $data['titulo'] == "") {
                    continue;
                }

                ContratoDetalle::create([
                    'id_contrato' => $idContrato,
                    'titulo' => $data['titulo'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'posicion' => $data['posicion'] ?? 1,
                    'estado' => isset($data['estado']) ? 1 : 0,
                ]);
            }
        }
        return back()->with('success', 'Contrato actualizado correctamente.');
    }


    public function store(Request $request)
    {
        $contrato = Contrato::create([
            'firmante_nombre'      => $request->firmante_nombre,
            'firmante_cargo'       => $request->firmante_cargo,
            'firmante_institucion' => $request->firmante_institucion,
            'firmante_detalle'     => $request->firmante_detalle,
            'estado'               => $request->estado ?? true,
        ]);

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato creado correctamente.');
    }

    public function show($id)
    {
        $contrato = Contrato::with('detalles')->findOrFail($id);
        return view('gestion_contratos.show', compact('contrato'));
    }

    public function edit($id)
    {
        $contrato = Contrato::findOrFail($id);
        return view('gestion_contratos.edit', compact('contrato'));
    }

    public function update(Request $request, $id)
    {
        $contrato = Contrato::findOrFail($id);

        $contrato->update([
            'firmante_nombre'      => $request->firmante_nombre,
            'firmante_cargo'       => $request->firmante_cargo,
            'firmante_institucion' => $request->firmante_institucion,
            'firmante_detalle'     => $request->firmante_detalle,
            'estado'               => $request->estado,
        ]);

        return redirect()->route('contratos.index', $contrato->id)
            ->with('success', 'Contrato actualizado correctamente.');
    }

    public function destroy($id)
    {
        $contrato = Contrato::findOrFail($id);
        $contrato->delete();

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato eliminado.');
    }
}
