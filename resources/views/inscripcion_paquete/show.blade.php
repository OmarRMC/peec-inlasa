@php
    use App\Models\Permiso;
    use App\Models\Configuracion;
    $documentos = [
        'Contrato firmado',
        'Formulario de inscripción',
        'Poder legal',
        'Carnet de identidad',
        'Registro de Comercio',
        'Designación de responsable',
    ];
    $actualizarDocumentos = $inscripcion->documentosInscripcion->isNotEmpty();
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
                    <div><strong>Código de Lab:</strong> {{ $inscripcion->laboratorio->cod_lab }}</div>
                    <div><strong>Laboratorio:</strong> {{ $inscripcion->laboratorio->nombre_lab }}</div>
                    <div><strong>Fecha de Inscripción:</strong> {{ $inscripcion->fecha_inscripcion }}</div>
                    <div><strong>Gestión:</strong> {{ $inscripcion->gestion }}</div>
                    <div><strong>Cantidad de Paquetes:</strong> {{ $inscripcion->cant_paq }}</div>
                    <div><strong>Costo Total:</strong> {{ number_format($inscripcion->costo_total, 2) }} Bs</div>
                    <div><strong>Saldo</strong> {{ number_format($inscripcion->saldo, 2) }} Bs</div>
                    <div><strong>Estado Inscripción:</strong>
                        <x-status-badge :value="$inscripcion->estado_inscripcion_texto ?? ''" />
                    </div>
                    <div><strong>Estado de Cuenta:</strong>
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
                            @if (!empty($detalle->descuento) && $detalle->descuento > 0)
                                <div><strong>Descuento aplicado:</strong> {{ $detalle->descuento }}%</div>
                            @endif
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
                    @if (!$inscripcion->estaAnulado())
                        <div>
                            @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN]))
                                <button onclick="document.getElementById('modalPago').showModal()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                    Registrar Pago
                                </button>
                            @endif
                            @if (Gate::any([Permiso::LABORATORIO,Permiso::GESTION_PAGOS, Permiso::ADMIN]) && configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS) && $inscripcion->estaAprobado())
                                <button onclick="document.getElementById('modalSubirPagos').showModal()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                    aria-label="Subir documentos" title="Subir documentos">
                                    Subir el comprobante
                                </button>
                            @endif
                            @if (Gate::any([Permiso::GESTION_PAGOS, Permiso::ADMIN, Permiso::LABORATORIO]) &&
                                    configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_PAGOS) &&
                                    $inscripcion->documentosPago->isNotEmpty())
                                <a href="{{ route('documentos.pagos.index', $inscripcion->id) }}" target="_blank"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition duration-200">
                                    Ver comprobantes
                                </a>
                            @endif
                        </div>
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
                        <div><strong>Razón Social:</strong> {{ $pago->razon_social ?: '/' }}</div>
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
                            @if (
                                !$inscripcion->estaAprobado() &&
                                    Gate::any([Permiso::LABORATORIO]) &&
                                    configuracion(Configuracion::HABILITAR_SUBIDA_DOCUMENTOS_INSCRIPCION))
                                <a class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                    href="https://forms.gle/PbGcRxwtBxCceV9FA" target="_blank">Subir documentos</a>
                                {{-- <button onclick="document.getElementById('modalSubirDocumentos').showModal()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition duration-200"
                                    aria-label="Subir documentos" title="Subir documentos">
                                    @if ($actualizarDocumentos)
                                        Actualizar documentos
                                    @else
                                        Subir documentos
                                    @endif
                                </button> --}}
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
                                                <textarea name="observacion[]" rows="2" class="w-full border border-gray-300 rounded p-1 text-xs resize-none"></textarea>
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


                <div class="flex flex-wrap gap-4">
                    @forelse ($inscripcion->documentosInscripcion as $doc)
                        <div class="border rounded p-3 bg-gray-50 text-gray-700 flex flex-col items-center w-40">
                            <!-- Nombre del documento -->
                            <div class="text-center mb-2">
                                <strong class="text-sm">Documento:</strong>
                                <p class="text-sm truncate" title="{{ $doc->nombre_doc }}">{{ $doc->nombre_doc }}</p>
                            </div>

                            <div class="mt-2 flex items-center justify-center w-40 max-h-[100px] border rounded bg-gray-50 text-gray-500 text-xs overflow-hidden"
                                id="preview-db-{{ $doc->id }}">
                            </div>
                            <a href="{{ asset($doc->ruta_doc) }}" target="_blank"
                                class="text-blue-600 text-xs px-2 py-1 border rounded hover:bg-blue-50">
                                Ver en nueva pestaña
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Sin documentos disponibles.</p>
                    @endforelse
                </div>
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
                    class="w-full border rounded px-2 text-sm" pattern="^[A-Za-z0-9\-\/*][A-Za-z0-9\-\/*\s.]{4,30}$"
                    maxlength="30" title="Entre 4 y 30 caracteres. Letras, números o guiones.">
            </div>

            <div id="nro_factura">
                <label id="label_transaccion" class="text-sm font-semibold">N° Factura</label>
                <input type="text" id="nro_factura" name="nro_factura" required
                    class="w-full border rounded px-2 text-sm" pattern="^\d{1,20}$" maxlength="20"
                    title="Solo números, hasta 20 dígitos.">
            </div>

            <div>
                <label class="text-sm font-semibold">Razón Social</label>
                <textarea name="razon_social" class="w-full border rounded px-2 text-sm" rows="1" required pattern=".{3,100}"
                    maxlength="100" title="Debe tener entre 3 y 100 caracteres."></textarea>
            </div>

            <div>
                <label class="text-sm font-semibold">Observaciones</label>
                <textarea name="obs_pago" class="w-full border rounded px-2 text-sm" rows="2" pattern=".{0,255}"
                    maxlength="255" title="Máximo 255 caracteres."></textarea>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('modalPago').close()"
                    class="text-gray-500 hover:underline text-sm">Cancelar</button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 rounded text-sm py-2">Guardar</button>
            </div>
        </form>
    </dialog>
    <dialog id="modalSubirDocumentos" class="rounded-lg shadow-lg backdrop:bg-black/30">
        <form method="POST" action="{{ route('inscripcion-paquetes.lab.subirDocumentos', $inscripcion->id) }}"
            enctype="multipart/form-data" class="bg-white px-4 pb-4 rounded-lg space-y-4 w-full max-w-4xl">
            @csrf
            <h2 class="text-lg font-bold text-blue-700 mb-1 flex justify-between">📁
                @if ($actualizarDocumentos)
                    Actualizar los documentos de inscripción
                @else
                    Subir documentos de inscripción
                @endif
                <span class="text-sm text-gray-700 bg-yellow-100 p-1 rounded font-medium">
                    Tamaño máximo permitido por documento: 5 MB
                </span>
            </h2>
            @if ($actualizarDocumentos)
                <p class="text-sm text-black bg-yellow-100 p-2 rounded mb-4">
                    ⚠️ Nota: Solo suba los documentos que desea actualizar. No es necesario volver a subir los
                    documentos existentes.
                </p>
            @endif
            <div class="grid grid-cols-2 gap-4">
                @foreach ($documentos as $i => $doc)
                    <div class="flex flex-col space-y-2 border rounded-lg p-3 shadow-sm">
                        <label class="text-sm font-semibold text-gray-700">{{ $doc }}</label>
                        <input type="hidden" name="titulos[]" value="{{ $doc }}">

                        <input type="file" name="documentos[]" accept="application/pdf,image/*"
                            class="border border-gray-300 rounded px-2 py-1 text-sm preview-input"
                            data-preview="preview-{{ $i }}" data-link="link-{{ $i }}"
                            @if (!$actualizarDocumentos) required @endif>
                        <div id="preview-{{ $i }}"
                            class="mt-2 flex items-center justify-center max-h-[100px] border rounded bg-gray-50 text-gray-500 text-xs overflow-hidden">
                            No seleccionado
                        </div>

                        <button type="button" id="link-{{ $i }}"
                            class="hidden bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 text-xs rounded"
                            onclick="window.open(this.dataset.url, '_blank')">
                            🔍 Ver completo
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="document.getElementById('modalSubirDocumentos').close()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded text-sm font-semibold">
                    @if ($actualizarDocumentos)
                        Actualizar Documentos
                    @else
                        Subir Documentos
                    @endif
                </button>
            </div>
        </form>
    </dialog>

    <dialog id="modalSubirPagos" class="rounded-lg shadow-lg backdrop:bg-black/30">
        <form method="POST" action="{{ route('pago.lab.subirComprobante', $inscripcion->id) }}"
            enctype="multipart/form-data" class="bg-white px-4 pb-4 rounded-lg space-y-4 w-full max-w-2xl">
            @csrf
            <h2 class="text-lg font-bold text-blue-700 mb-2 flex justify-between">
                Subir Comprobante de Pago
                <span class="text-sm text-gray-700 bg-yellow-100 p-1 rounded font-medium">
                    Tamaño máximo permitido: 5MB
                </span>
            </h2>
            <div>
                <label class="text-sm font-semibold text-gray-700">NIT</label>
                <input type="text" name="nit" id="nit" maxlength="20"
                    value="{{ old('nit', $inscripcion->laboratorio->nit_lab ?? '') }}"
                    class="input-standard w-full @error('nit') border-red-500 @enderror" placeholder="Ej: 1234567-8"
                    required>
                <span id="error-nit" class="text-red-500 text-sm hidden">NIT inválido</span>
            </div>
            <div>
                <label class="text-sm font-semibold text-gray-700">Razón Social</label>
                <input type="text" name="razon_social" id="razon_social" maxlength="100"
                    value="{{ old('razon_social', '') }}"
                    class="input-standard w-full @error('razon_social') border-red-500 @enderror"
                    placeholder="Ej: Laboratorios ABC S.R.L." required>
                <span id="error-razon" class="text-red-500 text-sm hidden">Razón Social inválida</span>
            </div>

            <p class="text-sm text-gray-600">
                Solo se admite <b>PDF o imagen</b>.
            </p>

            <div class="flex flex-col space-y-3">
                <label class="text-sm font-semibold text-gray-700">Archivo del Comprobante</label>
                <input type="file" name="comprobante" accept="application/pdf,image/*"
                    class="border border-gray-300 rounded px-2 py-1 text-sm preview-input"
                    data-preview="preview-comprobante" data-link="link-comprobante" required>

                <div id="preview-comprobante"
                    class="mt-2 flex items-center justify-center max-h-[120px] border rounded bg-gray-50 text-gray-500 text-xs overflow-hidden">
                    No seleccionado
                </div>

                <button type="button" id="link-comprobante"
                    class="hidden bg-blue-100 text-blue-700 hover:bg-blue-200 px-2 py-1 text-xs rounded"
                    onclick="window.open(this.dataset.url, '_blank')">
                    🔍 Ver completo
                </button>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="document.getElementById('modalSubirPagos').close()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-1 rounded text-sm">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm font-semibold">
                    Subir Comprobante
                </button>
            </div>
        </form>
    </dialog>
    @push('scripts')
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

            const nitInput = document.getElementById('nit');
            const razonInput = document.getElementById('razon_social');
            const errorNit = document.getElementById('error-nit');
            const errorRazon = document.getElementById('error-razon');
            const nitPattern = /^\d{1,20}(-\d{1,2})?$/;
            const razonPattern = /^[A-Za-z0-9ÁÉÍÓÚáéíóúÑñ&.\- ]{3,100}$/;
            nitInput.addEventListener('input', () => {
                if (nitPattern.test(nitInput.value.trim())) {
                    nitInput.classList.remove('border-red-500');
                    nitInput.classList.add('border-green-500');
                    errorNit.classList.add('hidden');
                } else {
                    nitInput.classList.remove('border-green-500');
                    nitInput.classList.add('border-red-500');
                    errorNit.classList.remove('hidden');
                }
            });

            razonInput.addEventListener('input', () => {
                if (razonPattern.test(razonInput.value.trim())) {
                    razonInput.classList.remove('border-red-500');
                    razonInput.classList.add('border-green-500');
                    errorRazon.classList.add('hidden');
                } else {
                    razonInput.classList.remove('border-green-500');
                    razonInput.classList.add('border-red-500');
                    errorRazon.classList.remove('hidden');
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
            // function mostrarAlertaConfirmacion(titulo, texto, icono, textoConfirmacion, callback) {
            // }


            document.addEventListener('DOMContentLoaded', function() {
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
            });

            document.addEventListener('DOMContentLoaded', function() {
                @foreach ($inscripcion->documentosInscripcion as $doc)
                    (function() {
                        const container = document.getElementById('preview-db-{{ $doc->id }}');
                        const url = "{{ asset($doc->ruta_doc) }}";
                        const ext = url.split('.').pop().toLowerCase();

                        if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext)) {
                            const img = document.createElement('img');
                            img.src = url;
                            img.classList.add('max-h-40', 'rounded', 'shadow');
                            container.appendChild(img);
                        } else if (ext === 'pdf') {
                            fetch(url).then(res => res.arrayBuffer()).then(async function(data) {
                                const pdf = await window.pdfjsLib.getDocument(new Uint8Array(data))
                                    .promise;
                                const page = await pdf.getPage(1);
                                const scale = 0.6;
                                const viewport = page.getViewport({
                                    scale
                                });

                                const canvas = document.createElement('canvas');
                                const context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                await page.render({
                                    canvasContext: context,
                                    viewport
                                }).promise;
                                container.appendChild(canvas);
                            });
                        } else {
                            container.textContent = "Formato no soportado";
                        }
                    })();
                @endforeach
                document.querySelectorAll('.preview-input').forEach(input => {
                    input.addEventListener('change', async function(event) {
                        const file = event.target.files[0];
                        const previewContainer = document.getElementById(event.target.dataset
                            .preview);
                        const linkBtn = document.getElementById(event.target.dataset.link);

                        previewContainer.innerHTML = "";
                        linkBtn.classList.add("hidden");

                        if (!file) {
                            previewContainer.textContent = "No seleccionado";
                            return;
                        }

                        const fileURL = URL.createObjectURL(file);
                        linkBtn.dataset.url = fileURL;
                        linkBtn.classList.remove("hidden");

                        if (file.type.startsWith("image/")) {
                            const img = document.createElement("img");
                            img.src = fileURL;
                            img.classList.add("max-h-[100px]", "rounded", "shadow", "mx-auto",
                                "bg-cover");
                            previewContainer.appendChild(img);
                        } else if (file.type === "application/pdf") {
                            const fileReader = new FileReader();
                            fileReader.onload = async function() {
                                const typedArray = new Uint8Array(this.result);
                                const pdf = await window.pdfjsLib.getDocument(typedArray)
                                    .promise;
                                const page = await pdf.getPage(1);

                                const scale = 0.5;
                                const viewport = page.getViewport({
                                    scale
                                });
                                const canvas = document.createElement("canvas");
                                const context = canvas.getContext("2d");
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;

                                await page.render({
                                    canvasContext: context,
                                    viewport
                                }).promise;
                                previewContainer.appendChild(canvas);
                            };
                            fileReader.readAsArrayBuffer(file);
                        } else {
                            previewContainer.textContent = "Formato no soportado";
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
