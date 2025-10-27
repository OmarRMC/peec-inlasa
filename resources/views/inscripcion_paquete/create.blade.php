@php
    use App\Models\Permiso;
    use App\Models\Configuracion;
@endphp
<x-app-layout>
    <div class="container py-6 max-w-5xl">
        <!-- Título y botón -->
        @if (
            (Gate::any([Permiso::LABORATORIO]) && Configuracion::esPeriodoInscripcion()) ||
                Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-bold text-primary">Inscripción de Paquetes</h1>
                <button id="openModal" class="btn-primary">
                    <i class="fas fa-plus-circle"></i> Seleccionar Paquetes
                </button>
            </div>
        @endif

        <!-- Modal para seleccionar paquetes -->
        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-3xl relative">
                @if ($deudasPendientes->isNotEmpty())
                    <div class="overflow-x-auto mb-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong class="font-bold">¡Atención!</strong>
                            <span class="block">
                                Tienes deudas pendientes y debes pagarlas antes de poder realizar nuevas inscripciones.
                            </span>
                        </div>
                        <table class="table w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="">Gestión</th>
                                    <th class="">Costo Total</th>
                                    <th class="">Saldo</th>
                                    <th class="">Paquetes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deudasPendientes as $deuda)
                                    <tr>
                                        <td class="">{{ $deuda->gestion }}</td>
                                        <td class="">
                                            {{ number_format($deuda->costo_total, 2) }}</td>
                                        <td class="">
                                            {{ number_format($deuda->saldo, 2) }}</td>
                                        <td class="">{{ $deuda->paquetes_descripcion }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div>
                        <h2 class="text-lg font-bold mb-4">Seleccionar Paquetes</h2>

                        <!-- Selector de Programa -->
                        <div class="mb-4">
                            <label class="block mb-1 text-sm font-medium">Programa</label>
                            <select id="programaSelect"
                                class="form-select block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">Seleccione un programa...</option>
                                @foreach ($programas as $programa)
                                    <option value="{{ $programa->id }}">{{ $programa->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tabla de paquetes disponibles -->
                        <div class="overflow-x-auto mb-4">
                            <table class="table w-full text-sm" id="tablaPaquetes">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Descripción</th>
                                        <th>Área</th>
                                        <th>Costo</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Botón cerrar modal -->
                <div class="flex justify-end">
                    <button id="cerrarModal" class="btn-secondary">Cerrar</button>
                </div>
            </div>
        </div>

        <div>
            <label for="observacionesGenerales"
                class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
            <textarea id="observacionesGenerales"
                class="w-full border border-gray-300 rounded-md shadow-sm text-sm p-2 focus:outline-none focus:ring-1 focus:ring-blue-300"
                rows="1"></textarea>
        </div>
        <!-- Tabla de paquetes seleccionados (fuera del modal) -->
        <div class="overflow-x-auto mb-4 mt-5">
            <h3 class="font-semibold mb-2">Paquetes Seleccionados</h3>
            <table class="table w-full text-sm">
                <thead>
                    <tr>
                        <th>Paquete</th>
                        <th>Área</th>
                        <th>Costo</th>
                        <th>Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaSeleccionados">
                </tbody>
            </table>
            <div class="text-right mt-2 font-bold">
                Total: <span id="total">0 Bs.</span>
            </div>
        </div>

        <!-- Botón de guardar fuera del modal -->
        @if ($deudasPendientes->isEmpty())
            <div class="flex justify-end">
                <button id="confirmarInscripcion" class="btn-primary">Confirmar Inscripción</button>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById('modal');
            const openModalBtn = document.getElementById('openModal');
            const cerrarModalBtn = document.getElementById('cerrarModal');
            const programaSelect = document.getElementById('programaSelect');
            const tablaPaquetes = document.getElementById('tablaPaquetes');
            const tablaSeleccionados = document.getElementById('tablaSeleccionados');
            const totalSpan = document.getElementById('total');
            const confirmarBtn = document.getElementById('confirmarInscripcion');

            const seleccionados = [];
            let paquetesDisponibles = [];
            const baseUrl = `{{ url('/admin/paquetes/programa') }}`;

            const LAB_ID = {{ $laboratorio->id }};

            openModalBtn.addEventListener('click', () => {
                modal.classList.remove('hidden');
            });

            cerrarModalBtn.addEventListener('click', () => {
                modal.classList.add('hidden');
                if (tablaPaquetes?.querySelector('tbody')) {
                    tablaPaquetes.querySelector('tbody').innerHTML = '';
                }
                if (programaSelect) {
                    programaSelect.value = '';
                }
            });
            let tablaPaquetesDT;
            programaSelect?.addEventListener('change', () => {
                const programaId = programaSelect.value;
                if (tablaPaquetesDT) {
                    tablaPaquetesDT.destroy();
                    tablaPaquetes.querySelector('tbody').innerHTML = '';
                }
                if (programaId) {
                    tablaPaquetesDT = $('#tablaPaquetes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: `${baseUrl}?programa_id=${programaId}&lab_id=${LAB_ID}`,
                        columns: [{
                                data: 'acciones',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'nombre_paquete'
                            },
                            {
                                data: 'nombre_area'
                            },
                            { 
                                data: 'costo', 
                                render: data => `${data} Bs.` 
                            }
                        ],
                        language: {
                            url: "{{ asset('translation/es.json') }}"
                        },
                        dom: 'frtp',
                        lengthChange: false, 
                        paging: true,
                        pageLength: 10,
                        drawCallback: function() {
                            tippy('[data-tippy-content]');
                            $(document).on('click', '.agregar-paquete', function() {
                                const id = $(this).data('id');
                                const paquete = $(this).data('paquete');
                                const costo = parseFloat($(this).data('costo'));
                                const area = $(this).data('area');

                                if (!seleccionados.some(p => p.id === id)) {
                                    seleccionados.push({
                                        id,
                                        nombre_paquete: paquete,
                                        costo,
                                        nombre_area: area
                                    });
                                    renderizarSeleccionados();
                                }
                            });
                        }
                    });
                }
            });

            window.agregarPaquete = function(id) {
                const pkt = paquetesDisponibles.find(p => p.paquete_id === id);
                if (!seleccionados.some(p => p.paquete_id === id)) {
                    seleccionados.push(pkt);
                    renderizarSeleccionados();
                }
            };

            window.eliminarPaquete = function(index) {
                seleccionados.splice(index, 1);
                renderizarSeleccionados();
            };
            window.guardarObservacion = function(index, valor) {
                seleccionados[index].observaciones = valor.trim();
            };

            function renderizarSeleccionados() {
                tippy('[data-tippy-content]');
                tablaSeleccionados.innerHTML = '';
                let total = 0;
                seleccionados.forEach((pkt, index) => {
                    total += pkt.costo;
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${pkt.nombre_paquete}</td>
                        <td>${pkt.nombre_area}</td>
                        <td>${pkt.costo} Bs.</td>
                        <td class='text_obs'>
                        </td>
                        <td>
                        <button data-tippy-content="Quitar paquete"
                                    onclick="eliminarPaquete(${index})"
                                    class="bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-2 py-1 rounded shadow-sm"
                            >
                            <i class="fas fa-minus-circle"></i>
                        </button>
                        </td>
                    `;
                    const textarea = document.createElement('input');
                    textarea.className =
                        "w-full p-1 border rounded-md text-sm resize-none focus:outline-none focus:ring-1 focus:border-blue-300";
                    textarea.rows = 2;
                    // textarea.placeholder = "Ingrese observaciones...";
                    textarea.value = pkt.observaciones ?? '';
                    textarea.oninput = function() {
                        guardarObservacion(index, this.value);
                    };
                    tr.querySelector('.text_obs').appendChild(textarea);
                    tablaSeleccionados.appendChild(tr);
                });
                totalSpan.textContent = `${total} Bs.`;
            }
            const registrarInscription = () => {
                const observacionesGenerales = document.getElementById('observacionesGenerales').value.trim();
                const data = {
                    id_lab: LAB_ID,
                    gestion: new Date().getFullYear(),
                    obs_inscripcion: observacionesGenerales,
                    paquetes: seleccionados.map(pkt => ({
                        id: pkt.id,
                        descripcion: pkt.nombre_paquete,
                        costo: pkt.costo,
                        observaciones: pkt.observaciones ?? ''
                    }))
                };

                fetch(`{{ url('/admin/inscripcion/paquetes') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => {
                        return res.json();
                    }).then(res => {
                        if (res.success) {
                            @if (isset($redirectTo))
                                window.location.href = "{{ $redirectTo }}";
                            @else
                                window.location.href = res.redirect_url;
                            @endif
                        } else {
                            alert(res.message || 'Error al registrar inscripción.');
                        }
                    })
            }

            confirmarBtn?.addEventListener('click', () => {
                if (seleccionados.length === 0) {
                    alert('Debe seleccionar al menos un paquete.');
                    return;
                }
                confirmAsyncHandle({
                    html: `
                        <div class="text-left text-gray-700 space-y-4">
                            <p class="text-base">¿Está seguro de que desea registrar la inscripción con los siguientes paquetes?</p>
                            <div class="bg-gray-50 p-4 rounded-md shadow-sm border border-gray-200">
                                <!-- Contenedor con scroll solo para la lista -->
                                <div class="max-h-64 overflow-y-auto pr-2">
                                    <ul class="divide-y divide-gray-200">
                                        ${seleccionados.map(pkt => `
                                                                                                                                                            <li class="py-0 flex justify-between">
                                                                                                                                                                <span class="font-medium text-sm">${pkt.nombre_paquete}</span>
                                                                                                                                                                <span class="text-sm text-gray-600">${parseFloat(pkt.costo).toFixed(2)} Bs.</span>
                                                                                                                                                            </li>
                                                                                                                                                            `).join('')}
                                    </ul>
                                </div>
                                <!-- Total fijo -->
                                <div class="border-t pt-3 mt-3 text-sm flex justify-between font-semibold text-gray-800  sticky bottom-0">
                                    <span>Total</span>
                                    <span>
                                        ${seleccionados.reduce((total, pkt) => total + parseFloat(pkt.costo), 0).toFixed(2)} Bs.
                                    </span>
                                </div>
                            </div>
                        </div>
                    `,
                    title: 'Confirmar Inscripción',
                    csrfToken: '{{ csrf_token() }}',
                    icon: 'warning',
                    handle: registrarInscription
                });



            });
        });
    </script>
</x-app-layout>
