<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles de la Inscripción</h1>
            <x-shared.btn-volver :url=" $backTo ?? route('inscripcion_paquete.index')" />
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden divide-y divide-gray-200">

            <!-- Datos Generales -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📄 Datos de Inscripción</h2>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Laboratorio:</strong> {{ $inscripcion->laboratorio->nombre_lab }}</div>
                    <div><strong>Fecha de Inscripción:</strong> {{ $inscripcion->fecha_inscripcion }}</div>
                    <div><strong>Gestión:</strong> {{ $inscripcion->gestion }}</div>
                    <div><strong>Cantidad de Paquetes:</strong> {{ $inscripcion->cant_paq }}</div>
                    <div><strong>Costo Total:</strong> {{ number_format($inscripcion->costo_total, 2) }} Bs</div>
                    <div><strong>Saldo</strong> {{ number_format($inscripcion->saldo, 2) }} Bs</div>
                    <div><strong>Estado Inscripción:</strong>
                        <x-status-badge :value="$inscripcion->estado_inscripcion_texto ?? ''" />
                    </div>
                    <div><strong>Estado Cuenta:</strong>
                        <x-status-badge :value="$inscripcion->estado_pago_texto ?? ''" />
                    </div>
                    <div><strong>Observaciones:</strong> {{ $inscripcion->obs_inscripcion ?? '-' }}</div>
                </div>
            </section>

            <!-- Paquetes Inscritos -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📦 Paquetes Inscritos</h2>
                <div class='flex gap-2 flex-wrap'>
                    @forelse ($inscripcion->detalleInscripciones as $detalle)
                        <div class="border rounded px-4 py-2 mb-2 text-sm text-gray-700 bg-gray-50">
                            <div><strong>Paquete:</strong> {{ $detalle->descripcion_paquete }}</div>
                            <div><strong>Costo:</strong> {{ number_format($detalle->costo_paquete, 2) }} Bs</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No hay paquetes registrados.</p>
                    @endforelse
                </div>
            </section>

            <!-- Pagos -->
            <section class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-blue-700">💳 Pagos</h2>
                    <button onclick="document.getElementById('modalPago').showModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                        Registrar Pago
                    </button>
                </div>

                @forelse ($inscripcion->pagos as $pago)
                    <div class="relative border rounded px-4 py-3 mb-3 text-sm bg-gray-50 text-gray-700 shadow-sm">
                        {{-- Botón Anular en la esquina superior derecha --}}

                        <div class="absolute top-2 right-2">
                            @if ($pago->status)
                                <form method="POST" onsubmit="return confirm('¿Estás seguro de anular este pago?')"
                                    action="{{ route('pago.destroy', [$pago->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 text-xs underline">Anular</button>
                                </form>
                            @else
                                <p>Anulado</p>
                            @endif
                            <div class="text-gray-500 mt-2 text-xs space-y-1">
                                <div>Registrado por: {{ $pago->creador->username ?? 'Desconocido' }}</div>
                                <div>Actualizado por: {{ $pago->editor->username ?? '---' }}</div>
                            </div>
                        </div>

                        <div><strong>Fecha:</strong> {{ $pago->fecha_pago }}</div>
                        <div><strong>Monto:</strong> {{ number_format($pago->monto_pagado, 2) }} Bs</div>
                        <div><strong>Transacción:</strong> {{ $pago->tipo_transaccion }}
                            @if ($pago->nro_tranferencia)
                                #{{ $pago->nro_tranferencia }}
                            @endif
                        </div>
                        <div><strong>Obs.:</strong> {{ $pago->obs_pago ?: 'Sin observaciones' }}</div>

                        {{-- Estado (opcional) --}}
                        {{-- <div><strong>Estado:</strong> <x-status-badge :value="$pago->status" /></div> --}}

                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Sin pagos registrados.</p>
                @endforelse
            </section>

            <!-- Documentos -->
            <section class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">📁 Documentos</h2>
                    <button
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                        Aprobar
                    </button>
                </div>
                @forelse ($inscripcion->documentos as $doc)
                    <div
                        class="border rounded px-4 py-2 mb-2 text-sm text-gray-700 bg-gray-50 flex justify-between items-center">
                        <div>
                            <strong>Documento:</strong> {{ $doc->nombre_doc }}<br>
                            <strong>Estado:</strong> <x-status-badge :status="$doc->status" />
                        </div>
                        <a href="{{ asset('storage/' . $doc->ruta_doc) }}" target="_blank"
                            class="text-blue-600 text-sm">Ver documento</a>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">Sin documentos disponibles.</p>
                @endforelse
            </section>

            <!-- Vigencia -->
            <section class="p-6">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📅 Vigencia</h2>
                @if ($inscripcion->vigencia)
                    <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div><strong>Inicio:</strong> {{ $inscripcion->vigencia->fecha_inicio }}</div>
                        <div><strong>Fin:</strong> {{ $inscripcion->vigencia->fecha_fin }}</div>
                        <div><strong>Estado:</strong>
                            <x-status-badge :value="$inscripcion->vigencia->status" />
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No se registró vigencia.</p>
                @endif
            </section>
        </div>
    </div>

    <dialog id="modalPago" class="rounded-lg shadow-lg backdrop:bg-black/30">
        <form id="formPago" class="bg-white p-6 rounded-lg space-y-4 w-80">
            <h2 class="text-lg font-bold text-blue-700 mb-2">Registrar Pago</h2>
            @csrf
            <input type="hidden" name="id_inscripcion" value="{{ $inscripcion->id }}">

            <div>
                <label class="text-sm font-semibold">Fecha de Pago</label>
                <input type="date" name="fecha_pago" required class="w-full border rounded px-2 py-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-semibold">Monto</label>
                <input type="number" name="monto_pagado" step="0.01" required
                    class="w-full border rounded px-2 py-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-semibold">Tipo de Transacción</label>
                <select name="tipo_transaccion" required class="w-full border rounded px-2 py-1 text-sm">
                    <option value="">Seleccione</option>
                    <option value="Depósito">Depósito</option>
                    <option value="Transferencia">Transferencia</option>
                    <option value="Efectivo">Efectivo</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">N° Transacción</label>
                <input type="text" name="nro_tranferencia" class="w-full border rounded px-2 py-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-semibold">Observaciones</label>
                <textarea name="obs_pago" class="w-full border rounded px-2 py-1 text-sm" rows="2"></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-4">
                <button type="button" onclick="document.getElementById('modalPago').close()"
                    class="text-gray-500 hover:underline text-sm">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Guardar</button>
            </div>
        </form>
    </dialog>
    <script>
        document.getElementById('formPago').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const response = await fetch("{{ route('pago.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Error al registrar el pago.');
            }
        });
    </script>
</x-app-layout>
