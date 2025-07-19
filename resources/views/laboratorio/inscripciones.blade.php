<x-app-layout>
    <div class="px-4 py-6 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Lista de Inscripciones</h1>
        </div>

        <!-- Filtros -->
        <div class="flex flex-col gap-2">
            <div class="flex justify-end">
                <a href="{{ route('lab.inscripcion.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                    <i class="fas fa-plus-circle"></i> Registrar una inscripcion
                </a>
            </div>
            <div class="flex items-center justify-between flex-wrap gap-4 mb-4 text-sm ">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <label for="custom-length">Mostrar</label>
                    <select id="custom-length" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>registros</span>
                </div>

                <div class="flex items-center gap-2 justify-end">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="custom-search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            placeholder="Buscar inscripciones...">
                    </div>
                    <button id="btn-search"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table id="inscripciones-table" class="min-w-full text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Acciones</th>
                            <th class="px-4 py-2 text-left">Fecha</th>
                            <th class="px-4 py-2 text-left">Laboratorio</th>
                            <th class="px-4 py-2 text-left">Código</th>
                            <th class="px-4 py-2 text-left">País</th>
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-left">Categoría</th>
                            <th class="px-4 py-2 text-left">Nivel</th>
                            <th class="px-4 py-2 text-left">Gestión</th>
                            <th class="px-4 py-2 text-left">Paquetes</th>
                            <th class="px-4 py-2 text-left">Costo</th>
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
                document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#inscripciones-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('lab_inscripcion.ajax.data') }}",
                            data: function(d) {

                            }
                        },
                        order: [
                            [1, 'desc']
                        ],
                        columns: [{
                                data: 'acciones',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'fecha',
                                name: 'fecha_inscripcion'
                            },
                            {
                                data: 'nombre_lab',
                                name: 'laboratorio.nombre_lab'
                            },
                            {
                                data: 'codigo_lab',
                                name: 'laboratorio.cod_lab'
                            },
                            {
                                data: 'pais',
                                name: 'laboratorio.pais.nombre_pais'
                            },
                            {
                                data: 'tipo',
                                name: 'laboratorio.tipo.descripcion'
                            },
                            {
                                data: 'categoria',
                                name: 'laboratorio.categoria.descripcion'
                            },
                            {
                                data: 'nivel',
                                name: 'laboratorio.nivel.descripcion_nivel'
                            },
                            {
                                data: 'gestion',
                                name: 'gestion'
                            },
                            {
                                data: 'paquetes',
                                name: 'detalle_inscripcion.descripcion_paquete',
                                searchable: false,
                                orderable: false,
                            },
                            {
                                data: 'costo',
                                name: 'costo_total'
                            },
                            {
                                data: 'estado',
                                name: 'status_inscripcion'
                            }
                        ],
                        language: {
                            url: '/translation/es.json'
                        },
                        dom: 'rt',
                        lengthChange: false,
                        drawCallback: () => {
                            tippy('[data-tippy-content]');
                            setupPagination(table);
                        }
                    });
                    $('#btn-search').on('click', () => table.search($('#custom-search').val()).draw());
                    $('#custom-search').on('keypress', e => {
                        if (e.which === 13) table.search($('#custom-search').val()).draw();
                    });
                    $('#custom-length').on('change', function() {
                        table.page.len(this.value).draw();
                    });

                    function resetFilters(ids) {
                        ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i.toUpperCase()}</option>`)
                            .prop('disabled', true));
                    }
                });
            </script>
        @endpush
</x-app-layout>
