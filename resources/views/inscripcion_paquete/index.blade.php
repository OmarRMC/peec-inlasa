@php
    use App\Models\Inscripcion;
    use App\Models\Permiso;
@endphp

<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-1">
            <h1 class="text-xl font-bold text-gray-800">Lista de Inscripciones</h1>
            <div class="flex justify-between items-center flex-wrap gap-4">
                @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_INSCRIPCIONES]))
                    <button id="add-inscripcion"
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                        <i class="fas fa-plus-circle"></i> Agregar Inscripción
                    </button>
                @endif
            </div>
        </div>

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

                <!-- Filtro por Rango de Fecha -->
                <div class="flex flex-wrap items-center gap-2">
                    <label for="filter-fecha-inicio" class="whitespace-nowrap">Fecha:</label>
                    <input type="date" id="filter-fecha-inicio" value="{{ $primerDiaMes }}"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[120px]">
                    <span class="whitespace-nowrap">a</span>
                    <input type="date" id="filter-fecha-fin" value="{{ $now }}"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[120px]">
                </div>

                <!-- Filtro por Gestión -->
                <select id="filter-gestion"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    <option value="">Todas las Gestiones</option>
                    @foreach ($gestiones as $gestion)
                        <option value="{{ $gestion }}">{{ $gestion }}</option>
                    @endforeach
                </select>

                <!-- Filtro País / Ubicación -->
                <div class="flex flex-wrap gap-2">
                    <select id="filter-pais"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Todos los Países</option>
                        @foreach ($paises as $pais)
                            <option value="{{ $pais->id }}">{{ $pais->nombre_pais }}</option>
                        @endforeach
                    </select>
                    <select id="filter-dep" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]"
                        disabled>
                        <option value="">Seleccione Departamento</option>
                    </select>
                    <select id="filter-prov"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]" disabled>
                        <option value="">Seleccione Provincia</option>
                    </select>
                    <select id="filter-mun" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]"
                        disabled>
                        <option value="">Seleccione Municipio</option>
                    </select>
                </div>

                <!-- Filtro Tipo / Categoría / Estados -->
                <div class="flex flex-wrap gap-2">
                    <select id="filter-tipo"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Todos los Tipos</option>
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->descripcion }}</option>
                        @endforeach
                    </select>
                    <select id="filter-categoria"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Todas las Categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->descripcion }}</option>
                        @endforeach
                    </select>
                    <select id="filter-status-inscripcion"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                        <option value="">Estados de Inscripción</option>
                        @foreach (Inscripcion::STATUS_INSCRIPCION as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select id="filter-status-cuenta"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Estados de cuenta</option>
                        @foreach (Inscripcion::STATUS_CUENTA as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select id="filter-status-doc-pago"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Estados de doc. pagos</option>
                        @foreach (Inscripcion::STATUS_DOCUMENTO_PAGO as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select id="filter-area"
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                        <option value="">Todos los areas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->descripcion }}</option>
                        @endforeach
                    </select>
                    <select id="filter-paquete" disabled
                        class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[180px]">
                        <option value="">Seleccione un paquete</option>
                        @foreach ($paquetes as $paquete)
                            <option value="{{ $paquete->id }}">{{ $paquete->descripcion }}</option>
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
                            placeholder="Buscar inscripciones...">
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

        <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-3xl relative">
                <h2 class="text-lg font-bold mb-1">Seleccionar Laboratorio</h2>
                <div class="flex items-center gap-2 justify-end !w-full mb-1">
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" id="custom-search-lab"
                            class="w-full pl-10 pr-4 py-1 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-xs"
                            placeholder="Buscar laboratorio...">
                    </div>
                    <button id="btn-search-lab"
                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-xs">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- Tabla de paquetes disponibles -->
                <div class="overflow-x-auto mb-4">
                    <table class="table w-full text-sm" id="tablaLaboratorio">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Inscribir</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Botón cerrar modal -->
                <div class="flex justify-end">
                    <button id="cerrarModal" class="btn-secondary">Cerrar</button>
                </div>
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
                                d.status_ins = $('#filter-status-inscripcion').val();
                                d.status_cuenta = $('#filter-status-cuenta').val();
                                d.status_doc_pago = $('#filter-status-doc-pago').val();
                                d.dep = $('#filter-dep').val();
                                d.prov = $('#filter-prov').val();
                                d.municipio = $('#filter-mun').val();
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
                            url: "{{ asset('translation/es.json') }}"
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

                    $('#filter-status-inscripcion, #filter-status-cuenta, #filter-status-doc-pago').on('change', () => table.draw());

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
                    $('#filter-area').on('change', async function() {
                        const area = this.value;

                        console.log(area);
                        $('#filter-paquete').prop('disabled', !area).html('<option>Cargando...</option>');
                        if (!area) {
                            resetFilters(['paquete']);
                            return;
                        }

                        const data = await fetch(`{{ url('/api/admin/area/${area}/paquetes') }}`).then(r => r
                            .json());
                        $('#filter-paquete').html('<option value="">Todos</option>' + data.map(d =>
                            `<option value="${d.id}">${d.descripcion}</option>`)).prop('disabled',
                            false);
                    })

                    $('#filter-mun').on('change', () => table.draw());
                    $('#filter-fecha-inicio, #filter-fecha-fin, #filter-gestion').on('change', () => table.draw());

                    function resetFilters(ids) {
                        ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i}</option>`)
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
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('modal');
                    const openModalBtn = document.getElementById('add-inscripcion');
                    const cerrarModalBtn = document.getElementById('cerrarModal');
                    const searchLab = document.getElementById('custom-search-lab');
                    const tablaLaboratorio = document.getElementById('tablaLaboratorio');
                    openModalBtn.addEventListener('click', () => {
                        modal.classList.remove('hidden');
                    });
                    cerrarModalBtn.addEventListener('click', () => {
                        modal.classList.add('hidden');
                        tablaLaboratorio.querySelector('tbody').innerHTML = '';
                    });
                    let value;
                    let tablaLabDB;
                    searchLab.addEventListener('change', (event) => {
                        value = event.target.value;
                    });
                    tablaLabDB = $('#tablaLaboratorio').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: "{{ route('getSearchLab') }}",
                        columns: [{
                                data: 'codigo',
                                name: 'cod_lab'
                            },
                            {
                                data: 'nombre_lab',
                                name: 'nombre_lab'
                            },
                            {
                                data: 'status_label',
                                name: 'status'
                            },
                            {
                                data: 'acciones',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        language: {
                            url: "{{ asset('translation/es.json') }}"
                        },
                        dom: 'rt',
                        lengthChange: false,
                        pageLength: 5,
                        drawCallback: function() {
                            tippy('[data-tippy-content]');
                        }
                    });
                    $('#btn-search-lab').on('click', () => tablaLabDB.search($('#custom-search-lab').val()).draw());
                    $('#custom-search-lab').on('keypress', e => {
                        if (e.which === 13) tablaLabDB.search($('#custom-search-lab').val()).draw();
                    });
                });
            </script>
        @endpush
</x-app-layout>
