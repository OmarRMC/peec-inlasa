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
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
                <div id="custom-info"></div>
                <div id="custom-pagination" class="flex justify-center"></div>
            </div>
        </div>
        @push('scripts')
        <script>
            const filtrosPaquetes = new Set();
            const plantillasDisponibles = @json($plantillas);

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
                        alert('Error al actualizar la plantilla');
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
                        }
                    },
                    order: [
                        [1, 'desc']
                    ],
                    columns: [{
                            data: 'laboratorio',
                            name: 'laboratorio'
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
                        }
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

                function resetFilters(ids) {
                    ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i}</option>`)
                        .prop('disabled', true));
                }
            });
        </script>
        @endpush
</x-app-layout>