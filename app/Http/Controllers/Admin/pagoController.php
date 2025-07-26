<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    /**
     * Registrar un nuevo pago.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_inscripcion'    => 'required|exists:inscripcion,id',
            'fecha_pago'        => 'required|date',
            'monto_pagado'      => 'required|numeric|min:0.1',
            'tipo_transaccion'  => 'required|string|max:50',
            'nro_tranferencia'  => 'nullable|string|max:100',
            'obs_pago'          => 'nullable|string|max:255',
            // 'status'            => 'required|in:Pendiente,Aprobado,Rechazado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            // $pago = Pago::create($request->only([
            //     'id_inscripcion',
            //     'fecha_pago',
            //     'monto_pagado',
            //     'tipo_transaccion',
            //     'nro_tranferencia',
            //     'obs_pago',
            //     'status',
            // ]));
            $pago   = new Pago();
            $pago->fill($request->only([
                'id_inscripcion',
                'fecha_pago',
                'monto_pagado',
                'tipo_transaccion',
                'nro_tranferencia',
                'obs_pago',

            ]));
            $pago->status = true;
            $pago->created_by = Auth::user()->id;
            $pago->save();

            $inscripcion = $pago->inscripcion;
            if ($inscripcion->saldo <= 0) {
                $inscripcion->status_cuenta = 1;
            } else {
                $inscripcion->status_cuenta = 2;
            }

            $inscripcion->save();

            // protected $fillable = [
            //     'id_lab',
            //     'id_formulario',
            //     'cant_paq',
            //     'costo_total',
            //     'obs_inscripcion',
            //     'fecha_inscripcion',
            //     'status_cuenta',
            //     'status_inscripcion',
            //     'created_by',
            //     'updated_by',
            //     'gestion'
            // ];


            DB::commit();

            return response()->json([
                'message' => 'Pago registrado exitosamente.',
                'pago' => $pago
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al registrar el pago.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un pago existente.
     */
    public function destroy(Pago $pago)
    {
        try {
            // Anular el pago en lugar de eliminarlo
            $pago->status = false;
            $pago->updated_by = Auth::user()->id;
            $pago->save();

            // Actualizar el estado de cuenta de la inscripción asociada
            $inscripcion = $pago->inscripcion;

            $saldo = $inscripcion->saldo;
            $inscripcion->status_cuenta = ($saldo <= 0) ? 1 : 2;
            $inscripcion->save();

            return redirect()->back()->with('success', 'Pago anulado correctamente.');
        } catch (\Throwable $e) {
            // return response()->json([
            //     'message' => 'Error al anular el pago.',
            //     'error' => $e->getMessage()
            // ], 500);
            return redirect()->back()->with('error', 'No se anulo el pago');
        }
    }
}
