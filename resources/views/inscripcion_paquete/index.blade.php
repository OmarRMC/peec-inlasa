<x-app-layout>
    <div class="px-4 py-6 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-bold text-gray-800">Lista de Inscripciones</h1>
        </div>

        <!-- Filtros -->
        <div>
            <div class="flex flex-wrap gap-4 mb-4 text-sm">
                <div class="flex items-center gap-2 text-sm text-gray-700">
                    <label for="custom-length">Mostrar</label>
                    <select id="custom-length" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>registros</span>
                </div>
                <!-- Filtro por Rango de Fecha -->
                <div class="flex items-center gap-2 text-sm">
                    <label for="filter-fecha-inicio">Fecha:</label>
                    <input type="date" id="filter-fecha-inicio" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <span>a</span>
                    <input type="date" id="filter-fecha-fin" class="border-gray-300 rounded-md shadow-sm text-sm">
                </div>

                <!-- Filtro por Gestión -->
                <select id="filter-gestion" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todas las Gestiones</option>
                    @foreach ($gestiones as $gestion)
                        <option value="{{ $gestion }}">{{ $gestion }}</option>
                    @endforeach
                </select>


                <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
                    <div class="flex space-x-2 text-sm">
                        <select id="filter-pais" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Todos los Países</option>
                            @foreach ($paises as $pais)
                                <option value="{{ $pais->id }}">{{ $pais->nombre_pais }}</option>
                            @endforeach
                        </select>
                        <select id="filter-dep" class="border-gray-300 rounded-md shadow-sm text-sm" disabled>
                            <option value="">Seleccione Departamento</option>
                        </select>
                        <select id="filter-prov" class="border-gray-300 rounded-md shadow-sm text-sm" disabled>
                            <option value="">Seleccione Provincia</option>
                        </select>
                        <select id="filter-mun" class="border-gray-300 rounded-md shadow-sm text-sm" disabled>
                            <option value="">Seleccione Municipio</option>
                        </select>
                    </div>
                    <div>
                        <select id="filter-tipo" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Todos los Tipos</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                            @endforeach
                        </select>
                        <select id="filter-categoria" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Todas las Categorías</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                            @endforeach
                        </select>
                        {{-- <select id="filter-nivel" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Todos los Niveles</option>
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id }}">{{ $nivel->descripcion_nivel }}</option>
                            @endforeach
                        </select> --}}
                        <select id="filter-paquete" class="border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">Seleccione un paquete</option>
                            @foreach ($paquetes as $paquete)
                                <option value="{{ $paquete->id }}">{{ $paquete->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Filtro por Paquetes -->
                    <div class="flex flex-col space-y-1 w-full sm:w-80">
                        <!-- Contenedor para filtros activos -->
                        <div id="paquete-filters" class="flex flex-wrap gap-2"></div>
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
                            <th class="px-4 py-2 text-left">Tipo</th>
                            <th class="px-4 py-2 text-left">Categoría</th>
                            {{-- <th class="px-4 py-2 text-left">Nivel</th> --}}
                            <th class="px-4 py-2 text-left">Gestión</th>
                            <th class="px-4 py-2 text-left">Paquetes</th>
                            <th class="px-4 py-2 text-left">Costo</th>
                            <th class="px-4 py-2 text-left">Estado</th>
                            <th class="px-4 py-2 text-left">Cuenta</th>
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
                document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#inscripciones-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('inscripcion_paquete.ajax.data') }}",
                            data: function(d) {
                                d.pais = $('#filter-pais').val();
                                d.tipo = $('#filter-tipo').val();
                                d.categoria = $('#filter-categoria').val();
                                // d.nivel = $('#filter-nivel').val();
                                d.dep = $('#filter-dep').val();
                                d.prov = $('#filter-prov').val();
                                d.mun = $('#filter-mun').val();
                                d.fecha_inicio = $('#filter-fecha-inicio').val();
                                d.fecha_fin = $('#filter-fecha-fin').val();
                                d.gestion = $('#filter-gestion').val();
                                d.paquetes = Array.from(filtrosPaquetes);
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
                                data: 'tipo',
                                name: 'laboratorio.tipo.descripcion'
                            },
                            {
                                data: 'categoria',
                                name: 'laboratorio.categoria.descripcion'
                            },
                            // {
                            //     data: 'nivel',
                            //     name: 'laboratorio.nivel.descripcion_nivel'
                            // },
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
                            },
                            {
                                data: 'cuenta',
                                name: 'status_cuenta'
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
                    $('#filter-pais, #filter-tipo, #filter-categoria, #filter-nivel').on('change', () => table.draw());

                    $('#filter-pais').on('change', async function() {
                        const pais = this.value;
                        $('#filter-dep').prop('disabled', !pais).html('<option>Cargando...</option>');
                        if (!pais) {
                            resetFilters(['dep', 'prov', 'mun']);
                            table.draw();
                            return;
                        }

                        const data = await fetch(`{{ url('/api/admin/departamento') }}/${pais}`).then(r => r
                            .json());
                        $('#filter-dep').html('<option value="">Todos</option>' + data.map(d =>
                            `<option value="${d.id}">${d.nombre_dep}</option>`)).prop('disabled', false);
                        resetFilters(['prov', 'mun']);
                        table.draw();
                    });

                    $('#filter-dep').on('change', async function() {
                        const dep = this.value;
                        $('#filter-prov').prop('disabled', !dep).html('<option>Cargando...</option>');
                        if (!dep) {
                            resetFilters(['prov', 'mun']);
                            table.draw();
                            return;
                        }

                        const data = await fetch(`{{ url('/api/admin/provincia') }}/${dep}`).then(r => r
                            .json());
                        $('#filter-prov').html('<option value="">Todos</option>' + data.map(d =>
                            `<option value="${d.id}">${d.nombre_prov}</option>`)).prop('disabled', false);
                        resetFilters(['mun']);
                        table.draw();
                    });

                    $('#filter-prov').on('change', async function() {
                        const prov = this.value;
                        $('#filter-mun').prop('disabled', !prov).html('<option>Cargando...</option>');
                        if (!prov) {
                            resetFilters(['mun']);
                            table.draw();
                            return;
                        }

                        const data = await fetch(`{{ url('/api/admin/municipio') }}/${prov}`).then(r => r
                            .json());
                        $('#filter-mun').html('<option value="">Todos</option>' + data.map(d =>
                            `<option value="${d.id}">${d.nombre_municipio}</option>`)).prop('disabled',
                            false);
                        table.draw();
                    });

                    $('#filter-mun').on('change', () => table.draw());
                    $('#filter-fecha-inicio, #filter-fecha-fin, #filter-gestion').on('change', () => table.draw());

                    function resetFilters(ids) {
                        ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i.toUpperCase()}</option>`)
                            .prop('disabled', true));
                    }

                    $('#filter-paquete').on('change', function() {
                        const id = this.value;
                        const text = this.options[this.selectedIndex].text;
                        if (!id || filtrosPaquetes.has(id)) return;

                        filtrosPaquetes.add(id);
                        $('#paquete-filters').append(`
                            <div class="bg-blue-100 text-blue-800  px-2 rounded-[5px] flex items-center justify-center gap-1 text-xs paquete-tag" data-id="${id}">
                            ${text}
                                <button type="button" class="text-blue-600 hover:text-red-500 font-bold text-lg remove-paquete-filter">&times;</button>
                            </div>
                        `);
                        this.value = '';
                        table.draw(); // redibujar tabla con nuevo filtro
                    });

                    // Eliminar filtros
                    $(document).on('click', '.remove-paquete-filter', function() {
                        const parent = $(this).closest('.paquete-tag');
                        const id = parent.data('id');
                        filtrosPaquetes.delete(String(id));
                        parent.remove();
                        table.draw();
                    });

                    // Pasar los IDs de paquetes como array al backend
                    $('#inscripciones-table').on('preXhr.dt', function(e, settings, data) {
                        data.paquetes = Array.from(filtrosPaquetes);
                    });
                });
            </script>
        @endpush
</x-app-layout>
