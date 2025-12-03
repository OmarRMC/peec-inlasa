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
                    <i class="fas fa-plus-circle"></i> Agregar una nueva inscripción
                </button>
            </div>
        @endif

        <!-- Modal para seleccionar paquetes -->
        <div id="modal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-2 sm:p-6">
            <div
                class="bg-white rounded-lg shadow-lg w-full max-w-full sm:max-w-4xl relative overflow-y-auto max-h-[90vh] p-3 sm:p-6 text-xs sm:text-sm">
                @if ($deudasPendientes->isNotEmpty())
                    <div class="overflow-x-auto mb-4">
                        <div
                            class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-4 text-xs sm:text-sm">
                            <strong class="font-bold">¡Atención!</strong>
                            <span class="block">Tienes deudas pendientes y debes pagarlas antes de poder realizar
                                nuevas inscripciones.</span>
                        </div>
                        <table class="table w-full text-xs sm:text-sm min-w-[500px]">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th>Gestión</th>
                                    <th>Costo Total</th>
                                    <th>Saldo</th>
                                    <th>Paquetes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deudasPendientes as $deuda)
                                    <tr>
                                        <td>{{ $deuda->gestion }}</td>
                                        <td>{{ number_format($deuda->costo_total, 2) }}</td>
                                        <td>{{ number_format($deuda->saldo, 2) }}</td>
                                        <td>{{ $deuda->paquetes_descripcion }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col sm:flex-row gap-1">
                        <div>
                            <div class="flex justify-between items-center mb-2 flex-wrap">
                                <h2 class="text-sm font-bold mb-2">Selecciona Paquetes</h2>
                                <div class="flex items-center gap-1 justify-end mb-1 flex-wrap w-full sm:w-auto">
                                    <div class="relative w-full sm:w-56 mb-1 sm:mb-0">
                                        <span
                                            class="absolute inset-y-0 left-0 flex items-center pl-1.5 sm:pl-2 text-gray-500 text-xs">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="search" id="custom-search-lab"
                                            class="w-full pl-7 sm:pl-8 pr-2 sm:pr-3 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                                            placeholder="Buscar paquetes...">
                                    </div>
                                    <button id="btn-search-lab"
                                        class="px-2 py-1 sm:px-3 sm:py-1.5 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Selector de Programa -->
                            <div class="mb-4">
                                <label class="block mb-1 text-xs sm:text-sm font-medium">Programa</label>
                                <select id="programaSelect"
                                    class="form-select block w-full border-gray-300 rounded-md shadow-sm text-xs sm:text-sm">
                                    <option value="">Seleccione un programa...</option>
                                    @foreach ($programas as $programa)
                                        <option value="{{ $programa->id }}">{{ $programa->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tabla de paquetes disponibles -->
                            <div class="overflow-x-auto mb-4">
                                <table class="table w-full text-xs sm:text-sm min-w-[500px]" id="tablaPaquetes">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Descripción</th>
                                            {{-- <th>Área</th> --}}
                                            <th>Costo (Bs)</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="flex justify-between items-center flex-wrap mt-2 gap-2 text-xs sm:text-sm">
                                <div id="custom-info"></div>
                                <div id="custom-pagination" class="flex justify-center"></div>
                            </div>

                        </div>
                        <div id="paquetesSeleccionados" class="">
                            <div
                                class="relative bg-white w-full p-2  max-h-[50vh] h-[90%]
                                    overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                                <div id="modal-selecionados">
                                    <!-- Aquí se insertará el contenido clonado -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Botón cerrar modal -->
                <div class="flex justify-end gap-2 flex-wrap">
                    <button id="cerrarModal"
                        class="btn-secondary px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm">Cerrar</button>
                    {{-- <button id="btn-ver-seleccionados"
                        class="px-3 py-1 sm:px-4 sm:py-2 text-white rounded btn-primary transition shadow text-xs sm:text-sm">
                        <i class="fas fa-eye"></i> Ver Paquetes Seleccionados
                    </button> --}}
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
        {{-- <div id="paquetesSeleccionados"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-2 sm:p-6">
            <div
                class="relative bg-white rounded-lg shadow-lg w-full max-w-3xl p-2 
               max-h-[90vh] overflow-y-auto overflow-x-hidden scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <!-- Contenedor donde se clonará la lista -->
                <button id="btn-ocultar-seleccionados"
                    class="px-3 text-white rounded btn-primary transition shadow text-sm">
                    <i class="fas fa-eye-slash"></i>Seguir Inscribiéndose
                </button>
                <div id="modal-selecionados">
                    <!-- Aquí se insertará el contenido clonado -->
                </div>
            </div>
        </div> --}}
        <!-- Tabla de paquetes seleccionados (fuera del modal) -->
        <div class="overflow-x-auto mb-4 mt-5" id="paquetesSeleccionadosContainer">
            <h3 class="font-semibold mb-2">Paquetes Seleccionados</h3>
            <table class="table w-full text-sm">
                <thead>
                    <tr>
                        <th>Paquete</th>
                        <th class="head_area">Área</th>
                        <th>Costo</th>
                        <th class="head_obs">Observaciones</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="tablaSeleccionados">
                </tbody>
            </table>
            <div class="text-right mt-2 font-bold costo-total">
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
    @push('scripts')
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
                const costoTotal = document.querySelector('.costo-total');

                // const btnListarInscripciones = document.getElementById('btn-ver-seleccionados');
                // const btnOcultarSeleccionados = document.getElementById('btn-ocultar-seleccionados');
                const listaInscripcionesDiv = document.getElementById('paquetesSeleccionados');
                const paquetesSeleccionadosContainer = document.getElementById('paquetesSeleccionadosContainer');



                // btnOcultarSeleccionados.addEventListener('click', () => {
                //     listaInscripcionesDiv.classList.add('hidden');
                //     const modalContent = listaInscripcionesDiv.querySelector('#modal-selecionados');
                //     modalContent.innerHTML = '';
                // });

                function renderModalSelecionadoPaquetes() {
                    const clone = paquetesSeleccionadosContainer.cloneNode(
                        true);
                    clone.id = '';
                    const costoTotalClone = costoTotal.cloneNode(true);
                    const modalContent = listaInscripcionesDiv.querySelector('#modal-selecionados');
                    modalContent.innerHTML = '';
                    modalContent.prepend(costoTotalClone);
                    modalContent.appendChild(clone);

                    modalContent.querySelectorAll('tr').forEach(tr => {
                        const tdToRemove = tr.querySelector('td.text_obs');
                        const thToRemove = tr.querySelector('th.head_obs');
                        const thToArea = tr.querySelector('th.head_area');
                        const tdToArea = tr.querySelector('td.text_area');
                        if (tdToRemove) {
                            tdToRemove.remove();
                        }
                        if (thToRemove) {
                            thToRemove.remove();
                        }
                        if (thToArea) {
                            thToArea.remove();
                        }
                        if (tdToArea) {
                            tdToArea.remove();
                        }
                    });

                    listaInscripcionesDiv.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
                // btnListarInscripciones.addEventListener('click', () => {
                //     listaInscripcionesDiv.classList.remove('hidden');
                //     renderModalSelecionadoPaquetes();
                // });

                const seleccionados = [];
                let paquetesDisponibles = [];
                const baseUrl = `{{ url('/admin/paquetes/programa') }}`;

                const LAB_ID = {{ $laboratorio->id }};

                openModalBtn.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    listaInscripcionesDiv.classList.remove('hidden');
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
                                    data: 'nombre_paquete',
                                },
                                // {
                                //     data: 'nombre_area'
                                // },
                                {
                                    data: 'costo_con_descuento',
                                    render: data => `${data}`
                                }
                            ],
                            columnDefs: [{
                                    width: '40px',
                                    targets: 0
                                },
                                {
                                    width: '250px',
                                    targets: 1
                                },
                                {
                                    width: '100px',
                                    targets: 2
                                }
                            ],
                            language: {
                                url: "{{ asset('translation/es.json') }}"
                            },
                            dom: 'rt',
                            lengthChange: false,
                            paging: true,
                            pageLength: 6,
                            autoWidth: false,
                            drawCallback: function() {
                                tippy('[data-tippy-content]');
                                setupPagination(tablaPaquetesDT);
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
                                        renderModalSelecionadoPaquetes();
                                    }
                                });
                            }
                        });
                        $('#btn-search-lab').on('click', () => tablaPaquetesDT.search($('#custom-search-lab')
                                .val())
                            .draw());
                        $('#custom-search-lab').on('keypress', e => {
                            if (e.which === 13) tablaPaquetesDT.search($('#custom-search-lab').val())
                                .draw();
                        });
                    }
                });

                window.agregarPaquete = function(id) {
                    console.log('Agregar paquete ID:', id);
                    const pkt = paquetesDisponibles.find(p => p.paquete_id === id);
                    if (!seleccionados.some(p => p.paquete_id === id)) {
                        seleccionados.push(pkt);
                        renderizarSeleccionados();
                    }
                };

                window.eliminarPaquete = function(index) {
                    seleccionados.splice(index, 1);
                    renderizarSeleccionados();
                    renderModalSelecionadoPaquetes();
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
                        <td class='text_area'>${pkt.nombre_area}</td>
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
        <div class="text-left text-gray-700 text-sm space-y-2">
            <p class="text-[13px] leading-snug">
                ¿Está seguro de registrar la inscripción con los siguientes paquetes?
            </p>
            <div class="bg-gray-50 p-2 rounded-md border border-gray-200 shadow-inner">
                <!-- Contenedor scrollable -->
                <div class="max-h-40 overflow-y-auto pr-1">
                    <ul class="divide-y divide-gray-200">
                        ${seleccionados.map(pkt => `
                                                                                                                                                            <li class="py-1 flex justify-between items-center">
                                                                                                                                                                <span class="font-medium text-[12px]">${pkt.nombre_paquete}</span>
                                                                                                                                                                <span class="text-[12px] text-gray-600">${parseFloat(pkt.costo).toFixed(2)} Bs.</span>
                                                                                                                                                            </li>
                                                                                                                                                        `).join('')}
                    </ul>
                </div>
                <!-- Total fijo -->
                <div class="border-t border-gray-300 pt-2 mt-2 flex justify-between font-semibold text-gray-800 text-[13px]">
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
    @endpush
</x-app-layout>
