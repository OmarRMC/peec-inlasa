<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">

        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-gray-800">Lista de Laboratorios</h1>
            <a href="{{ route('laboratorio.create') }}"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Laboratorio
            </a>
        </div>
        
        <!-- Filtros y Buscador -->
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
                <!-- Nuevos filtros -->
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
                <select id="filter-nivel" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Todos los Niveles</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id }}">{{ $nivel->descripcion_nivel }}</option>
                    @endforeach
                </select>

            </div>
            <div class="flex items-center gap-2 justify-end">
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="search" id="custom-search"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Buscar laboratorio...">
                </div>
                <button id="btn-search"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table id="labs-table" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Acciones</th>
                        <th class="px-4 py-2 text-left">Fecha/Hora</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Correo</th>
                        <th class="px-4 py-2 text-left">WhatsApp</th>
                        <th class="px-4 py-2 text-left">Departamento</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Info y paginación -->
        <div class="flex justify-between items-center flex-wrap mt-6 gap-2">
            <div id="custom-info"></div>
            <div id="custom-pagination" class="flex justify-center"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let table = $('#labs-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('laboratorio.ajax.data') }}",
                        data: function(d) {
                            d.pais = $('#filter-pais').val();
                            d.dep = $('#filter-dep').val();
                            d.prov = $('#filter-prov').val();
                            d.mun = $('#filter-mun').val();
                            d.tipo = $('#filter-tipo').val();
                            d.categoria = $('#filter-categoria').val();
                            d.nivel = $('#filter-nivel').val();
                        }
                    },
                    order: [
                        [1, 'desc']
                    ],
                    columns: [{
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                        },
                        {
                            data: 'nombre_lab',
                            name: 'nombre_lab'
                        },
                        {
                            data: 'email',
                            name: 'usuario.email',
                        },
                        {
                            data: 'wapp_lab',
                            name: 'wapp_lab'
                        },
                        {
                            data: 'departamento_nombre',
                            name: 'departamento.nombre_dep'
                        },
                        {
                            data: 'status_label',
                            name: 'status',
                        },
                    ],
                    language: {
                        url: "/translation/es.json"
                    },
                    dom: 'rt',
                    lengthChange: false,
                    drawCallback: function() {
                        tippy('[data-tippy-content]');
                        confirmDelete({
                            csrfToken: '{{ csrf_token() }}',
                            table
                        });
                        setupPagination(table);
                    }
                });

                $('#filter-tipo, #filter-categoria, #filter-nivel').on('change', () => table.draw());
                $('#btn-search').on('click', () => table.search($('#custom-search').val()).draw());
                $('#custom-search').on('keypress', e => {
                    if (e.which === 13) table.search($('#custom-search').val()).draw();
                });

                $('#filter-pais').on('change', async function() {
                    const pais = this.value;
                    $('#filter-dep').prop('disabled', !pais).html('<option>Cargando...</option>');
                    if (!pais) {
                        resetFilters(['dep', 'prov', 'mun']);
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

                function resetFilters(ids) {
                    ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i.toUpperCase()}</option>`)
                        .prop('disabled', true));
                    table.draw();
                }
            });
        </script>
    @endpush
</x-app-layout>
