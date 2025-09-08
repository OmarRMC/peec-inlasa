@php
    use App\Models\Inscripcion;
@endphp

<x-app-layout>
    <div class="px-4 max-w-7xl mx-auto">
        <!-- Encabezado -->
        <div class="flex justify-between items-center mb-1">
            <h1 class="text-xl font-bold text-gray-800">Lista de Ensayos de Aptitud</h1>
        </div>

        <div>
            <!-- Filtros -->
            {{-- <div class="flex flex-wrap gap-3 mb-4 text-sm">
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
            </div> --}}
            <!-- Tabla -->
            <div class="overflow-x-auto bg-white rounded-lg shadow">
                <table id="inscripciones-table" class="min-w-full text-sm text-gray-800">
                    <thead class="bg-gray-100">
                        <tr>
                            <th>Acciones</th>
                            <th>Area</th>
                            <th>Paquete</th>
                            <th>Ensayo A.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ensayos as $ensayo)
                            <tr>
                                <td class="px-4 py-2">
                                    <a href="{{ route('certificados.desempeno.labs.show', $ensayo->id) }}" target="_blank"
                                        class="text-blue-600 hover:underline">Ver Ponderaciones</a>
                                </td>
                                <td>{{ $ensayo->paquete->area->descripcion ?? 'N/D' }}</td>
                                <td>{{ $ensayo->paquete->descripcion ?? 'N/D' }}</td>
                                <td>{{ $ensayo->descripcion }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-muted">No hay ensayos registrados.
                                </td>
                            </tr>
                        @endforelse
                </table>
            </div>
            <div class="mt-4">
                {{ $ensayos->links() }}
            </div>
        </div>

        {{-- @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const table = $('#inscripciones-table').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: "{{ route('certificado-desempeno.ajax.index') }}",
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
                                data: 'created_at',
                                name: 'certificado.detalles.created_at'
                            },
                            {
                                data: 'cod_lab',
                                name: 'laboratorio.cod_lab'
                            },
                            {
                                data: 'nombre_lab',
                                name: 'laboratorio.nombre_lab'
                            },
                            {
                                data: 'mail_lab',
                                name: 'laboratorio.mail_lab',
                            },
                            {
                                data: 'wapp_lab',
                                name: 'laboratorio.wapp_lab'
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
        @endpush --}}
</x-app-layout>
