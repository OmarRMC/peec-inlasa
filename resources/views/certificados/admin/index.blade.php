@php
use App\Models\Inscripcion;
use App\Models\Permiso;
use App\Models\Certificado;
@endphp

<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">

        <div>
            <!-- Filtros -->
            <div class="flex flex-wrap gap-3 mb-4 text-sm">
                <!-- Filtro Mostrar registros -->
                <div class="flex items-center gap-2 flex-wrap">
                    <label for="custom-length" class="whitespace-nowrap">Mostrar</label>
                    <select id="custom-length"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[80px]">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="whitespace-nowrap">registros</span>
                </div>

                <!-- Filtro por Gestión -->
                <select id="filter-gestion"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    <option value="">Todas las Gestiones</option>
                    @foreach ($gestiones as $gestion)
                    <option value="{{ $gestion }}">{{ $gestion }}</option>
                    @endforeach
                </select>

                <!-- Filtro Tipo / Categoría / Estados -->
                <div class="flex flex-wrap gap-2">
                    <select id="filter-status"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Estados de certificado</option>
                        @foreach (Certificado::STATUS_CERTIFICADO as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select id="filter-publicado"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="1">Publicados</option>
                        <option value="0">No publicados</option>
                        <option value="todos">Todos</option>
                    </select>
                </div>

                <!-- Filtros activos (paquetes) -->
                <div class="flex space-y-1 w-full">
                    <div id="paquete-filters" class="flex flex-wrap gap-2">
                    </div>
                </div>

                <!-- Buscador -->
                <div class="flex items-center gap-2 justify-end !w-full ">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="custom-search"
                            class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                            placeholder="Buscar laboratorio...">
                    </div>
                    <button id="btn-search"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table id="inscripciones-table" class="min-w-full text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Laboratorio</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">Gestión</th>
                            <th class="px-4 py-2 text-left">Plantilla</th>
                            <th class="px-4 py-2 text-left">Participacion</th>
                            <th class="px-4 py-2 text-left">Desempeño</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Publicación</th>
                            @if ($puedeEditar)
                            <th class="px-4 py-2 text-left">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="custom-info"></div>
                <div id="custom-pagination" class="flex justify-center"></div>
            </div>
        </div>
        @if ($puedeEditar)
        <div id="modal-editar-certificado"
            class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-lg rounded-lg shadow p-6 relative">
                <button type="button" onclick="closeEditarModal()"
                    class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-lg font-bold mb-4 text-primary">Editar certificado</h2>

                <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                    <div><span class="text-gray-500">Código:</span> <span id="modal-cert-cod" class="font-medium"></span></div>
                    <div><span class="text-gray-500">Gestión:</span> <span id="modal-cert-gestion" class="font-medium"></span></div>
                </div>

                <div class="flex items-center justify-between gap-2 p-3 rounded-md bg-gray-50 border mb-4">
                    <div class="text-sm">
                        <span class="text-gray-500">Estado:</span>
                        <span id="modal-cert-estado" class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded"></span>
                    </div>
                    <button type="button" id="btn-cert-despublicar" class="btn-secondary text-xs">
                        <i class="fas fa-eye-slash mr-1"></i>Despublicar
                    </button>
                    <button type="button" id="btn-cert-publicar" class="btn-primary text-xs">
                        <i class="fas fa-eye mr-1"></i>Publicar
                    </button>
                </div>

                <form id="form-editar-certificado">
                    <div class="mb-2">
                        <label class="label" for="modal-cert-nombre">Nombre en el certificado</label>
                        <input type="text" id="modal-cert-nombre" class="input-standard" maxlength="400" required>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        Nombre actual del laboratorio: <span id="modal-cert-nombre-actual" class="font-medium"></span>
                    </p>
                    <button type="button" id="btn-cert-usar-actual"
                        class="text-xs text-indigo-600 hover:underline">
                        <i class="fas fa-sync-alt mr-1"></i>Usar nombre del laboratorio
                    </button>
                    <p id="modal-cert-aviso" class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded p-2 mt-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Despublique el certificado para editar el nombre.
                    </p>

                    <div class="flex justify-end space-x-2 mt-6">
                        <button type="button" onclick="closeEditarModal()" class="btn-secondary">Cancelar</button>
                        <button type="submit" id="btn-cert-guardar" class="btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @push('scripts')
        <script>
            const filtrosPaquetes = new Set();
            const plantillasDisponibles = @json($plantillas);

            function escapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            }

            function renderPlantillaSelect(data, type, row) {
                let options = '<option value="">Sin plantilla</option>';
                plantillasDisponibles.forEach(p => {
                    const selected = (row.plantilla_id == p.id) ? 'selected' : '';
                    options += `<option value="${p.id}" ${selected}>${p.nombre}</option>`;
                });
                return `<select class="plantilla-select border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[140px]"
                                data-id-lab="${row.idLab}"
                                data-gestion="${row.gestion}">
                                ${options}
                            </select>`;
            }

            function updatePlantilla(idLab, gestion, plantillaId, selectElement) {
                const originalValue = selectElement.dataset.originalValue || '';

                fetch("{{ route('admin.certificado.update-plantilla') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id_laboratorio: idLab,
                            gestion: gestion,
                            plantilla_id: plantillaId || null
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            selectElement.dataset.originalValue = plantillaId;
                            showToast({
                                title: 'Plantilla actualizada correctamente.'
                            });
                        } else {
                            selectElement.value = originalValue;
                            showError('Error: ' + (data.error || 'No se pudo actualizar'))
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        selectElement.value = originalValue;
                        showError('Error al actualizar la plantilla');
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                const table = $('#inscripciones-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.certificado.ajax.index') }}",
                        data: function(d) {
                            d.pais = $('#filter-pais').val();
                            d.estado = $('#filter-status').val();
                            d.gestion = $('#filter-gestion').val();
                            d.publicado = $('#filter-publicado').val();
                        }
                    },
                    order: [
                        [2, 'desc'],
                        [0, 'asc']
                    ],
                    columns: [{
                            data: 'laboratorio',
                            name: 'laboratorio',
                            render: function(data, type, row) {
                                if (type !== 'display') return data;
                                let html = escapeHtml(data);
                                if (row.nombre_lab_actual && row.nombre_lab_actual !== data) {
                                    html += `<div class="text-[11px] text-amber-700">Nombre actual: ${escapeHtml(row.nombre_lab_actual)}</div>`;
                                }
                                return html;
                            }
                        },
                        {
                            data: 'codigo_lab',
                            name: 'codigo_lab'
                        },
                        {
                            data: 'gestion',
                            name: 'gestion'
                        },
                        {
                            data: 'plantilla_id',
                            name: 'plantilla',
                            render: renderPlantillaSelect,
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'participacion',
                            name: 'participacion'
                        },
                        {
                            data: 'desempeño',
                            name: 'desempeño'
                        },
                        {
                            data: 'estado',
                            name: 'estado'
                        },
                        {
                            data: 'publicado',
                            name: 'publicado',
                            orderable: false,
                            searchable: false,
                            render: function(data, type) {
                                if (type !== 'display') return data ? 'Publicado' : 'No publicado';
                                return data ?
                                    `<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs font-medium rounded shadow-sm"><i class="fas fa-eye"></i> Publicado</span>` :
                                    `<span class="inline-flex items-center gap-1 px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-medium rounded shadow-sm"><i class="fas fa-eye-slash"></i> No publicado</span>`;
                            }
                        },
                        @if ($puedeEditar)
                        {
                            data: 'acciones',
                            name: 'acciones',
                            orderable: false,
                            searchable: false
                        },
                        @endif
                    ],
                    language: {
                        url: "{{ asset('translation/es.json') }}"
                    },
                    dom: 'rt',
                    lengthChange: false,
                    drawCallback: () => {
                        tippy('[data-tippy-content]');
                        setupPagination(table);

                        // Guardar valores originales y agregar listeners a los selects
                        document.querySelectorAll('.plantilla-select').forEach(select => {
                            select.dataset.originalValue = select.value;
                        });
                    }
                });

                // Delegación de eventos para los selects de plantilla
                $('#inscripciones-table').on('change', '.plantilla-select', function() {
                    const select = this;
                    const idLab = select.dataset.idLab;
                    const gestion = select.dataset.gestion;
                    const plantillaId = select.value;
                    updatePlantilla(idLab, gestion, plantillaId, select);
                });

                $('#btn-search').on('click', () => table.search($('#custom-search').val()).draw());
                $('#custom-search').on('keypress', e => {
                    if (e.which === 13) table.search($('#custom-search').val()).draw();
                });
                $('#custom-length').on('change', function() {
                    table.page.len(this.value).draw();
                });
                // $('#filter-pais').on('change', () => table.draw());

                $('#filter-status').on('change', () => table.draw());

                $('#filter-gestion').on('change', () => table.draw());

                $('#filter-publicado').on('change', () => table.draw());

                @if ($puedeEditar)
                const modal = document.getElementById('modal-editar-certificado');
                const inputNombre = document.getElementById('modal-cert-nombre');
                let certActual = null;

                function renderEstadoModal() {
                    const estado = document.getElementById('modal-cert-estado');
                    const publicado = certActual.publicado;
                    estado.textContent = publicado ? 'Publicado' : 'No publicado';
                    estado.className = 'inline-flex items-center px-2 py-0.5 text-xs font-medium rounded ' +
                        (publicado ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800');
                    document.getElementById('btn-cert-despublicar').style.display = publicado ? '' : 'none';
                    document.getElementById('btn-cert-publicar').style.display = publicado ? 'none' : '';
                    document.getElementById('modal-cert-aviso').style.display = publicado ? '' : 'none';
                    ['btn-cert-usar-actual', 'btn-cert-guardar'].forEach(id => {
                        const btn = document.getElementById(id);
                        btn.disabled = publicado;
                        btn.style.opacity = publicado ? '0.5' : '';
                        btn.style.cursor = publicado ? 'not-allowed' : '';
                    });
                    inputNombre.disabled = publicado;
                    inputNombre.style.backgroundColor = publicado ? '#f3f4f6' : '';
                    inputNombre.style.cursor = publicado ? 'not-allowed' : '';
                }

                window.openEditarModal = function(data) {
                    certActual = data;
                    document.getElementById('modal-cert-cod').textContent = data.codLab;
                    document.getElementById('modal-cert-gestion').textContent = data.gestion;
                    document.getElementById('modal-cert-nombre-actual').textContent = data.nombreActual;
                    inputNombre.value = data.nombre;
                    renderEstadoModal();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                };

                window.closeEditarModal = function() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    certActual = null;
                };

                function postCertificado(url, extra = {}) {
                    return fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            id_laboratorio: certActual.idLab,
                            gestion: certActual.gestion,
                            ...extra
                        })
                    }).then(async response => {
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok || !data.success) {
                            throw new Error(data.error || data.message || 'No se pudo completar la acción');
                        }
                        return data;
                    });
                }

                function cambiarPublicacion(publicar) {
                    confirmDialog({
                        title: publicar ? '¿Publicar certificado?' : '¿Despublicar certificado?',
                        text: publicar ?
                            'El laboratorio podrá volver a descargar sus certificados.' :
                            'El laboratorio dejará de poder descargar sus certificados hasta que se vuelvan a publicar.',
                        icon: publicar ? 'question' : 'warning',
                        confirmText: publicar ? 'Publicar' : 'Despublicar'
                    }).then(result => {
                        if (!result.isConfirmed) return;
                        const url = publicar ?
                            "{{ route('admin.certificado.publicar-laboratorio') }}" :
                            "{{ route('admin.certificado.despublicar') }}";
                        postCertificado(url)
                            .then(data => {
                                certActual.publicado = data.publicado ?? publicar;
                                renderEstadoModal();
                                showToast({ title: data.message });
                                table.ajax.reload(null, false);
                            })
                            .catch(err => showError(err.message));
                    });
                }

                $('#inscripciones-table').on('click', '.btn-editar-cert', function() {
                    openEditarModal({
                        idLab: this.dataset.idLab,
                        gestion: this.dataset.gestion,
                        codLab: this.dataset.codLab,
                        nombre: this.dataset.nombre,
                        nombreActual: this.dataset.nombreActual,
                        publicado: this.dataset.publicado === '1'
                    });
                });

                document.getElementById('btn-cert-despublicar').addEventListener('click', () => cambiarPublicacion(false));
                document.getElementById('btn-cert-publicar').addEventListener('click', () => cambiarPublicacion(true));
                document.getElementById('btn-cert-usar-actual').addEventListener('click', () => {
                    inputNombre.value = certActual.nombreActual;
                });

                document.getElementById('form-editar-certificado').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const nombre = inputNombre.value.trim();
                    if (!nombre) {
                        showError('El nombre no puede estar vacío.');
                        return;
                    }
                    postCertificado("{{ route('admin.certificado.update-nombre') }}", {
                            nombre_laboratorio: nombre
                        })
                        .then(data => {
                            showToast({ title: data.message });
                            closeEditarModal();
                            table.ajax.reload(null, false);
                        })
                        .catch(err => showError(err.message));
                });
                @endif

                function resetFilters(ids) {
                    ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i}</option>`)
                        .prop('disabled', true));
                }
            });
        </script>
        @endpush
</x-app-layout>