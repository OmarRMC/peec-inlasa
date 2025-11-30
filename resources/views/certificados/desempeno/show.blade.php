<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-1">
            <h1 class="text-xl font-bold text-gray-800">Lista de Inscripciones al Ensayo de Aptitud:
                {{ $ensayoA->descripcion }}</h1>
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

                <!-- Filtro por Gestión -->
                <select id="filter-gestion"
                    class="border-gray-300 rounded-md shadow-sm text-xs px-2 py-1 min-w-[160px]">
                    @foreach ($gestiones as $gestion)
                        <option value="{{ $gestion }}" {{ $gestion == now()->year ? 'selected' : '' }}>
                            {{ $gestion }}</option>
                    @endforeach
                </select>
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
                            <th class="px-4 py-2 text-left">Desempeño</th>
                            <th class="px-4 py-2 text-left">Fecha de Act.</th>
                            <th class="px-4 py-2 text-left">Codigo</th>
                            <th class="px-4 py-2 text-left">Laboratorio</th>
                            <th class="px-4 py-2 text-left">Correo</th>
                            <th class="px-4 py-2 text-left">Gestión</th>
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
                            url: "{{ route('certificado-desempeno.ajax.index', ['id' => $idEA]) }}",
                            data: function(d) {
                                d.gestion = $('#filter-gestion').val();
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
                                data: 'updated_at',
                                name: 'updated_at'
                            },
                            {
                                data: 'cod_lab',
                                name: 'inscripcion.laboratorio.cod_lab'
                            },
                            {
                                data: 'nombre_lab',
                                name: 'inscripcion.laboratorio.nombre_lab'
                            },
                            {
                                data: 'mail_lab',
                                name: 'inscripcion.laboratorio.mail_lab',
                            },
                            {
                                data: 'gestion',
                                name: 'inscripcion.gestion'
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
                            $('.update-form-desempeno').on('submit', function(e) {
                                e.preventDefault();
                                const form = $(this);
                                const url = form.attr('action');
                                const data = {
                                    _token: '{{ csrf_token() }}',
                                    calificacion_certificado: form.find(
                                        'input[name="calificacion_certificado"]').val()
                                };

                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: data,
                                    success: function(response) {
                                        table.draw();
                                        tippy.hideAll();
                                        showToast({
                                            title: 'Desempeño actualizado correctamente.'
                                        });
                                    },
                                    error: function(xhr) {
                                        showError('Error al actualizar el desempeño: ' + xhr
                                            .responseJSON.message);
                                    }
                                });
                            });
                        }
                    });
                    $('#btn-search').on('click', () => table.search($('#custom-search').val()).draw());
                    $('#filter-gestion').on('change', () => table.draw());
                    $(
                        '#custom-search').on('keypress', e => {
                        if (e.which === 13) table.search($('#custom-search').val()).draw();
                    });
                    $('#custom-length').on('change', function() {
                        table.page.len(this.value).draw();
                    });

                    $('#filter-status-inscripcion').on('change', () => table.draw());
                });
            </script>
        @endpush
</x-app-layout>
