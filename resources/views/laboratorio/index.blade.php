@php
    $estados = [
        '1' => 'Activo',
        '0' => 'Inactivo',
    ];
    $descuento = [
        '1' => 'Con Descuento',
        '0' => 'Sin Descuento',
    ];
    use App\Models\Permiso;
@endphp
<x-app-layout>
    <div class="px-4 max-w-6xl mx-auto">

        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4">
            <h1 class="text-xl font-bold text-gray-800">Lista de Laboratorios</h1>
            @if (Gate::any([Permiso::ADMIN, Permiso::GESTION_LABORATORIO]))
                <a href="{{ route('laboratorio.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                    <i class="fas fa-plus-circle"></i> Nuevo Laboratorio
                </a>
            @endif
        </div>

        <!-- Filtros y Buscador -->
        <div class="flex justify-between items-center flex-wrap gap-3 mb-4 text-sm">
            <!-- Filtros ubicación -->
            <div class="flex flex-wrap gap-2">
                <select id="filter-pais" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    <option value="">Todos los Países</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->id }}">{{ $pais->nombre_pais }}</option>
                    @endforeach
                </select>
                <select id="filter-dep" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]"
                    disabled>
                    <option value="">Seleccione Departamento</option>
                </select>
                <select id="filter-prov" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]"
                    disabled>
                    <option value="">Seleccione Provincia</option>
                </select>
                <select id="filter-mun" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]"
                    disabled>
                    <option value="">Seleccione Municipio</option>
                </select>
            </div>

            <!-- Filtros tipo, categoría, estado -->
            <div class="flex flex-wrap gap-2">
                <select id="filter-tipo" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
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
                <select id="filter-status" class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    <option value="">Todos los Estados</option>
                    @foreach ($estados as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </select>
                <select id="filter-descuento"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    <option value="">Filtrar por descuento</option>
                    @foreach ($descuento as $key => $valor)
                        <option value="{{ $key }}">{{ $valor }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buscador -->
            <div class="flex items-center gap-2 justify-end w-full sm:w-auto">
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
            <table id="labs-table" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Acciones</th>
                        <th class="px-4 py-2 text-left">Fecha/Hora</th>
                        <th class="px-4 py-2 text-left">Codigo</th>
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
            const routeToggleDescuentoTemplate = @json(route('laboratorio.toggle-descuento', ['id' => 'LAB_ID_PLACEHOLDER']));
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
                            d.municipio = $('#filter-mun').val();
                            d.tipo = $('#filter-tipo').val();
                            d.categoria = $('#filter-categoria').val();
                            // d.nivel = $('#filter-nivel').val();
                            d.filter_descuento = $('#filter-descuento').val();
                            d.status = $('#filter-status').val();
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
                            data: 'cod_lab',
                            name: 'cod_lab'
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
                        url: "{{ asset('translation/es.json') }}"
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
                        document.querySelectorAll('.toggle-descuento-btn').forEach(btn => {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                let btn = $(this);
                                let id = btn.data('id');
                                let token = $('meta[name="csrf-token"]').attr('content');
                                let url = routeToggleDescuentoTemplate.replace(
                                    'LAB_ID_PLACEHOLDER', id);
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: {
                                        _token: token
                                    },
                                    success: function(response) {
                                        if (!response.success) {
                                            showToast({
                                                icon: 'error',
                                                title: 'No se pudo actualizar'
                                            });
                                            return;
                                        }

                                        if (response.tiene_descuento) {
                                            btn.html(
                                                '<i class="fas fa-tags text-green-600"></i>'
                                            );
                                            btn.attr('data-tippy-content',
                                                'Tiene descuento');
                                        } else {
                                            btn.html(
                                                '<i class="fas fa-ban text-red-600"></i>'
                                            );
                                            btn.attr('data-tippy-content',
                                                'No tiene descuento');
                                        }

                                        if (btn[0] && btn[0]._tippy) {
                                            btn[0]._tippy.setContent(btn.attr(
                                                'data-tippy-content'));
                                        } else {
                                            tippy(btn[0]);
                                        }

                                        showToast({
                                            icon: 'success',
                                            title: response.message ||
                                                'Estado actualizado'
                                        });
                                    },
                                    error: function(xhr) {
                                        showToast({
                                            icon: 'error',
                                            title: 'Error al cambiar el estado'
                                        });
                                        console.error(xhr);
                                    }
                                });
                            });
                        });
                    }
                });

                $('#filter-tipo, #filter-categoria, #filter-descuento').on('change', () => table.draw());
                $('#filter-status').on('change', () => table.draw());
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
                    ids.forEach(i => $(`#filter-${i}`).html(`<option value="">Seleccione ${i}</option>`)
                        .prop('disabled', true));
                    table.draw();
                }
            });
        </script>
    @endpush
</x-app-layout>
