<x-app-layout>
    <div class="px-4 py-6 max-w-7xl mx-auto">
        {{-- Encabezado --}}
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Historial de acciones</h1>
                <p class="text-sm text-gray-500">Registro de auditoría: quién hizo qué y cuándo en el sistema.</p>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Entidad</label>
                    <select id="filtro-entidad"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas</option>
                        @foreach ($entidades as $clase => $etiqueta)
                            <option value="{{ $clase }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Acción</label>
                    <select id="filtro-evento"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas</option>
                        @foreach ($eventos as $clave => $etiqueta)
                            <option value="{{ $clave }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Desde</label>
                    <input type="date" id="filtro-fecha-desde"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Hasta</label>
                    <input type="date" id="filtro-fecha-hasta"
                        class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-end gap-2">
                    <button id="btn-filtrar"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <button id="btn-limpiar"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition text-sm">
                        Limpiar
                    </button>
                </div>
            </div>
        </div>

        {{-- Barra superior --}}
        <div class="flex justify-between items-center flex-wrap gap-4 mb-4">
            <div class="flex items-center gap-2 text-sm text-gray-700">
                <label for="custom-length">Mostrar</label>
                <select id="custom-length"
                    class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
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
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm
                               focus:ring-blue-500 focus:border-blue-500 text-sm"
                        placeholder="Buscar...">
                </div>
                <button id="btn-search"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500 transition shadow text-sm">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table id="auditoria-table" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha y hora</th>
                        <th class="px-4 py-2 text-left">Usuario</th>
                        <th class="px-4 py-2 text-left">Acción</th>
                        <th class="px-4 py-2 text-left">Entidad</th>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Cambios</th>
                        <th class="px-4 py-2 text-left">IP</th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="d-flex justify-center flex-wrap gap-1 mt-6">
            <div id="custom-info"></div>
            <div id="custom-pagination" class="flex justify-center flex-wrap gap-1"></div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let table = $('#auditoria-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.auditoria.data') }}",
                        data: function(d) {
                            d.entidad = $('#filtro-entidad').val();
                            d.evento = $('#filtro-evento').val();
                            d.fecha_desde = $('#filtro-fecha-desde').val();
                            d.fecha_hasta = $('#filtro-fecha-hasta').val();
                        }
                    },
                    lengthMenu: [10, 25, 50],
                    lengthChange: false,
                    dom: 'rt',
                    columns: [
                        { data: 'created_at', name: 'created_at' },
                        { data: 'usuario', name: 'user_id', searchable: false },
                        { data: 'evento_label', name: 'event', searchable: false },
                        { data: 'entidad', name: 'auditable_type', searchable: false },
                        { data: 'auditable_id', name: 'auditable_id' },
                        { data: 'cambios', name: 'cambios', orderable: false, searchable: false },
                        { data: 'ip_address', name: 'ip_address' },
                    ],
                    order: [[0, 'desc']],
                    language: {
                        url: "{{ asset('translation/es.json') }}"
                    },
                    drawCallback: function() {
                        setupPagination(table);
                    }
                });

                $('#btn-filtrar').on('click', () => table.draw());
                $('#btn-limpiar').on('click', function() {
                    $('#filtro-entidad').val('');
                    $('#filtro-evento').val('');
                    $('#filtro-fecha-desde').val('');
                    $('#filtro-fecha-hasta').val('');
                    table.draw();
                });

                $('#btn-search').on('click', function() {
                    table.search($('#custom-search').val()).draw();
                });
                $('#custom-search').on('keypress', function(e) {
                    if (e.which === 13) $('#btn-search').click();
                });
                $('#custom-length').on('change', function() {
                    table.page.len(this.value).draw();
                });
            });
        </script>
    @endpush
</x-app-layout>
