@php
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Detalles de la Inscripción</h1>
            <x-shared.btn-volver :url="$backTo ?? route('inscripcion_paquete.index')" />
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden divide-y divide-gray-200">

            <!-- Datos Generales -->
            <section class="p-6 relative">
                <h2 class="text-lg font-semibold text-blue-700 mb-4">📄 Datos de Inscripción</h2>
                <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <div><strong>Codigo Lab:</strong> {{ $inscripcion->laboratorio->cod_lab }}</div>
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
                <div class="absolute top-2 right-2">
                    @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                        @if ($inscripcion->estaAprobado() || $inscripcion->estaAnulado())
                            <form method="POST" id="en-revision-inscripcion"
                                action="{{ route('inscripcion-paquetes.enRevision', $inscripcion->id) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-medium px-4 py-2 rounded shadow-sm text-sm transition duration-150"
                                    title="Pasar a estado En Revisión">
                                    Establecer en Revisión
                                </button>
                            </form>
                        @else
                            <form method="POST" id="anular-inscripcion"
                                action="{{ route('inscripcion-paquetes.anular', $inscripcion->id) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-red-100 hover:bg-red-200 text-red-800 font-medium px-4 py-2 rounded shadow-sm text-sm transition duration-150"
                                    title="Anular esta inscripción">
                                    Anular la Inscripción
                                </button>
                            </form>
                            {{-- <script>
                                const formAnular = document.getElementById('anular-inscripcion');
                                formAnular.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    Swal.fire({
                                        title: `¿Estás seguro de anular la inscripción?`,
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#e3342f',
                                        cancelButtonColor: '#6c757d',
                                        confirmButtonText: 'Anular',
                                        cancelButtonText: 'Cancelar'
                                    }).then(result => {
                                        if (result.isConfirmed) formAnular.submit();
                                    });
                                })
                            </script> --}}
                        @endif
                    @endif

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
                            <div><strong>Observación:</strong>
                                {{ $detalle->observaciones ?? 'No tiene observaciones' }}
                            </div>
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
                    @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN]))
                        <button onclick="document.getElementById('modalPago').showModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Registrar Pago
                        </button>
                    @endif
                </div>

                @forelse ($inscripcion->pagos as $pago)
                    <div class="relative border rounded px-4 py-3 mb-3 text-sm bg-gray-50 text-gray-700 shadow-sm">
                        {{-- Botón Anular en la esquina superior derecha --}}

                        <div class="absolute top-2 right-2">
                            @if ($pago->status)
                                @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN]))
                                    <form method="POST" id='anular-pago'
                                        action="{{ route('pago.destroy', [$pago->id]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800 text-xs underline">
                                            Anular
                                        </button>
                                    </form>
                                @endif
                            @else
                                <p>Anulado</p>
                            @endif
                            @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                                <div class="text-gray-500 mt-2 text-xs space-y-1">
                                    <div>Registrado por: {{ $pago->creador->username ?? 'N/A' }}</div>
                                    @if ($pago->editor && $pago->editor->username)
                                        <div>Anulado por: {{ $pago->editor->username ?? 'N/A' }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div><strong>Fecha:</strong> {{ $pago->fecha_pago }}</div>
                        <div><strong>Monto:</strong> {{ number_format($pago->monto_pagado, 2) }} Bs</div>
                        <div><strong>Transacción:</strong> {{ $pago->tipo_transaccion }}
                            @if ($pago->nro_tranferencia)
                                #{{ $pago->nro_tranferencia }}
                            @endif
                        </div>
                        <div><strong>Nro Factura.:</strong> {{ $pago->nro_factura ?: '/' }}</div>
                        <div><strong>Razon Social:</strong> {{ $pago->razon_social ?: '/' }}</div>
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
                    @if (!$inscripcion->estaAnulado())
                    <div class="flex space-x-2">
                        {{-- Verifica si el usuario tiene permiso --}}
                        @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                            @if ($inscripcion->estaAprobado())
                                <p class="text-green-700 text-sm bg-green-50 p-2">Documentos aprobados</p>
                            @else
                                {{-- Botón para aprobar inscripción --}}
                                <form method="POST" id="aprobar-inscripcion"
                                    action="{{ route('inscripcion-paquetes.aprobar', $inscripcion->id) }}">
                                    @csrf
                                    <button type="submit" {{-- onclick="return confirm('¿Estás seguro de aprobar esta inscripción?')" --}}
                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                        aria-label="Aprobar inscripción" title="Aprobar inscripción">
                                        Aprobar
                                    </button>
                                </form>
                            @endif

                            @if (!$inscripcion->estaAprobado())
                                <button
                                    onclick="document.getElementById('modal-observacion').classList.remove('hidden')"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                    Registrar Observación
                                </button>
                            @endif
                        @endif

                    </div>
                    @endif
                </div>

                {{-- Modal de Observación --}}
                <div id="modal-observacion"
                    class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                    <div class="bg-white p-4 rounded-lg shadow-lg w-full max-w-3xl">
                        <h3 class="text-base font-bold mb-3 text-blue-700">📝 Observaciones por Documento</h3>

                        <form method="POST"
                            action="{{ route('inscripcion-paquetes.obserbaciones', $inscripcion->id) }}">
                            @csrf

                            @php
                                $documentos = [
                                    'Contrato firmado',
                                    'Formulario de inscripción',
                                    'Poder legal',
                                    'Carnet Identidad',
                                    'Registro de comercio',
                                    'Designación de responsable',
                                ];
                                $categoria = $inscripcion->laboratorio->categoria->descripcion ?? '-';
                            @endphp

                            <div class="text-sm text-gray-600 mb-3">
                                <strong>Categoría del laboratorio:</strong> {{ $categoria }}
                            </div>

                            <table class="w-full text-sm border border-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 text-left">Documento</th>
                                        <th class="p-2 text-left">Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documentos as $i => $doc)
                                        <tr class="border-t border-gray-100">
                                            <td class="p-1">
                                                {{ $doc }}
                                                <input type="hidden" name="titulo[]" value="{{ $doc }}">
                                            </td>
                                            <td class="p-1">
                                                <textarea name="observacion[]" rows="2" class="w-full border border-gray-300 rounded p-1 text-xs resize-none"
                                                    placeholder="Sin observación">Sin observación</textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4 flex justify-end space-x-2">
                                <button type="button"
                                    onclick="document.getElementById('modal-observacion').classList.add('hidden')"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                                    Cancelar
                                </button>
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm font-semibold">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
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

            @if (Gate::any([Permiso::GESTION_LABORATORIO, Permiso::ADMIN]))
                <!-- Vigencia -->
                <section class="p-6">
                    <h2 class="text-lg font-semibold text-blue-700 mb-4">📅 Vigencia</h2>
                    @if ($inscripcion->vigencia)
                        <div class="grid md:grid-cols-2 gap-4 text-sm text-gray-700">
                            <div><strong>Inicio:</strong> {{ $inscripcion->vigencia->fecha_inicio }}</div>
                            <div><strong>Fin:</strong> {{ $inscripcion->vigencia->fecha_fin }}</div>
                            {{-- <div><strong>Estado:</strong>
                                <x-status-badge :value="$inscripcion->vigencia->status" />
                            </div> --}}
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No se registró vigencia.</p>
                    @endif
                </section>
            @endif
        </div>
    </div>

    <dialog id="modalPago" class="rounded-lg shadow-lg backdrop:bg-black/30">
        <form id="formPago" class="bg-white p-6 rounded-lg space-y-2 w-80">
            <h2 class="text-lg font-bold text-blue-700 mb-2">Registrar Pago</h2>
            @csrf
            <input type="hidden" name="id_inscripcion" value="{{ $inscripcion->id }}">

            <div>
                <label class="text-sm font-semibold">Fecha de Pago</label>
                <input type="date" name="fecha_pago" required class="w-full border rounded px-2 text-sm"
                    value="{{ now()->format('Y-m-d') }}">
            </div>

            <div class="flex gap-2">
                <div>
                    <label class="text-sm font-semibold">Monto</label>
                    <input type="number" name="monto_pagado" step="0.01" min="0" required
                        class="w-full border rounded px-2 text-sm" pattern="^\d+(\.\d{1,2})?$"
                        title="Solo números con hasta 2 decimales">
                </div>

                <div>
                    <label class="text-sm font-semibold">Tipo de Transacción</label>
                    <select name="tipo_transaccion" id="tipo_transaccion" required
                        class="w-full border rounded px-2 text-sm" onchange="cambiarLabelYValidacion()">
                        <option value="">Seleccione</option>
                        <option value="Depósito">Depósito</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Sigep">Sigep</option>
                    </select>
                </div>
            </div>

            <div id="campo_transaccion" style="display: none;">
                <label id="label_transaccion" class="text-sm font-semibold">N° Transacción</label>
                <input type="text" id="nro_transaccion" name="nro_tranferencia"
                    class="w-full border rounded px-2 text-sm" pattern="^[A-Za-z0-9\-]{4,30}$"
                    title="Entre 4 y 30 caracteres. Letras, números o guiones.">
            </div>

            <div id="nro_factura">
                <label id="label_transaccion" class="text-sm font-semibold">N° Factura</label>
                <input type="text" id="nro_factura" name="nro_factura" required
                    class="w-full border rounded px-2 text-sm" pattern="^\d{1,20}$"
                    title="Solo números, hasta 20 dígitos.">
            </div>

            <div>
                <label class="text-sm font-semibold">Razon Social</label>
                <textarea name="razon_social" class="w-full border rounded px-2 text-sm" rows="1" required pattern=".{3,100}"
                    title="Debe tener entre 3 y 100 caracteres."></textarea>
            </div>

            <div>
                <label class="text-sm font-semibold">Observaciones</label>
                <textarea name="obs_pago" class="w-full border rounded px-2 text-sm" rows="2" pattern=".{0,255}"
                    title="Máximo 255 caracteres."></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalPago').close()"
                    class="text-gray-500 hover:underline text-sm">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 rounded text-sm py-2">Guardar</button>
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

        function cambiarLabelYValidacion() {
            const tipo = document.getElementById('tipo_transaccion').value;
            const label = document.getElementById('label_transaccion');
            const input = document.getElementById('nro_transaccion');
            const contenedor = document.getElementById('campo_transaccion');

            if (tipo === 'Depósito' || tipo === 'Transferencia' || tipo === 'Sigep') {
                contenedor.style.display = 'block';
                label.textContent = 'N° Transacción';
                input.required = true;
            } else {
                contenedor.style.display = 'none';
                input.required = false;
                input.value = '';
            }
            // if (tipo === 'Efectivo') {
            //     // contenedor.style.display = 'none';
            //     // label.textContent = 'N° de Factura';
            //     // input.required = true;
            //     // input.placeholder = 'Ingrese el número de factura';
            // } else if (tipo === 'Depósito' || tipo === 'Transferencia' || tipo === 'Sigep') {
            //     contenedor.style.display = 'block';
            //     label.textContent = 'N° Transacción';
            //     input.required = true;
            //     // input.placeholder = 'Ingrese el número de transacción';
            // } else {

            // }
        }
        // Función genérica para mostrar alertas
        function mostrarAlertaConfirmacion(titulo, texto, icono, textoConfirmacion, callback) {
            Swal.fire({
                title: titulo,
                text: texto,
                icon: icono,
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6c757d',
                confirmButtonText: textoConfirmacion,
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'swal2-sm',
                    title: 'text-base',
                    htmlContainer: 'text-sm'
                }
            }).then(result => {
                if (result.isConfirmed && typeof callback === "function") {
                    callback();
                }
            });
        }

    
        // Asignar alertas a cada formulario
        document.getElementById('en-revision-inscripcion')?.addEventListener('submit', function(e) {
            e.preventDefault();
            mostrarAlertaConfirmacion(
                '¿Pasar a Revisión?',
                'La inscripción se pondrá en estado de revisión.',
                'warning',
                'Sí, en revisión',
                () => this.submit()
            );
        });

        document.getElementById('anular-inscripcion')?.addEventListener('submit', function(e) {
            e.preventDefault();
            mostrarAlertaConfirmacion(
                '¿Anular Inscripción?',
                'Esta acción no se puede deshacer.',
                'error',
                'Sí, anular',
                () => this.submit()
            );
        });

        document.getElementById('aprobar-inscripcion')?.addEventListener('submit', function(e) {
            e.preventDefault();
            mostrarAlertaConfirmacion(
                '¿Aprobar Inscripción?',
                'La inscripción quedará aprobada.',
                'success',
                'Aprobar',
                () => this.submit()
            );
        });

        document.getElementById('anular-pago')?.addEventListener('submit', function(e) {
            e.preventDefault();
            mostrarAlertaConfirmacion(
                '¿Anular Pago?',
                'Este pago se marcará como anulado.',
                'error',
                'Sí, anular',
                () => this.submit()
            );
        });
    </script>
</x-app-layout>
