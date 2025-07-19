<x-app-layout>
    <div class="px-4 py-6 max-w-6xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center flex-wrap gap-4 mb-6">
            <h1 class="text-xl font-bold text-gray-800">Lista de Usuarios</h1>
            <a href="{{ route('usuario.create') }}"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition shadow-md text-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Usuario
            </a>
        </div>

        <!-- Barra superior -->
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

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table id="usuarios-table" class="min-w-full divide-y divide-gray-200 text-sm text-gray-800">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Fecha hora</th>
                        <th class="px-4 py-2 text-left">Usuario</th>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Cargo</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
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
                let table = $('#usuarios-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('usuario.data') }}",
                    lengthMenu: [10, 25, 50],
                    lengthChange: false,
                    dom: 'rt',
                    columns: [{
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'nombre_completo',
                            name: 'nombre'
                        },
                        {
                            data: 'cargo',
                            name: 'cargo',
                            searchable: false
                        },
                        {
                            data: 'status_label',
                            name: 'status',
                            searchable: false
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    language: {
                        url: "/translation/es.json"
                    },
                    drawCallback: function() {
                        tippy('[data-tippy-content]');
                        confirmDelete({
                            csrfToken: '{{ csrf_token() }}',
                            table
                        });
                        setupPagination(table);
                    }
                });

                $('#btn-search').on('click', function() {
                    const value = $('#custom-search').val();
                    table.search(value).draw();
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
